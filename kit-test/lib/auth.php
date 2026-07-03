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
