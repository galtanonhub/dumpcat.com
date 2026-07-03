/* ------------------------------------------------------------------
   editor.js — the buyer's in-page editor (loads only in ?edit=1 mode).
   Lets them change TEXT and IMAGES. It can't touch structure: it only
   reads the data-edit / data-edit-img paths the template declared, and
   sends those back to save.php. Layout is untouchable.
   ------------------------------------------------------------------ */
(function () {
  var status = document.getElementById('editor-status');
  var fileInput = document.getElementById('editor-file');
  var dirty = false;
  var pendingImg = null; // { el, path }

  function setStatus(msg, kind) {
    status.textContent = msg || '';
    status.className = 'editor-bar__status' + (kind ? ' is-' + kind : '');
  }
  function markDirty() { dirty = true; setStatus('Unsaved changes', 'dirty'); }

  /* --- text: make every declared field editable --- */
  var fields = Array.prototype.slice.call(document.querySelectorAll('[data-edit]'));
  fields.forEach(function (el) {
    el.setAttribute('contenteditable', 'true');
    el.addEventListener('input', markDirty);
    /* keep edits plain-text: block rich paste */
    el.addEventListener('paste', function (e) {
      e.preventDefault();
      var text = (e.clipboardData || window.clipboardData).getData('text');
      document.execCommand('insertText', false, text);
    });
  });

  /* --- form fields: <input>/<textarea> with data-edit-field (e.g. social URLs).
     Unlike data-edit (contenteditable text), these are real form controls whose
     .value we send on save. Same data-only contract — buyer edits values, not
     structure. --- */
  var inputFields = Array.prototype.slice.call(document.querySelectorAll('[data-edit-field]'));
  inputFields.forEach(function (el) {
    el.addEventListener('input', markDirty);
  });

  /* --- images: click to replace --- */
  document.querySelectorAll('[data-edit-img]').forEach(function (img) {
    img.addEventListener('click', function (e) {
      e.preventDefault();
      pendingImg = { el: img, path: img.getAttribute('data-edit-img') };
      fileInput.click();
    });
  });

  fileInput.addEventListener('change', function () {
    if (!fileInput.files.length || !pendingImg) return;
    var fd = new FormData();
    fd.append('image', fileInput.files[0]);
    fd.append('path', pendingImg.path);
    setStatus('Uploading image…');
    fetch('save.php', { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (!res.ok) throw new Error(res.error || 'upload failed');
        pendingImg.el.src = res.url + '?t=' + Date.now();
        setStatus('Image updated', 'ok');
      })
      .catch(function (err) { setStatus(err.message, 'error'); })
      .finally(function () { fileInput.value = ''; pendingImg = null; });
  });

  /* --- save all text edits --- */
  document.getElementById('editor-save').addEventListener('click', function () {
    var patch = {};
    /* textContent (not innerText) so CSS text-transform — e.g. uppercase eyebrows —
       isn't baked into the saved data. innerText returns the rendered/transformed text. */
    fields.forEach(function (el) { patch[el.getAttribute('data-edit')] = el.textContent; });
    inputFields.forEach(function (el) { patch[el.getAttribute('data-edit-field')] = el.value.trim(); });
    setStatus('Saving…');
    fetch('save.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ patch: patch })
    })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (!res.ok) throw new Error(res.error || 'save failed');
        dirty = false;
        setStatus('Saved ✓', 'ok');
      })
      .catch(function (err) { setStatus(err.message, 'error'); });
  });

  /* --- revert to original --- */
  document.getElementById('editor-revert').addEventListener('click', function () {
    if (!confirm('Reset ALL content back to the original delivered version? This cannot be undone.')) return;
    setStatus('Reverting…');
    fetch('save.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'revert' })
    })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (!res.ok) throw new Error(res.error || 'revert failed');
        dirty = false;
        location.reload();
      })
      .catch(function (err) { setStatus(err.message, 'error'); });
  });

  /* --- list controls: add/remove a repeating item (services, reviews,
     areas, faq, gallery…). Both are an immediate server round-trip + full
     page reload — the server holds the authoritative array and PHP
     re-renders correct, freshly-reindexed data-edit paths, so there's no
     client-side renumbering to get wrong. Reload means any UNSAVED edits
     elsewhere on the page are lost, hence the confirm. --- */
  document.querySelectorAll('[data-list-remove]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (!confirm('Remove this item? Unsaved edits elsewhere on this page will be lost.')) return;
      setStatus('Removing…');
      fetch('save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'remove_item', path: btn.getAttribute('data-list-remove'), index: parseInt(btn.getAttribute('data-list-index'), 10) })
      })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res.ok) throw new Error(res.error || 'remove failed');
          location.reload();
        })
        .catch(function (err) { setStatus(err.message, 'error'); });
    });
  });

  document.querySelectorAll('[data-list-add]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (dirty && !confirm('Add a new item? Unsaved edits elsewhere on this page will be lost.')) return;
      setStatus('Adding…');
      fetch('save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'add_item', path: btn.getAttribute('data-list-add') })
      })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res.ok) throw new Error(res.error || 'add failed');
          location.reload();
        })
        .catch(function (err) { setStatus(err.message, 'error'); });
    });
  });

  /* warn before leaving with unsaved edits */
  window.addEventListener('beforeunload', function (e) {
    if (dirty) { e.preventDefault(); e.returnValue = ''; }
  });
})();
