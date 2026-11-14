"use strict";
/* H.H Traders - storefront engine. Product data + settings come from window.HH
   (rendered by PHP from data.json, managed in the admin panel). */
(function () {
  var HH = window.HH || { wa: "", cta_mode: "shop", products: [] };
  var P = {}; HH.products.forEach(function (p) { P[p.id] = p; });
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var money = function (n) { return "Rs " + Number(n).toLocaleString("en-US"); };
  var store = {
    get: function (k, dft) { try { var v = localStorage.getItem(k); return v ? JSON.parse(v) : dft; } catch (e) { return dft; } },
    set: function (k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} }
  };
  var cart = store.get("hhp_cart", []);
  var wish = store.get("hhp_wish", []);
  function saveCart() { store.set("hhp_cart", cart); }
  function saveWish() { store.set("hhp_wish", wish); }
  function cartCount() { return cart.reduce(function (a, c) { return a + c.qty; }, 0); }
  function cartTotal() { return cart.reduce(function (a, c) { var p = P[c.id]; return a + (p ? p.price * c.qty : 0); }, 0); }
  function waLink(text) { return "https://wa.me/" + HH.wa + "?text=" + encodeURIComponent(text); }

  var toastT = null;
  function toast(msg) {
    var t = $("#toast"); if (!t) return;
    t.textContent = msg; t.classList.add("show");
    clearTimeout(toastT); toastT = setTimeout(function () { t.classList.remove("show"); }, 2400);
  }

  function syncBadges() {
    $$("[data-cart-count]").forEach(function (b) { b.textContent = cartCount(); b.style.display = cartCount() ? "flex" : "none"; });
    $$("[data-wish-count]").forEach(function (b) { b.textContent = wish.length; b.style.display = wish.length ? "flex" : "none"; });
    $$("[data-wish]").forEach(function (b) { b.classList.toggle("on", wish.indexOf(+b.getAttribute("data-wish")) > -1); });
  }

  // ---------- overlays ----------
  function openOv(id) { var o = $(id); if (o) { o.classList.add("open"); document.body.style.overflow = "hidden"; } }
  function closeOv(el) { var o = el.closest(".overlay"); if (o) { o.classList.remove("open"); document.body.style.overflow = ""; } }
  function closeAll() { $$(".overlay.open").forEach(function (o) { o.classList.remove("open"); }); document.body.style.overflow = ""; }

  // ---------- cart ----------
  function fly(fromEl) {
    if (!fromEl) return;
    var card = fromEl.closest(".card, .pdgrid, .qvbox");
    var img = card ? card.querySelector("img") : null;
    var target = $("#cartBtn") || $("#mbCart");
    if (!img || !target) return;
    var r1 = img.getBoundingClientRect(), r2 = target.getBoundingClientRect();
    var ghost = img.cloneNode();
    ghost.style.cssText = "position:fixed;z-index:200;pointer-events:none;border-radius:10px;left:" + r1.left + "px;top:" + r1.top + "px;width:" + r1.width + "px;height:" + r1.height + "px;object-fit:cover;transition:all .7s cubic-bezier(.5,-0.1,.3,1)";
    document.body.appendChild(ghost);
    requestAnimationFrame(function () {
      ghost.style.left = (r2.left + r2.width / 2 - 14) + "px"; ghost.style.top = (r2.top + r2.height / 2 - 14) + "px";
      ghost.style.width = "28px"; ghost.style.height = "28px"; ghost.style.opacity = ".25"; ghost.style.borderRadius = "50%";
    });
    setTimeout(function () { ghost.remove(); }, 750);
  }
  function addToCart(id, qty, fromEl) {
    qty = qty || 1;
    var line = null;
    cart.forEach(function (c) { if (c.id === id) line = c; });
    if (line) line.qty += qty; else cart.push({ id: id, qty: qty });
    saveCart(); syncBadges(); renderDrawer(); fly(fromEl);
    toast("Added to cart: " + (P[id] ? P[id].name : "Product"));
  }
  function renderDrawer() {
    var body = $("#drawerBody"); if (!body) return;
    if (!cart.length) {
      body.innerHTML = '<div class="dempty"><p>Your cart is empty.</p><a class="shopnow" href="shop.html">Browse Products</a></div>';
      return;
    }
    var h = "";
    cart.forEach(function (c) {
      var p = P[c.id]; if (!p) return;
      h += '<div class="dline"><img src="' + p.img + '" alt=""><div class="dlin"><b>' + p.name + '</b><span>' + money(p.price) + '</span>' +
        '<div class="qty sm"><button data-dec="' + c.id + '">-</button><span>' + c.qty + '</span><button data-inc="' + c.id + '">+</button></div></div>' +
        '<div class="dlr"><b>' + money(p.price * c.qty) + '</b><button class="rm" data-rm="' + c.id + '" aria-label="Remove">&times;</button></div></div>';
    });
    h += '<div class="dtot"><span>Total</span><b>' + money(cartTotal()) + '</b></div>' +
      '<a class="shopnow wide" href="checkout.html">Go to Checkout</a><a class="ghost wide" href="cart.html">View Cart</a>';
    body.innerHTML = h;
  }

  // ---------- quick view ----------
  var qvId = null;
  function openQV(id) {
    var p = P[id]; if (!p) return;
    qvId = id;
    $("#qvImg").src = p.img; $("#qvImg").alt = p.name;
    $("#qvCat").textContent = "";
    $("#qvName").textContent = p.name;
    $("#qvStars").innerHTML = '<span class="cstars"><b>' + Number(p.rating).toFixed(1) + ' rating</b> - Part no. ' + p.part_no + '</span>';
    $("#qvPrice").textContent = p.rfq ? "Request for Quotation" : money(p.price);
    $("#qvBlurb").textContent = p.blurb || "";
    var add = $("#qvAdd");
    if (p.rfq || HH.cta_mode !== "shop") { add.style.display = "none"; } else { add.style.display = ""; }
    $("#qvView").href = "product-" + id + ".html";
    openOv("#qvRoot");
  }

  // ---------- hero slider ----------
  function initSlider() {
    var hero = $("#heroSlider"); if (!hero) return;
    var slides = $$(".slide", hero), dots = $$(".dot", hero), idx = 0, timer = null;
    function go(i) {
      idx = (i + slides.length) % slides.length;
      slides.forEach(function (s, k) { s.classList.toggle("on", k === idx); });
      dots.forEach(function (dt, k) { dt.classList.toggle("on", k === idx); });
    }
    function auto() { clearInterval(timer); timer = setInterval(function () { go(idx + 1); }, 5000); }
    var prev = $(".arrow.prev", hero), next = $(".arrow.next", hero);
    if (prev) prev.addEventListener("click", function () { go(idx - 1); auto(); });
    if (next) next.addEventListener("click", function () { go(idx + 1); auto(); });
    dots.forEach(function (dt) { dt.addEventListener("click", function () { go(+dt.getAttribute("data-slide")); auto(); }); });
    if (window.matchMedia && window.matchMedia("(hover:hover) and (pointer:fine)").matches) {
      hero.addEventListener("mouseenter", function () { clearInterval(timer); });
      hero.addEventListener("mouseleave", auto);
    }
    var tx = null;
    hero.addEventListener("touchstart", function (e) { tx = e.touches[0].clientX; }, { passive: true });
    hero.addEventListener("touchend", function (e) {
      if (tx === null) return;
      var dx = e.changedTouches[0].clientX - tx;
      if (Math.abs(dx) > 42) { go(idx + (dx < 0 ? 1 : -1)); auto(); }
      tx = null;
    }, { passive: true });
    auto();
  }

  // ---------- reveal ----------
  var io = null, rvTimer = null;
  function observeRv() {
    var els = $$(".rv:not(.in)");
    if (!("IntersectionObserver" in window)) { els.forEach(function (el) { el.classList.add("in"); }); return; }
    if (!io) {
      io = new IntersectionObserver(function (es) {
        es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add("in"); io.unobserve(en.target); } });
      }, { threshold: 0.08, rootMargin: "0px 0px -6% 0px" });
    }
    els.forEach(function (el) { io.observe(el); });
    clearTimeout(rvTimer);
    rvTimer = setTimeout(function () { $$(".rv:not(.in)").forEach(function (el) { el.classList.add("in"); }); }, 1800);
  }

  // ---------- shop ----------
  function initShop() {
    var grid = $("#shopGrid"); if (!grid) return;
    var init = window.HH_INIT_SHOP || { cat: "", q: "" };
    var state = { cat: init.cat || "all", q: (init.q || "").toLowerCase(), sort: "feat", max: +($("#priceRange") ? $("#priceRange").max : 0), rate: 0 };
    var cells = $$(".cell", grid);
    var catNames = { all: "All Products" };
    $$("#catMenu [data-catpick]").forEach(function (b) { catNames[b.getAttribute("data-catpick")] = b.textContent; });
    var label = $("#catBtnLabel");
    if (state.cat !== "all") label.textContent = catNames[state.cat] || "All Products";
    var title = $("#shopTitle");
    function apply() {
      var vis = [];
      cells.forEach(function (c) {
        var ok = (state.cat === "all" || c.getAttribute("data-cat") === state.cat)
          && (!state.q || c.getAttribute("data-name").indexOf(state.q) > -1)
          && (+c.getAttribute("data-rating") >= state.rate)
          && (c.getAttribute("data-rfq") === "1" || +c.getAttribute("data-price") <= state.max);
        c.style.display = ok ? "" : "none";
        if (ok) vis.push(c);
      });
      vis.sort(function (a, b) {
        var pa = a.getAttribute("data-rfq") === "1" ? null : +a.getAttribute("data-price");
        var pb = b.getAttribute("data-rfq") === "1" ? null : +b.getAttribute("data-price");
        if (state.sort === "price-asc") return (pa === null ? 1e12 : pa) - (pb === null ? 1e12 : pb);
        if (state.sort === "price-desc") return (pb === null ? -1 : pb) - (pa === null ? -1 : pa);
        if (state.sort === "name") return a.getAttribute("data-name") < b.getAttribute("data-name") ? -1 : 1;
        if (state.sort === "rating") return +b.getAttribute("data-rating") - +a.getAttribute("data-rating");
        return +a.getAttribute("data-idx") - +b.getAttribute("data-idx");
      });
      vis.forEach(function (c) { grid.appendChild(c); });
      $("#shopCount").textContent = vis.length;
      $("#noRes").hidden = vis.length > 0;
      if (title) title.textContent = state.cat === "all" ? "Shop" : (catNames[state.cat] || "Shop");
      observeRv();
    }
    var catBtn = $("#catBtn"), catMenu = $("#catMenu");
    if (catBtn) catBtn.addEventListener("click", function (e) { e.stopPropagation(); catMenu.classList.toggle("open"); });
    document.addEventListener("click", function () { if (catMenu) catMenu.classList.remove("open"); });
    $$("#catMenu [data-catpick]").forEach(function (b) {
      b.addEventListener("click", function () { state.cat = b.getAttribute("data-catpick"); label.textContent = b.textContent; catMenu.classList.remove("open"); apply(); });
    });
    var sortSel = $("#sortSel"); if (sortSel) sortSel.addEventListener("change", function () { state.sort = sortSel.value; apply(); });
    var rateSel = $("#rateSel"); if (rateSel) rateSel.addEventListener("change", function () { state.rate = +rateSel.value; apply(); });
    var range = $("#priceRange"), pval = $("#priceVal");
    if (range) range.addEventListener("input", function () { state.max = +range.value; if (pval) pval.textContent = money(state.max); apply(); });
    var clr = $("#clearF");
    if (clr) clr.addEventListener("click", function () {
      state = { cat: "all", q: "", sort: "feat", max: range ? +range.max : state.max, rate: 0 };
      label.textContent = "All Products"; if (sortSel) sortSel.value = "feat"; if (rateSel) rateSel.value = "0";
      if (range) { range.value = range.max; if (pval) pval.textContent = money(+range.max); }
      apply();
    });
    apply();
  }

  // ---------- cart page ----------
  function initCartPage() {
    var root = $("#cartRoot"); if (!root) return;
    function render() {
      if (!cart.length) {
        root.innerHTML = '<div class="dempty" style="padding:38px 0"><p>Your cart is empty.</p><a class="shopnow" href="shop.html">Browse Products</a></div>';
        return;
      }
      var h = '<div class="cline head"><span>Product</span><span>Price</span><span>Qty</span><span>Total</span><span></span></div>';
      cart.forEach(function (c) {
        var p = P[c.id]; if (!p) return;
        h += '<div class="cline"><span class="cp"><img src="' + p.img + '" alt=""><b>' + p.name + '</b></span><span>' + money(p.price) + '</span>' +
          '<span><span class="qty sm"><button data-dec="' + c.id + '">-</button><span>' + c.qty + '</span><button data-inc="' + c.id + '">+</button></span></span>' +
          '<span><b>' + money(p.price * c.qty) + '</b></span><span><button class="rm" data-rm="' + c.id + '" aria-label="Remove">&times;</button></span></div>';
      });
      h += '<div class="dtot"><span>Total</span><b>' + money(cartTotal()) + '</b></div><a class="shopnow" href="checkout.html">Proceed to Checkout</a>';
      root.innerHTML = h;
    }
    root.addEventListener("click", function () { setTimeout(render, 0); });
    render();
  }

  // ---------- checkout ----------
  function initCheckout() {
    var form = $("#coForm"); if (!form) return;
    var itemsBox = $("#coItems");
    function renderSum() {
      if (!cart.length) { itemsBox.innerHTML = "<p>Your cart is empty.</p>"; return; }
      var h = "";
      cart.forEach(function (c) { var p = P[c.id]; if (p) h += '<div class="co-line"><span>' + p.name + ' x ' + c.qty + '</span><b>' + money(p.price * c.qty) + '</b></div>'; });
      h += '<div class="co-line tot"><span>Total</span><b>' + money(cartTotal()) + '</b></div>';
      itemsBox.innerHTML = h;
    }
    renderSum();
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var name = $("#coName"), phone = $("#coPhone"), city = $("#coCity"), addr = $("#coAddr"), note = $("#coNote");
      var ok = true;
      [name, phone, city, addr].forEach(function (f) {
        var good = f.value.trim().length >= (f === phone ? 10 : 3);
        f.classList.toggle("bad", !good); if (!good) ok = false;
      });
      if (!cart.length) { toast("Your cart is empty."); return; }
      if (!ok) { toast("Please fill the highlighted fields."); return; }
      var btn = $("#coSubmit"); btn.disabled = true; btn.textContent = "Placing order...";
      var payload = { name: name.value.trim(), phone: phone.value.trim(), city: city.value.trim(), address: addr.value.trim(), note: note.value.trim(), items: cart };
      function finish(orderNo, total, count) {
        var lines = cart.map(function (c) { var p = P[c.id]; return p ? "- " + p.name + " x " + c.qty + " = " + money(p.price * c.qty) : ""; }).filter(Boolean).join("\n");
        var text = "New Order " + orderNo + "\nName: " + payload.name + "\nPhone: " + payload.phone + "\nCity: " + payload.city + "\nAddress: " + payload.address + (payload.note ? "\nNote: " + payload.note : "") + "\n\nProducts:\n" + lines + "\n\nTotal: " + money(total) + " (" + count + " items)";
        var order = { no: orderNo, total: total, count: count, wa: waLink(text) };
        store.set("hhp_order", order);
        cart = []; saveCart(); syncBadges();
        window.open(order.wa, "_blank");
        location.href = "confirmation.html";
      }
      fetch("order-save.php", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(payload) })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res && res.ok) finish(res.no, res.total, res.count);
          else finish("HH-" + new Date().getFullYear() + "-" + Math.floor(1000 + Math.random() * 9000), cartTotal(), cartCount());
        })
        .catch(function () { finish("HH-" + new Date().getFullYear() + "-" + Math.floor(1000 + Math.random() * 9000), cartTotal(), cartCount()); });
    });
  }

  // ---------- confirmation ----------
  function initConfirmation() {
    var root = $("#confRoot"); if (!root) return;
    var o = store.get("hhp_order", null);
    if (o && o.no) {
      $("#confText").textContent = "Order " + o.no + " - " + o.count + " items - " + money(o.total) + ". Our team will call to confirm your order shortly.";
      var wa = $("#confWa");
      if (wa && o.wa) { wa.href = o.wa; wa.hidden = false; }
    }
  }

  // ---------- global clicks (one guarded delegated listener) ----------
  var bound = false;
  function bindGlobal() {
    if (bound) return; bound = true;
    document.addEventListener("click", function (e) {
      var t = e.target;
      var close = t.closest("[data-close]"); if (close) { closeOv(close); return; }
      var add = t.closest("[data-add]"); if (add) { addToCart(+add.getAttribute("data-add"), 1, add); return; }
      var qv = t.closest("[data-qv]"); if (qv) { openQV(+qv.getAttribute("data-qv")); return; }
      var wishB = t.closest("[data-wish]");
      if (wishB) {
        var id = +wishB.getAttribute("data-wish"), i = wish.indexOf(id);
        if (i > -1) { wish.splice(i, 1); toast("Removed from wishlist"); } else { wish.push(id); toast("Added to wishlist"); }
        saveWish(); syncBadges(); return;
      }
      var inc = t.closest("[data-inc]"), dec = t.closest("[data-dec]"), rm = t.closest("[data-rm]");
      if (inc || dec || rm) {
        var cid = +(inc || dec || rm).getAttribute("data-inc") || +(inc || dec || rm).getAttribute("data-dec") || +(inc || dec || rm).getAttribute("data-rm");
        cart = cart.map(function (c) { if (c.id === cid) { if (inc) c.qty++; if (dec) c.qty = Math.max(0, c.qty - 1); if (rm) c.qty = 0; } return c; }).filter(function (c) { return c.qty > 0; });
        saveCart(); syncBadges(); renderDrawer();
        var cr = $("#cartRoot"); if (cr) setTimeout(function () { initCartPageRender(); }, 0);
        return;
      }
      if (t.closest("#cartBtn") || t.closest("#mbCart")) { renderDrawer(); openOv("#drawerRoot"); return; }
      if (t.closest("#menuBtn") || t.closest("#mbCats")) { openOv("#menuRoot"); return; }
      if (t.closest("#acctBtn") || t.closest("#mbAcct")) { toast("Account sign-in opens with the live store."); return; }
      if (t.closest("#qvAdd")) { if (qvId) { addToCart(qvId, 1, $("#qvAdd")); closeAll(); } return; }
      var panel = $("#allCatsPanel");
      if (t.closest("#allCatsBtn")) { if (panel) panel.classList.toggle("open"); return; }
      if (panel && !t.closest(".allcats")) panel.classList.remove("open");
    });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") closeAll(); });
    var si = $("#searchInput");
    if (si) {
      si.addEventListener("input", function () {
        var q = si.value.trim().toLowerCase(), box = $("#suggest");
        if (!box) return;
        if (q.length < 2) { box.classList.remove("open"); box.innerHTML = ""; return; }
        var hits = HH.products.filter(function (p) { return p.name.toLowerCase().indexOf(q) > -1; }).slice(0, 6);
        box.innerHTML = hits.map(function (p) { return '<a href="product-' + p.id + '.html'"><img src="' + p.img + '" alt=""><span>' + p.name + '</span><b>' + (p.rfq ? "RFQ" : money(p.price)) + '</b></a>'; }).join("");
        box.classList.toggle("open", hits.length > 0);
      });
      document.addEventListener("click", function (e) { var box = $("#suggest"); if (box && !e.target.closest(".searchbox")) box.classList.remove("open"); });
    }
    // product page qty
    var pdAdd = $("#pdAdd"), qv = $("#qVal");
    if (pdAdd && qv) {
      var qty = 1;
      $("#qPlus").addEventListener("click", function () { qty++; qv.textContent = qty; });
      $("#qMinus").addEventListener("click", function () { qty = Math.max(1, qty - 1); qv.textContent = qty; });
      pdAdd.addEventListener("click", function () { addToCart(+pdAdd.getAttribute("data-addqty"), qty, pdAdd); });
    }
  }
  function initCartPageRender() { /* cart page re-render happens via its own listener */ }

  document.addEventListener("DOMContentLoaded", function () {
    bindGlobal(); syncBadges(); renderDrawer();
    [initSlider, initShop, initCartPage, initCheckout, initConfirmation].forEach(function (fn) { try { fn(); } catch (e) { if (window.console) console.error(fn.name, e); } });
    observeRv();
  });
})();
