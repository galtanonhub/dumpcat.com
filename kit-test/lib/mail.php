<?php
/* ------------------------------------------------------------------
   mail.php — pluggable contact-form delivery.

   Every buyer's hosting/mail situation is different, so delivery method
   is config, not code: kit_send_mail() reads lib/mail-config.php (generated
   per-site at stamp time — see new-template.js, same "gitignored secret
   file, always present, falls back gracefully if absent" pattern as
   lib/auth-secret.php) and dispatches to either PHP's built-in mail()
   (zero setup, the default) or a hand-rolled SMTP client (better
   deliverability, needs the buyer's real mailbox credentials). Switching
   methods is a one-file edit — lib/mail-config.php — never a code change.
   ------------------------------------------------------------------ */

/* per-site config if delivered, else the zero-setup default */
function kit_mail_config() {
  $file = __DIR__ . '/mail-config.php';
  if (is_file($file)) {
    $cfg = require $file;
    if (is_array($cfg)) return $cfg;
  }
  return ['method' => 'phpmail', 'to' => null, 'smtp' => []];
}

/* Strip header-injection characters (CR/LF) from anything that ends up in
   an email header. Every value that reaches a header — including
   server-controlled ones like business.name — goes through this; never
   skip it for user-submitted values. */
function kit_mail_header_safe($s) {
  return str_replace(["\r", "\n"], '', (string) $s);
}

function kit_build_headers($fromName, $fromAddr, $replyEmail, $replyName) {
  $headers   = [];
  $headers[] = 'From: ' . kit_mail_header_safe($fromName) . ' <' . kit_mail_header_safe($fromAddr) . '>';
  if ($replyEmail !== '') {
    $headers[] = 'Reply-To: ' . kit_mail_header_safe($replyName) . ' <' . kit_mail_header_safe($replyEmail) . '>';
  }
  $headers[] = 'MIME-Version: 1.0';
  $headers[] = 'Content-Type: text/plain; charset=UTF-8';
  return $headers;
}

/* $replyToEmail/$replyToName come from the contact form — the "best way to
   reach you" field is free text (phone OR email), so it's only used as a
   real Reply-To when it actually validates as an email address; otherwise
   it's left out of headers entirely and the caller already put it in the
   message body instead. */
function kit_send_mail($subject, $body, $replyToEmail = '', $replyToName = '') {
  $cfg = kit_mail_config();
  $to  = $cfg['to'] ?: c('business.email', '');
  if ($to === '') return false;

  $fromName = c('business.name', 'Website');
  $smtpFrom = $cfg['smtp']['from'] ?? '';
  $fromAddr = $smtpFrom !== '' ? $smtpFrom : $to;

  $replyValid = $replyToEmail !== '' && filter_var($replyToEmail, FILTER_VALIDATE_EMAIL);
  $replyEmail = $replyValid ? $replyToEmail : '';

  if (($cfg['method'] ?? 'phpmail') === 'smtp' && !empty($cfg['smtp']['host'])) {
    return kit_smtp_send($cfg['smtp'], $to, $subject, $body, $fromName, $fromAddr, $replyEmail, $replyToName);
  }
  return kit_phpmail_send($to, $subject, $body, $fromName, $fromAddr, $replyEmail, $replyToName);
}

function kit_phpmail_send($to, $subject, $body, $fromName, $fromAddr, $replyEmail, $replyName) {
  $headers = kit_build_headers($fromName, $fromAddr, $replyEmail, $replyName);
  return @mail($to, kit_mail_header_safe($subject), $body, implode("\r\n", $headers));
}

/* ------------------------------------------------------------------
   Minimal dependency-free SMTP client — just enough to AUTH LOGIN and
   send a plain-text message over STARTTLS (587) or implicit TLS (465),
   which covers Gmail/Office365/most host mailboxes with an app password.
   No external library: the kit ships with zero dependencies, and vendoring
   something like PHPMailer across every stamped site is a lot of surface
   for what's a fairly bounded piece of protocol. If a buyer's provider
   needs OAuth2 (e.g. Gmail without an app password), swap in PHPMailer for
   that case — this won't cover it.
   ------------------------------------------------------------------ */
function kit_smtp_send($cfg, $to, $subject, $body, $fromName, $fromAddr, $replyEmail, $replyName) {
  $host   = $cfg['host'] ?? '';
  $port   = (int) ($cfg['port'] ?? 587);
  $secure = $cfg['secure'] ?? 'tls';
  $user   = $cfg['user'] ?? '';
  $pass   = $cfg['pass'] ?? '';
  if ($host === '') return false;

  $target = ($secure === 'ssl' ? 'ssl://' : '') . $host;
  $errno = 0; $errstr = '';
  $fp = @stream_socket_client($target . ':' . $port, $errno, $errstr, 15);
  if (!$fp) return false;
  stream_set_timeout($fp, 15);

  /* @ on fgets/fwrite: a dropped/reset connection mid-protocol raises a PHP
     warning, and letting that print would flush output before form.php's
     header() redirect runs — same failure shape as the stream_socket_enable_crypto
     case above. Every fallible call in this function is deliberately @-quieted
     for that reason; failures still surface correctly via the $ok() checks. */
  $read = function () use ($fp) {
    $data = '';
    while (($line = @fgets($fp, 515)) !== false) {
      $data .= $line;
      if (strlen($line) < 4 || $line[3] === ' ') break;   // last line of a (possibly multi-line) reply
    }
    return $data;
  };
  $code  = function ($resp) { return (int) substr($resp, 0, 3); };
  $ok    = function ($resp, $expected) use ($code) { return in_array($code($resp), (array) $expected, true); };
  $write = function ($cmd) use ($fp) { @fwrite($fp, $cmd . "\r\n"); };
  $ehloName = $_SERVER['SERVER_NAME'] ?? 'localhost';

  if (!$ok($read(), 220)) { fclose($fp); return false; }

  $write('EHLO ' . $ehloName);
  if (!$ok($read(), 250)) { fclose($fp); return false; }

  if ($secure === 'tls') {
    $write('STARTTLS');
    if (!$ok($read(), 220)) { fclose($fp); return false; }
    if (!@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) { fclose($fp); return false; }
    $write('EHLO ' . $ehloName);
    if (!$ok($read(), 250)) { fclose($fp); return false; }
  }

  if ($user !== '') {
    $write('AUTH LOGIN');
    if (!$ok($read(), 334)) { fclose($fp); return false; }
    $write(base64_encode($user));
    if (!$ok($read(), 334)) { fclose($fp); return false; }
    $write(base64_encode($pass));
    if (!$ok($read(), 235)) { fclose($fp); return false; }
  }

  $write('MAIL FROM:<' . $fromAddr . '>');
  if (!$ok($read(), 250)) { fclose($fp); return false; }
  $write('RCPT TO:<' . $to . '>');
  if (!$ok($read(), [250, 251])) { fclose($fp); return false; }
  $write('DATA');
  if (!$ok($read(), 354)) { fclose($fp); return false; }

  $headers   = kit_build_headers($fromName, $fromAddr, $replyEmail, $replyName);
  $headers[] = 'Subject: ' . kit_mail_header_safe($subject);
  $headers[] = 'To: <' . $to . '>';
  $bodyStuffed = preg_replace('/^\./m', '..', $body);   // dot-stuffing per RFC 5321
  $write(implode("\r\n", $headers) . "\r\n\r\n" . $bodyStuffed . "\r\n.");
  if (!$ok($read(), 250)) { fclose($fp); return false; }

  $write('QUIT');
  fclose($fp);
  return true;
}
