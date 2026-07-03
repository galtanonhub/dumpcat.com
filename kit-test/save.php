<?php
/* ------------------------------------------------------------------
   save.php — the only thing the buyer's editor talks to.
   Writes content.json (text edits + uploaded images) and handles
   "revert to original". It can ONLY change data — never structure —
   so there is no way for the buyer to break the layout.

   Locked behind a password (lib/auth.php): login sets a session, every
   write/upload/revert calls require_edit_auth() first. Unauthenticated
   requests get 401 and change nothing.
   ------------------------------------------------------------------ */
require __DIR__ . '/lib/content.php';
header('Content-Type: application/json');

/* Read the JSON body once (empty for multipart image uploads). Reused below
   for login/logout/revert/patch. */
$body = json_decode(file_get_contents('php://input'), true);

/* ---- login / logout (the only unauthenticated actions) ---- */
if (is_array($body) && ($body['action'] ?? '') === 'login') {
  if (kit_edit_login($body['password'] ?? '')) {
    echo json_encode(['ok' => true, 'unlocked' => true]);
  } else {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'wrong password']);
  }
  exit;
}
if (is_array($body) && ($body['action'] ?? '') === 'logout') {
  kit_edit_logout();
  echo json_encode(['ok' => true, 'locked' => true]);
  exit;
}

/* ---- everything past here writes data — must be unlocked ---- */
require_edit_auth();

/* set a value at a dot-path inside a nested array, creating as needed */
function set_path(array &$data, $path, $value) {
  $keys = explode('.', $path);
  $node = &$data;
  while (count($keys) > 1) {
    $key = array_shift($keys);
    if (!isset($node[$key]) || !is_array($node[$key])) $node[$key] = [];
    $node = &$node[$key];
  }
  $node[$keys[0]] = $value;
}

/* get a value at a dot-path by reference, creating intermediate arrays as
   needed — same walk as set_path, but returns a reference so callers can
   mutate an array in place (add_item/remove_item below). */
function &get_path(array &$data, $path) {
  $keys = explode('.', $path);
  $node = &$data;
  foreach ($keys as $key) {
    if (!isset($node[$key]) || !is_array($node[$key])) $node[$key] = [];
    $node = &$node[$key];
  }
  return $node;
}

function write_content(array $data) {
  $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  return file_put_contents(CONTENT_LIVE, $json . "\n") !== false;
}

function fail($msg) { http_response_code(400); echo json_encode(['ok' => false, 'error' => $msg]); exit; }

/* ---- image upload (multipart) ---- */
if (!empty($_FILES['image'])) {
  $path = $_POST['path'] ?? '';
  if ($path === '') fail('missing path');
  $f = $_FILES['image'];
  if ($f['error'] !== UPLOAD_ERR_OK) fail('upload error');
  if ($f['size'] > 8 * 1024 * 1024) fail('image too large (8MB max)');

  /* getimagesize() validates it's a real image and gives us the type,
     without depending on the fileinfo extension. */
  $info = getimagesize($f['tmp_name']);
  $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
  if ($info === false || !isset($types[$info[2]])) fail('unsupported image type');

  $dir = SLICE_DIR . '/uploads';
  if (!is_dir($dir)) mkdir($dir, 0755, true);
  $name = 'img_' . bin2hex(random_bytes(6)) . '.' . $types[$info[2]];
  if (!move_uploaded_file($f['tmp_name'], "$dir/$name")) fail('could not store file');

  $url = 'uploads/' . $name;
  $data = kit_load_content();
  set_path($data, $path, $url);
  if (!write_content($data)) fail('could not write content.json');
  echo json_encode(['ok' => true, 'url' => $url]);
  exit;
}

/* ---- JSON body (text patch or revert) ---- */
if (!is_array($body)) fail('invalid request');

if (($body['action'] ?? '') === 'revert') {
  if (!file_exists(CONTENT_ORIG)) fail('no original to revert to');
  if (!copy(CONTENT_ORIG, CONTENT_LIVE)) fail('could not restore original');
  echo json_encode(['ok' => true, 'reverted' => true]);
  exit;
}

/* ---- add/remove a repeating item (services.items, reviews.items,
   areas.list, faq.items, gallery items…) — buyer changes list LENGTH,
   not just each item's text/images. Blank-item shape is derived from the
   array's own last element: object arrays (e.g. services.items) get a
   copy of the last item's keys, blanked out; plain scalar arrays (e.g.
   areas.list, a list of city strings) get an empty string. ---- */
if (($body['action'] ?? '') === 'add_item') {
  $path = $body['path'] ?? '';
  if ($path === '') fail('missing path');
  $data = kit_load_content();
  $arr = &get_path($data, $path);
  if (!is_array($arr)) $arr = [];
  if ($arr && is_array(end($arr))) {
    $blank = array_map(function ($v) { return is_array($v) ? [] : ''; }, end($arr));
  } elseif ($arr) {
    $blank = '';
  } else {
    $blank = is_array($body['blank'] ?? null) || is_string($body['blank'] ?? null) ? $body['blank'] : '';
  }
  $arr[] = $blank;
  if (!write_content($data)) fail('could not write content.json');
  echo json_encode(['ok' => true, 'count' => count($arr)]);
  exit;
}

if (($body['action'] ?? '') === 'remove_item') {
  $path = $body['path'] ?? '';
  $index = $body['index'] ?? null;
  if ($path === '' || $index === null || !ctype_digit((string)$index)) fail('missing path/index');
  $data = kit_load_content();
  $arr = &get_path($data, $path);
  $index = (int)$index;
  if (!is_array($arr) || !array_key_exists($index, $arr)) fail('invalid index');
  array_splice($arr, $index, 1);
  if (!write_content($data)) fail('could not write content.json');
  echo json_encode(['ok' => true, 'count' => count($arr)]);
  exit;
}

$patch = $body['patch'] ?? null;
if (!is_array($patch)) fail('no patch');
$data = kit_load_content();
foreach ($patch as $path => $value) {
  set_path($data, (string)$path, is_string($value) ? trim($value) : $value);
}
if (!write_content($data)) fail('could not write content.json');
echo json_encode(['ok' => true, 'saved' => count($patch)]);
