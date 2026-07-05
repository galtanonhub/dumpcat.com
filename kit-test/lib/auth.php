<?php
/* ------------------------------------------------------------------
   auth.php — the edit-mode gate.

   Editing (and every write to save.php) is locked behind a password.
   The password HASH lives in lib/auth-secret.php, which the stamper
   writes per delivered site (a unique random password printed at stamp
   time). That file is NOT committed and NOT in the public repo — only
   the hash ever touches disk, never the plaintext.

   If no secret file is present we fall back to a dev-only password
   ("edit") so the local _slice proof is testable. A stamped site always
   ships its own secret, so the dev password never reaches a buyer.
   ------------------------------------------------------------------ */

if (session_status() === PHP_SESSION_NONE) {
  /* lock the cookie down — editing is the only thing that needs it */
  session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
  session_start();
}

/* per-site hash if delivered, else the dev-only default */
if (is_file(__DIR__ . '/auth-secret.php')) {
  require __DIR__ . '/auth-secret.php';
}
if (!defined('KIT_EDIT_HASH')) {
  /* dev-only — password is "edit". Stamps always override this. */
  define('KIT_EDIT_HASH', '$2y$12$n.CP3751YbuBuNBMHCYr9ediP4yMjUPyTWoIPjV.cyrBk6X8USxAC');
}

/* is the current session allowed to edit? */
function edit_unlocked() { return !empty($_SESSION['kit_edit']); }

/* Per-IP login throttle — bcrypt alone only slows a guess down to
   milliseconds; without this, an attacker can still try thousands of
   passwords an hour against a public save.php. Same small file-based
   pattern as mail.php's kit_rate_limited() (no database on typical shared
   hosting): after 5 failures from one IP, lock it out for 5 minutes.

   Every read AND write goes through kit_login_attempts_locked() below,
   which holds an exclusive flock() for the whole read-modify-write —
   without it, concurrent requests (e.g. a distributed brute-force firing
   several logins at once) each read the same pre-increment count and
   clobber each other's write, silently losing failed-attempt increments
   and letting the lockout threshold never actually trip. */
function kit_login_attempts_file() { return __DIR__ . '/.login-attempts.json'; }

/* $mutator, if given, receives the current data and must return the data
   to persist; omit it for a lock-guarded read with no write. */
function kit_login_attempts_locked(?callable $mutator = null) {
  $fp = fopen(kit_login_attempts_file(), 'c+');
  if (!$fp) return [];
  flock($fp, LOCK_EX);
  $raw  = stream_get_contents($fp);
  $data = $raw !== '' ? (json_decode($raw, true) ?: []) : [];
  if (!is_array($data)) $data = [];
  if ($mutator) {
    $data = $mutator($data);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($data));
    fflush($fp);
  }
  flock($fp, LOCK_UN);
  fclose($fp);
  return $data;
}
function kit_login_locked($key) {
  $rec = kit_login_attempts_locked()[$key] ?? null;
  return $rec && $rec['count'] >= 5 && (time() - $rec['last']) < 300;
}
function kit_login_attempt_failed($key) {
  kit_login_attempts_locked(function ($data) use ($key) {
    $now = time();
    if (!isset($data[$key]) || ($now - $data[$key]['last']) >= 300) $data[$key] = ['count' => 0, 'last' => $now];
    $data[$key]['count']++;
    $data[$key]['last'] = $now;
    return array_filter($data, fn($r) => $now - $r['last'] < 3600); // prune stale IPs
  });
}
function kit_login_attempt_reset($key) {
  kit_login_attempts_locked(function ($data) use ($key) {
    unset($data[$key]);
    return $data;
  });
}

/* verify a password attempt and unlock the session on success */
function kit_edit_login($password) {
  if (!is_string($password) || $password === '') return false;
  if (!password_verify($password, KIT_EDIT_HASH)) return false;
  session_regenerate_id(true);   /* fresh id once authenticated */
  $_SESSION['kit_edit'] = true;
  return true;
}

function kit_edit_logout() { unset($_SESSION['kit_edit']); }

/* guard for save.php — refuse any write unless unlocked */
function require_edit_auth() {
  if (edit_unlocked()) return;
  http_response_code(401);
  echo json_encode(['ok' => false, 'error' => 'not authorized']);
  exit;
}
