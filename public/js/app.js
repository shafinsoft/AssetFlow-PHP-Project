(function () {
  'use strict';
  var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  // ---- Client-side validation (server still re-checks everything) ----
  var rules = {
    loginForm: function (f) {
      if (!f.email.value.trim()) return [f.email, 'Enter your email.'];
      if (!EMAIL.test(f.email.value.trim())) return [f.email, 'Enter a valid email address.'];
      if (!f.password.value) return [f.password, 'Enter your password.'];
    },
    signupForm: function (f) {
      if (!f.name.value.trim()) return [f.name, 'Enter your name.'];
      if (!EMAIL.test(f.email.value.trim())) return [f.email, 'Enter a valid email address.'];
      if (f.password.value.length < 6) return [f.password, 'Password must be at least 6 characters.'];
      if (f.password.value !== f.confirm.value) return [f.confirm, 'Passwords do not match.'];
    },
    assetForm: function (f) {
      if (!f.asset_name.value.trim()) return [f.asset_name, 'Enter the asset name.'];
      var p = parseFloat(f.price.value);
      if (isNaN(p) || p < 0) return [f.price, 'Price must be 0 or more.'];
    },
    categoryForm: function (f) {
      if (!f.category_name.value.trim()) return [f.category_name, 'Enter the category name.'];
    }
  };

  function showError(form, msg) {
    var box = form.querySelector('.msg.err');
    if (!box) {
      box = document.createElement('div');
      box.className = 'msg err';
      box.setAttribute('role', 'alert');
      var h = form.querySelector('h2,h3');
      form.insertBefore(box, h ? h.nextSibling : form.firstChild);
    }
    box.textContent = msg;
  }

  Object.keys(rules).forEach(function (id) {
    var form = document.getElementById(id);
    if (!form) return;
    form.addEventListener('submit', function (e) {
      var r = rules[id](form);
      if (r) { e.preventDefault(); showError(form, r[1]); r[0].focus(); }
    });
  });

  // ---- Show / hide password ----
  document.querySelectorAll('input[type="password"]').forEach(function (inp) {
    var wrap = document.createElement('div');
    wrap.className = 'pw';
    inp.parentNode.insertBefore(wrap, inp);
    wrap.appendChild(inp);
    var b = document.createElement('button');
    b.type = 'button'; b.className = 'pw-t'; b.textContent = 'Show';
    b.addEventListener('click', function () {
      var hidden = inp.type === 'password';
      inp.type = hidden ? 'text' : 'password';
      b.textContent = hidden ? 'Hide' : 'Show';
    });
    wrap.appendChild(b);
  });

  // ---- Flash messages: close button, success fades away ----
  function dismiss(m) { m.classList.add('gone'); setTimeout(function () { m.remove(); }, 300); }
  document.querySelectorAll('.wrap > .msg').forEach(function (m) {
    var x = document.createElement('button');
    x.type = 'button'; x.className = 'msg-x'; x.setAttribute('aria-label', 'Close message'); x.textContent = '\u00d7';
    x.addEventListener('click', function () { dismiss(m); });
    m.appendChild(x);
    if (m.classList.contains('ok')) setTimeout(function () { dismiss(m); }, 5000);
  });

  // ---- Delete confirmation dialog ----
  var dlg, pending;
  function build() {
    dlg = document.createElement('dialog');
    dlg.id = 'confirmDialog';
    dlg.innerHTML = '<h3>Please confirm</h3><p id="confirmText"></p><div class="dlg-b">' +
      '<button type="button" class="btn gray" id="confirmNo">Cancel</button>' +
      '<button type="button" class="red" id="confirmYes">Delete</button></div>';
    document.body.appendChild(dlg);
    dlg.querySelector('#confirmNo').addEventListener('click', function () { dlg.close(); });
    dlg.querySelector('#confirmYes').addEventListener('click', function () {
      dlg.close(); if (pending) { pending.dataset.ok = '1'; pending.submit(); }
    });
  }
  document.querySelectorAll('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      if (f.dataset.ok) return;
      e.preventDefault();
      if (!window.HTMLDialogElement) { if (confirm(f.dataset.confirm)) { f.dataset.ok = '1'; f.submit(); } return; }
      if (!dlg) build();
      pending = f;
      dlg.querySelector('#confirmText').textContent = f.dataset.confirm;
      dlg.showModal();
    });
  });
})();

// ---- Motion and views ----
(function () {
  'use strict';
  var rm = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  // highlight current page in the navbar
  var here = location.pathname.split('/').pop();
  document.querySelectorAll('.nav a').forEach(function (a) {
    var h = a.getAttribute('href');
    if (h && h !== 'logout.php' && h === here) a.classList.add('on');
  });
  // dashboard numbers count up
  if (!rm) document.querySelectorAll('.stat b').forEach(function (b) {
    var t = b.textContent.trim(), n = parseFloat(t.replace(/,/g, ''));
    if (isNaN(n)) return;
    var d = (t.split('.')[1] || '').length, s = performance.now();
    (function tick(now) {
      var k = Math.min((now - s) / 800, 1), v = n * (1 - Math.pow(1 - k, 3));
      b.textContent = v.toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d });
      if (k < 1) requestAnimationFrame(tick);
    })(s);
  });
  // home cover: floating products follow the mouse a little
  var c = document.querySelector('.cover');
  if (c && !rm) c.addEventListener('mousemove', function (e) {
    var r = c.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - 0.5, y = (e.clientY - r.top) / r.height - 0.5;
    c.querySelectorAll('.fl').forEach(function (f) {
      var d = +f.dataset.d || 10;
      f.style.translate = (x * d * 2) + 'px ' + (y * d * 2) + 'px';
    });
  });
  // assets: table / cards switch (remembered)
  var v = document.getElementById('assetViews');
  if (v) {
    var bs = document.querySelectorAll('.seg button');
    var set = function (m) {
      v.dataset.view = m;
      bs.forEach(function (b) { b.classList.toggle('on', b.dataset.view === m); });
      try { localStorage.setItem('assetView', m); } catch (e) {}
    };
    bs.forEach(function (b) { b.addEventListener('click', function () { set(b.dataset.view); }); });
    var m = 'table';
    try { m = localStorage.getItem('assetView') || 'table'; } catch (e) {}
    set(m === 'cards' ? 'cards' : 'table');
  }
})();

// ---- Dark mode switch ----
(function () {
  'use strict';
  var r = document.documentElement, nav = document.querySelector('.nav > div, .top-r');
  if (!nav) return;
  var b = document.createElement('button');
  b.type = 'button'; b.className = 'theme-t'; b.id = 'themeToggle';
  b.setAttribute('aria-label', 'Switch between light and dark mode');
  var paint = function () { b.textContent = r.dataset.theme === 'dark' ? 'Light' : 'Dark'; };
  b.addEventListener('click', function () {
    var dark = r.dataset.theme === 'dark';
    if (dark) delete r.dataset.theme; else r.dataset.theme = 'dark';
    try { localStorage.setItem('theme', dark ? 'light' : 'dark'); } catch (e) {}
    paint();
  });
  paint(); nav.appendChild(b);
})();

// ---- Scroll reveal (home page) ----
(function () {
  'use strict';
  var els = document.querySelectorAll('.rv');
  if (!els.length) return;
  if (!('IntersectionObserver' in window)) { els.forEach(function (e) { e.classList.add('shown'); }); return; }
  var io = new IntersectionObserver(function (en) {
    en.forEach(function (x) { if (x.isIntersecting) { x.target.classList.add('shown'); io.unobserve(x.target); } });
  }, { threshold: 0.12 });
  els.forEach(function (e) { io.observe(e); });
})();

// ---- v3: app shell, 3D hero, dialogs, bulk actions, loading states ----
(function () {
  'use strict';
  var rm = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  // mobile sidebar
  var mb = document.getElementById('menuBtn'), sd = document.getElementById('side'), sc = document.getElementById('scrim');
  function openSide(o) { sd.classList.toggle('open', o); sc.classList.toggle('show', o); }
  if (mb && sd && sc) {
    mb.addEventListener('click', function () { openSide(!sd.classList.contains('open')); });
    sc.addEventListener('click', function () { openSide(false); });
  }
  // 3D tilt for the hero scene
  var s3 = document.querySelector('.scene3d'), cv = document.querySelector('.cover');
  if (s3 && cv && !rm) cv.addEventListener('mousemove', function (e) {
    var r = cv.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - 0.5, y = (e.clientY - r.top) / r.height - 0.5;
    s3.style.setProperty('--ry', (-9 + x * 14) + 'deg'); s3.style.setProperty('--rx', (4 - y * 10) + 'deg');
  });
  // dashboard stat cards tilt a little under the mouse
  if (!rm) document.querySelectorAll('.stat').forEach(function (c) {
    c.addEventListener('mousemove', function (e) {
      var r = c.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - 0.5, y = (e.clientY - r.top) / r.height - 0.5;
      c.style.transform = 'perspective(700px) rotateX(' + (-y * 6) + 'deg) rotateY(' + (x * 8) + 'deg)';
    });
    c.addEventListener('mouseleave', function () { c.style.transform = ''; });
  });
  // add / edit asset dialog
  var ad = document.getElementById('assetDialog');
  if (ad) {
    var ob = document.getElementById('addAssetBtn');
    if (ob) ob.addEventListener('click', function () { ad.showModal(); });
    ad.querySelectorAll('[data-close]').forEach(function (b) { b.addEventListener('click', function () { ad.close(); }); });
    if (ad.hasAttribute('data-open')) ad.showModal();
  }
  // bulk selection
  var bf = document.getElementById('bulkForm');
  if (bf) {
    var all = document.getElementById('chkAll'), bar = document.getElementById('bulkBar'), cnt = document.getElementById('bulkCount');
    var upd = function () {
      var n = document.querySelectorAll('.rowchk:checked').length;
      bar.classList.toggle('show', n > 0); cnt.textContent = n + ' selected';
    };
    document.querySelectorAll('.rowchk').forEach(function (c) { c.addEventListener('change', upd); });
    if (all) all.addEventListener('change', function () {
      document.querySelectorAll('.rowchk').forEach(function (c) { c.checked = all.checked; }); upd();
    });
    bf.addEventListener('submit', function (e) {
      if (!document.querySelectorAll('.rowchk:checked').length) e.preventDefault();
    });
  }
  // loading spinner on submit buttons
  document.querySelectorAll('form').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      var b = f.querySelector('button[type=submit],button:not([type])');
      if (!b) return;
      setTimeout(function () { if (!e.defaultPrevented && !f.hasAttribute('data-confirm')) b.classList.add('loading'); }, 0);
    });
  });
  window.addEventListener('pageshow', function () {
    document.querySelectorAll('button.loading').forEach(function (b) { b.classList.remove('loading'); });
  });
})();
