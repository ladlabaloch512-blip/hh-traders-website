/* H.H Traders - store engine (final client design). Cart, wishlist, shop, product, checkout. */
(function () {
  "use strict";
  var D = window.HH_DATA;
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var fmt = new Intl.NumberFormat("en-PK");
  function money(n) { return "Rs " + fmt.format(n); }
  function lsGet(k, fb) { try { var v = localStorage.getItem(k); return v ? JSON.parse(v) : fb; } catch (e) { return fb; } }
  function lsSet(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} }
  var cart = lsGet("hhf_cart", []);
  var wish = lsGet("hhf_wish", []);
  function byId(id) { for (var i = 0; i < D.products.length; i++) if (D.products[i].id === id) return D.products[i]; return null; }
  function catName(id) { for (var i = 0; i < D.categories.length; i++) if (D.categories[i].id === id) return D.categories[i].name; return id; }
  function esc(s) { return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;"); }
  function stars(r) {
    var full = Math.round(r), out = "";
    for (var i = 0; i < 5; i++) out += '<svg viewBox="0 0 24 24" class="st' + (i < full ? " on" : "") + '" aria-hidden="true"><path d="M12 2.6l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.5l-5.9 3.1 1.2-6.5L2.5 9.5l6.6-.9z"/></svg>';
    return '<span class="stars" aria-label="Rated ' + r + ' of 5">' + out + "</span>";
  }
  var ICON_HEART = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 1 0-7.8 7.8l1 1L12 21.2l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>';
  var ICON_EYE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';

  /* ---------- counters ---------- */
  function cartCount() { return cart.reduce(function (a, c) { return a + c.qty; }, 0); }
  function cartTotal() { return cart.reduce(function (a, c) { var p = byId(c.id); return a + (p ? p.price * c.qty : 0); }, 0); }
  function syncBadges() {
    $$("[data-cart-count]").forEach(function (el) { el.textContent = cartCount(); el.style.display = cartCount() ? "" : "none"; });
    $$("[data-wish-count]").forEach(function (el) { el.textContent = wish.length; el.style.display = wish.length ? "" : "none"; });
  }
  /* ---------- toast ---------- */
  var toastTimer = null;
  function toast(msg) {
    var t = $("#toast"); if (!t) return;
    t.textContent = msg; t.classList.add("show");
    clearTimeout(toastTimer); toastTimer = setTimeout(function () { t.classList.remove("show"); }, 2400);
  }
  /* ---------- cart ops ---------- */
  function saveCart() { lsSet("hhf_cart", cart); syncBadges(); renderDrawer(); }
  function addToCart(id, qty, flyFrom) {
    qty = qty || 1;
    var found = null;
    cart.forEach(function (c) { if (c.id === id) found = c; });
    if (found) found.qty += qty; else cart.push({ id: id, qty: qty });
    saveCart();
    var p = byId(id);
    toast("Added to cart: " + (p ? p.name : "Item"));
    if (flyFrom) fly(flyFrom, p);
    bumpCartIcon();
  }
  function bumpCartIcon() { var i = $("#cartBtn"); if (!i) return; i.classList.remove("bump"); void i.offsetWidth; i.classList.add("bump"); }
  function fly(fromEl, p) {
    if (!fromEl || !p) return;
    var target = $("#cartBtn"); if (!target) return;
    var a = fromEl.getBoundingClientRect(), b = target.getBoundingClientRect();
    var dot = document.createElement("div"); dot.className = "flyer";
    dot.style.left = (a.left + a.width / 2 - 14) + "px"; dot.style.top = (a.top + a.height / 2 - 14) + "px";
    dot.style.backgroundImage = "url(" + p.img + ")";
    document.body.appendChild(dot);
    requestAnimationFrame(function () {
      dot.style.transform = "translate(" + (b.left + b.width / 2 - (a.left + a.width / 2)) + "px," + (b.top + b.height / 2 - (a.top + a.height / 2)) + "px) scale(.12)";
      dot.style.opacity = "0.25";
    });
    setTimeout(function () { dot.remove(); }, 720);
  }
  function setQty(id, qty) {
    cart = cart.filter(function (c) { return c.id !== id || qty > 0; });
    cart.forEach(function (c) { if (c.id === id) c.qty = qty; });
    saveCart(); renderCartPage();
  }
  /* ---------- wishlist ---------- */
  function toggleWish(id, btn) {
    var i = wish.indexOf(id);
    if (i >= 0) { wish.splice(i, 1); toast("Removed from wishlist"); } else { wish.push(id); toast("Added to wishlist"); }
    lsSet("hhf_wish", wish); syncBadges();
    $$('[data-wish="' + id + '"]').forEach(function (b) { b.classList.toggle("on", wish.indexOf(id) >= 0); });
  }
  /* ---------- card templates ---------- */
  function cardHTML(p) {
    return '<article class="card rv"><a class="cimg" href="product.html?id=' + p.id + '"><img src="' + p.img + '" alt="' + esc(p.name) + '" loading="lazy"></a>' +
      '<div class="cbody"><span class="ccat">' + esc(catName(p.cat)) + '</span>' +
      '<h3><a href="product.html?id=' + p.id + '">' + esc(p.name) + "</a></h3>" +
      '<div class="crow1">' + stars(p.rating) + '<span class="rnum">' + p.rating.toFixed(1) + "</span></div>" +
      '<div class="crow2"><span class="price">' + money(p.price) + "</span>" +
      '<button class="addbtn" data-add="' + p.id + '">Add to Cart</button></div>' +
      '<div class="ctools"><button class="tool" data-wish="' + p.id + '" aria-label="Toggle wishlist">' + ICON_HEART + " Wishlist</button>" +
      '<button class="tool" data-qv="' + p.id + '">' + ICON_EYE + " Quick View</button></div></div></article>";
  }
  function featHTML(p) {
    return '<article class="fcard rv"><a class="cimg" href="product.html?id=' + p.id + '"><img src="' + p.img + '" alt="' + esc(p.name) + '" loading="lazy"></a>' +
      '<h3><a href="product.html?id=' + p.id + '">' + esc(p.name) + "</a></h3>" +
      '<button class="addbtn pill" data-add="' + p.id + '">Add to Cart</button></article>';
  }
  /* ---------- home ---------- */
  function initHome() {
    var fg = $("#featGrid");
    if (fg) fg.innerHTML = D.featured.map(function (id) { return featHTML(byId(id)); }).join("");
    var tg = $("#tileGrid");
    if (fg) observeRv(fg);
    if (tg) tg.innerHTML = D.tiles.map(function (t) {
      return '<a class="tile rv" href="shop.html?cat=' + t.cat + '"><span class="timg"><img src="' + t.img + '" alt="' + esc(t.name) + '" loading="lazy"></span><span class="tname">' + esc(t.name) + "</span></a>";
    }).join("");
    if (tg) observeRv(tg);
    initSlider();
  }
  function initSlider() {
    var root = $("#heroSlider"); if (!root) return;
    var track = $(".slides", root);
    track.innerHTML = D.heroes.map(function (h, i) {
      return '<div class="slide' + (i === 0 ? " on" : "") + '" style="background-image:url(' + h.img + ')"><div class="shade"></div>' +
        '<div class="wrap scap"><span class="kick">' + esc(h.kick) + '</span><h2><span class="l1">' + esc(h.l1) + '</span> <span class="l2">' + esc(h.l2) + '</span></h2>' +
        "<p>" + esc(h.text) + '</p><a class="shopnow" href="shop.html">' + esc(h.cta) + ' <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a></div></div>';
    }).join("");
    var dots = $(".dots", root);
    dots.innerHTML = D.heroes.map(function (_, i) { return '<button data-dot="' + i + '" aria-label="Go to slide ' + (i + 1) + '"' + (i === 0 ? ' class="on"' : "") + "></button>"; }).join("");
    var idx = 0, slides = $$(".slide", root), timer = null;
    function go(n) {
      slides[idx].classList.remove("on"); $$("[data-dot]", dots)[idx].classList.remove("on");
      idx = (n + slides.length) % slides.length;
      slides[idx].classList.add("on"); $$("[data-dot]", dots)[idx].classList.add("on");
    }
    function auto() { clearInterval(timer); timer = setInterval(function () { go(idx + 1); }, 5200); }
    $(".prev", root).addEventListener("click", function () { go(idx - 1); auto(); });
    $(".next", root).addEventListener("click", function () { go(idx + 1); auto(); });
    dots.addEventListener("click", function (e) { var b = e.target.closest("[data-dot]"); if (b) { go(+b.dataset.dot); auto(); } });
    var x0 = null;
    root.addEventListener("touchstart", function (e) { x0 = e.touches[0].clientX; }, { passive: true });
    root.addEventListener("touchend", function (e) {
      if (x0 === null) return; var dx = e.changedTouches[0].clientX - x0;
      if (Math.abs(dx) > 42) { go(idx + (dx < 0 ? 1 : -1)); auto(); } x0 = null;
    }, { passive: true });
    if (window.matchMedia && window.matchMedia("(hover:hover) and (pointer:fine)").matches) {
      root.addEventListener("mouseenter", function () { clearInterval(timer); });
      root.addEventListener("mouseleave", auto);
    }
    auto();
  }
  /* ---------- shop ---------- */
  var shopState = { cat: "all", sort: "featured", max: 20000, rating: 0, q: "" };
  function shopFiltered() {
    var list = D.products.filter(function (p) {
      if (shopState.cat !== "all" && p.cat !== shopState.cat) return false;
      if (p.price > shopState.max) return false;
      if (p.rating < shopState.rating) return false;
      if (shopState.q && p.name.toLowerCase().indexOf(shopState.q) < 0 && catName(p.cat).toLowerCase().indexOf(shopState.q) < 0) return false;
      return true;
    });
    if (shopState.sort === "price-asc") list.sort(function (a, b) { return a.price - b.price; });
    if (shopState.sort === "price-desc") list.sort(function (a, b) { return b.price - a.price; });
    if (shopState.sort === "rating") list.sort(function (a, b) { return b.rating - a.rating; });
    if (shopState.sort === "name") list.sort(function (a, b) { return a.name.localeCompare(b.name); });
    return list;
  }
  function renderShop() {
    var g = $("#shopGrid"); if (!g) return;
    var list = shopFiltered();
    g.innerHTML = list.length ? list.map(cardHTML).join("") : '<p class="empty">No products match these filters. <button class="linklike" id="resetF">Reset filters</button></p>';
    var rc = $("#resCount"); if (rc) rc.textContent = list.length + (list.length === 1 ? " product" : " products");
    var rf = $("#resetF"); if (rf) rf.addEventListener("click", function () { location.href = "shop.html"; });
    observeRv(g);
    $$("[data-wish]", g).forEach(function (b) { if (wish.indexOf(+b.dataset.wish) >= 0) b.classList.add("on"); });
  }
  function setCat(cat) {
    shopState.cat = cat;
    var lbl = $("#catBtnLabel"); if (lbl) lbl.textContent = cat === "all" ? "All Products" : catName(cat);
    $$("[data-catpick]").forEach(function (b) { b.classList.toggle("on", b.dataset.catpick === cat); });
    renderShop();
  }
  function initShop() {
    var g = $("#shopGrid"); if (!g) return;
    var params = new URLSearchParams(location.search);
    if (params.get("q")) { shopState.q = params.get("q").toLowerCase(); var qb = $("#qNote"); if (qb) { qb.style.display = ""; $("#qTerm").textContent = params.get("q"); } }
    var sort = $("#sortSel"), price = $("#priceRange"), rate = $("#rateSel");
    if (sort) sort.addEventListener("change", function () { shopState.sort = sort.value; renderShop(); });
    if (price) price.addEventListener("input", function () { shopState.max = +price.value; $("#priceVal").textContent = money(shopState.max); renderShop(); });
    if (rate) rate.addEventListener("change", function () { shopState.rating = +rate.value; renderShop(); });
    var cb = $("#catBtn"), panel = $(".catpanel");
    if (cb && panel) {
      cb.addEventListener("click", function (e) { e.stopPropagation(); var open = panel.classList.toggle("open"); cb.setAttribute("aria-expanded", open); });
      document.addEventListener("click", function (e) { if (!e.target.closest(".catdrop")) { panel.classList.remove("open"); cb.setAttribute("aria-expanded", "false"); } });
    }
    document.addEventListener("click", function (e) {
      var b = e.target.closest("[data-catpick]"); if (!b) return;
      setCat(b.dataset.catpick);
      if (panel) panel.classList.remove("open");
    });
    setCat(params.get("cat") && catName(params.get("cat")) !== params.get("cat") ? params.get("cat") : (params.get("cat") || "all"));
  }
  /* ---------- product page ---------- */
  function initProduct() {
    if (!$("#pdName")) return;
    var id = +new URLSearchParams(location.search).get("id");
    var p = byId(id) || D.products[0];
    document.title = p.name + " - H.H Traders";
    $("#pdImg").src = p.img; $("#pdImg").alt = p.name;
    $("#pdCat").textContent = catName(p.cat);
    var pc2 = $("#pdCat2"); if (pc2) pc2.textContent = catName(p.cat);
    $("#pdName").textContent = p.name;
    $("#pdStars").innerHTML = stars(p.rating) + '<span class="rnum">' + p.rating.toFixed(1) + " rating</span>";
    $("#pdPrice").textContent = money(p.price);
    $("#pdBlurb").textContent = p.blurb;
    $("#pdSku").textContent = "HH-" + String(100 + p.id);
    var qty = 1;
    $("#qMinus").addEventListener("click", function () { qty = Math.max(1, qty - 1); $("#qVal").textContent = qty; });
    $("#qPlus").addEventListener("click", function () { qty = Math.min(99, qty + 1); $("#qVal").textContent = qty; });
    $("#pdAdd").addEventListener("click", function () { addToCart(p.id, qty, $("#pdImg")); });
    $("#pdWish").addEventListener("click", function () { toggleWish(p.id, this); });
    if (wish.indexOf(p.id) >= 0) $("#pdWish").classList.add("on");
    $("#pdWa").href = D.store.whatsapp + "?text=" + encodeURIComponent("Assalam o Alaikum, I want to order: " + p.name + " (" + money(p.price) + ")");
    var rel = D.products.filter(function (x) { return x.cat === p.cat && x.id !== p.id; }).slice(0, 4);
    $("#relGrid").innerHTML = rel.map(cardHTML).join("");
    observeRv($("#relGrid"));
  }
  /* ---------- cart page ---------- */
  function renderCartPage() {
    var root = $("#cartRoot"); if (!root) return;
    if (!cart.length) {
      root.innerHTML = '<div class="emptybox"><h2>Your cart is empty</h2><p>Add products from the shop to see them here.</p><a class="shopnow" href="shop.html">Continue Shopping</a></div>';
      return;
    }
    var rows = cart.map(function (c) {
      var p = byId(c.id); if (!p) return "";
      return '<div class="citem"><a href="product.html?id=' + p.id + '"><img src="' + p.img + '" alt="' + esc(p.name) + '"></a>' +
        '<div class="ci-info"><h3><a href="product.html?id=' + p.id + '">' + esc(p.name) + '</a></h3><span class="ccat">' + esc(catName(p.cat)) + '</span><span class="price">' + money(p.price) + '</span></div>' +
        '<div class="qty"><button data-dec="' + p.id + '" aria-label="Decrease quantity">-</button><span>' + c.qty + '</span><button data-inc="' + p.id + '" aria-label="Increase quantity">+</button></div>' +
        '<span class="cline">' + money(p.price * c.qty) + '</span><button class="rm" data-rm="' + p.id + '" aria-label="Remove item">&times;</button></div>';
    }).join("");
    root.innerHTML = '<div class="cartgrid"><div>' + rows + '</div><aside class="summary"><h3>Order Summary</h3>' +
      '<div class="srow"><span>Subtotal</span><b>' + money(cartTotal()) + '</b></div>' +
      '<div class="srow"><span>Delivery</span><b>Calculated at checkout</b></div>' +
      '<div class="srow tot"><span>Total</span><b>' + money(cartTotal()) + '</b></div>' +
      '<a class="shopnow wide" href="checkout.html">Proceed to Checkout</a>' +
      '<a class="ghost wide" href="shop.html">Continue Shopping</a></aside></div>';
  }
  function initCartPage() { renderCartPage(); }
  /* ---------- checkout ---------- */
  function initCheckout() {
    var form = $("#coForm"); if (!form) return;
    var items = $("#coItems");
    if (items) {
      items.innerHTML = cart.map(function (c) { var p = byId(c.id); return p ? '<div class="srow"><span>' + esc(p.name) + " x " + c.qty + "</span><b>" + money(p.price * c.qty) + "</b></div>" : ""; }).join("") +
        '<div class="srow tot"><span>Total</span><b>' + money(cartTotal()) + "</b></div>";
    }
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var ok = true;
      $$("[required]", form).forEach(function (f) {
        var bad = !f.value.trim() || (f.type === "tel" && f.value.replace(/\D/g, "").length < 10);
        f.classList.toggle("bad", bad); if (bad) ok = false;
      });
      if (!ok) { toast("Please fill the highlighted fields correctly"); return; }
      if (!cart.length) { toast("Your cart is empty"); return; }
      var btn = $("#coBtn"); btn.disabled = true; btn.innerHTML = '<span class="spin"></span> Placing order...';
      var order = {
        no: "HH-2026-" + Math.floor(1000 + Math.random() * 9000),
        name: $("#coName").value.trim(), phone: $("#coPhone").value.trim(),
        city: $("#coCity").value.trim(), address: $("#coAddr").value.trim(),
        total: cartTotal(), count: cartCount(),
        items: cart.map(function (c) { var p = byId(c.id); return { name: p ? p.name : "", qty: c.qty, price: p ? p.price : 0 }; })
      };
      setTimeout(function () {
        lsSet("hhf_order", order);
        cart = []; lsSet("hhf_cart", cart); syncBadges();
        location.href = "confirmation.html";
      }, 1100);
    });
    $$("[required]", form).forEach(function (f) { f.addEventListener("input", function () { f.classList.remove("bad"); }); });
  }
  function initConfirmation() {
    var root = $("#confRoot"); if (!root) return;
    var o = lsGet("hhf_order", null);
    if (!o) { $("#confMsg").textContent = "No recent order found."; return; }
    $("#ordNo").textContent = o.no;
    $("#confMsg").textContent = "Thank you, " + o.name + ". Your order of " + o.count + " item(s) totalling " + money(o.total) + " is confirmed. We will call you on " + o.phone + " before dispatch.";
    var list = $("#confItems");
    if (list) list.innerHTML = o.items.map(function (i) { return '<div class="srow"><span>' + esc(i.name) + " x " + i.qty + "</span><b>" + money(i.price * i.qty) + "</b></div>"; }).join("");
    var check = $(".checkmark"); if (check) check.classList.add("draw");
  }
  /* ---------- drawer / quick view / search / menus ---------- */
  function renderDrawer() {
    var body = $("#drawerBody"); if (!body) return;
    if (!cart.length) { body.innerHTML = '<p class="empty">Your cart is empty.</p>'; }
    else body.innerHTML = cart.map(function (c) {
      var p = byId(c.id); if (!p) return "";
      return '<div class="ditem"><img src="' + p.img + '" alt=""><div><h4>' + esc(p.name) + '</h4><span>' + c.qty + " x " + money(p.price) + '</span></div><button class="rm" data-rm="' + p.id + '" aria-label="Remove item">&times;</button></div>';
    }).join("") + '<div class="srow tot"><span>Total</span><b>' + money(cartTotal()) + '</b></div><a class="shopnow wide" href="cart.html">View Cart</a><a class="ghost wide" href="checkout.html">Checkout</a>';
  }
  function openDrawer() { $("#drawerRoot").classList.add("open"); document.body.classList.add("lock"); renderDrawer(); }
  function closeAll() {
    $$(".open").forEach(function (el) { if (el.id === "drawerRoot" || el.id === "menuRoot" || el.id === "qvRoot") el.classList.remove("open"); });
    document.body.classList.remove("lock");
  }
  function initChrome() {
    var cb = $("#cartBtn"); if (cb) cb.addEventListener("click", openDrawer);
    $$("[data-close]").forEach(function (b) { b.addEventListener("click", closeAll); });
    var mb = $("#menuBtn"); if (mb) mb.addEventListener("click", function () { $("#menuRoot").classList.add("open"); document.body.classList.add("lock"); });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") closeAll(); });
    /* All Categories dropdown (desktop nav) */
    var ac = $("#allCatsBtn"), ap = $("#allCatsPanel");
    if (ac && ap) {
      ac.addEventListener("click", function (e) { e.stopPropagation(); ap.classList.toggle("open"); });
      document.addEventListener("click", function (e) { if (!e.target.closest(".allcats")) ap.classList.remove("open"); });
    }
    /* search suggestions */
    var si = $("#searchInput"), sg = $("#suggest");
    if (si && sg) {
      si.addEventListener("input", function () {
        var q = si.value.trim().toLowerCase();
        if (q.length < 2) { sg.classList.remove("open"); sg.innerHTML = ""; return; }
        var hits = D.products.filter(function (p) { return p.name.toLowerCase().indexOf(q) >= 0 || catName(p.cat).toLowerCase().indexOf(q) >= 0; }).slice(0, 6);
        sg.innerHTML = hits.length ? hits.map(function (p) { return '<a href="product.html?id=' + p.id + '"><img src="' + p.img + '" alt=""><span>' + esc(p.name) + "</span><b>" + money(p.price) + "</b></a>"; }).join("") : '<span class="sgnone">No matches. Press Enter to search the shop.</span>';
        sg.classList.add("open");
      });
      si.addEventListener("keydown", function (e) { if (e.key === "Enter") { e.preventDefault(); location.href = "shop.html?q=" + encodeURIComponent(si.value.trim()); } });
      document.addEventListener("click", function (e) { if (!e.target.closest(".searchbox")) sg.classList.remove("open"); });
      var sb = $("#searchBtn"); if (sb) sb.addEventListener("click", function () { location.href = "shop.html?q=" + encodeURIComponent(si.value.trim()); });
    }
    /* reveal */
    observeRv(document);
  }
  var rvIO = null;
  function observeRv(scope) {
    var els = $$(".rv:not(.in)", scope || document);
    if (!("IntersectionObserver" in window)) { els.forEach(function (el) { el.classList.add("in"); }); return; }
    if (!rvIO) {
      rvIO = new IntersectionObserver(function (es) { es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add("in"); rvIO.unobserve(en.target); } }); }, { threshold: 0.08, rootMargin: "0px 0px -8% 0px" });
      setTimeout(function () { $$(".rv:not(.in)").forEach(function (el) { el.classList.add("in"); }); }, 1800);
    }
    els.forEach(function (el) { rvIO.observe(el); });
  }
  /* ---------- global clicks (bound once) ---------- */
  var bound = false;
  function bindGlobal() {
    if (bound) return; bound = true;
    document.addEventListener("click", function (e) {
      var add = e.target.closest("[data-add]");
      if (add) { var card = add.closest("article"); addToCart(+add.dataset.add, 1, card ? $("img", card) : add); return; }
      var w = e.target.closest("[data-wish]");
      if (w) { toggleWish(+w.dataset.wish, w); return; }
      var qv = e.target.closest("[data-qv]");
      if (qv) { openQV(+qv.dataset.qv); return; }
      var rm = e.target.closest("[data-rm]");
      if (rm) { setQty(+rm.dataset.rm, 0); renderDrawer(); return; }
      var inc = e.target.closest("[data-inc]");
      if (inc) { var c1 = cart.find(function (c) { return c.id === +inc.dataset.inc; }); if (c1) setQty(c1.id, c1.qty + 1); return; }
      var dec = e.target.closest("[data-dec]");
      if (dec) { var c2 = cart.find(function (c) { return c.id === +dec.dataset.dec; }); if (c2) setQty(c2.id, c2.qty - 1); return; }
    });
  }
  function openQV(id) {
    var p = byId(id); if (!p) return;
    var root = $("#qvRoot"); if (!root) return;
    $("#qvImg").src = p.img; $("#qvName").textContent = p.name;
    $("#qvCat").textContent = catName(p.cat);
    $("#qvPrice").textContent = money(p.price);
    $("#qvBlurb").textContent = p.blurb;
    $("#qvStars").innerHTML = stars(p.rating);
    $("#qvAdd").onclick = function () { addToCart(p.id, 1, $("#qvImg")); closeAll(); openDrawer(); };
    $("#qvView").href = "product.html?id=" + p.id;
    root.classList.add("open"); document.body.classList.add("lock");
    var f = $("#qvAdd"); if (f) f.focus();
  }
  /* ---------- boot ---------- */
  document.addEventListener("DOMContentLoaded", function () {
    bindGlobal(); initChrome(); syncBadges(); renderDrawer();
    [initHome, initShop, initProduct, initCartPage, initCheckout, initConfirmation].forEach(function (fn) { try { fn(); } catch (e) { if (window.console) console.error(fn.name, e); } });
    $$("[data-wish]").forEach(function (b) { if (wish.indexOf(+b.dataset.wish) >= 0) b.classList.add("on"); });
  });
})();
