<?php
require __DIR__ . '/lib/content.php';
require __DIR__ . '/lib/mail.php';

/* Contact form handler. Redirects back to contact.php?sent=1 on success,
   or back to the bare contact page (form re-shown) if validation fails —
   a direct POST bypasses the form's `required` attributes entirely, so
   server-side validation is the only real guard.

   Honeypot: hp_website (see the .contact-form partials) is a field real
   visitors never see or fill; a bot filling every input trips it. We still
   redirect to the normal "sent" success page either way — never reveal to
   the bot that it was caught, just silently drop the submission instead of
   sending it.

   TODO before delivery: CSRF token, rate-limiting. */

$isSpam  = !empty($_POST['hp_website']);
$name    = trim((string) ($_POST['name'] ?? ''));
$contact = trim((string) ($_POST['contact'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

/* strlen (byte length), not mb_strlen — mbstring isn't guaranteed to be
   enabled on every buyer's hosting, and a byte-length upper bound is fine
   for a sanity/spam guard that never displays or truncates the text. */
$valid = $name !== '' && $contact !== '' && $message !== ''
      && strlen($name) <= 120 && strlen($contact) <= 120 && strlen($message) <= 4000;

$params = edit_mode() ? ['edit' => '1'] : [];

if (!$valid) {
  header('Location: contact.php' . ($params ? '?' . http_build_query($params) : ''));
  exit;
}

if (!$isSpam) {
  $subject = 'New website inquiry from ' . $name;
  $body    = "Name: $name\nContact: $contact\n\nMessage:\n$message\n";
  kit_send_mail($subject, $body, $contact, $name);
}

$params['sent'] = '1';
header('Location: contact.php?' . http_build_query($params));
exit;
