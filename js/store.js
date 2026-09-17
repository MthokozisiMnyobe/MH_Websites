/* ============================================================
   Cart state (persisted with localStorage so it survives page
   navigation across this multi-page site) + shared render helpers.
   ============================================================ */

const CART_KEY = "mh_store_cart_v1";

function getCart(){
  try{
    const raw = localStorage.getItem(CART_KEY);
    return raw ? JSON.parse(raw) : [];
  }catch(e){ return []; }
}

function saveCart(cart){
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
  updateCartCount();
}

function addToCart(id, qty=1){
  const product = PRODUCTS.find(p=>p.id===id);
  if(!product || product.stock==="out") return;
  const cart = getCart();
  const existing = cart.find(i=>i.id===id);
  if(existing){ existing.qty += qty; }
  else { cart.push({ id, qty }); }
  saveCart(cart);
}

function removeFromCart(id){
  saveCart(getCart().filter(i=>i.id!==id));
}

function setQty(id, qty){
  const cart = getCart();
  const item = cart.find(i=>i.id===id);
  if(!item) return;
  item.qty = Math.max(1, qty);
  saveCart(cart);
}

function cartLines(){
  return getCart().map(i=>({ ...i, product: PRODUCTS.find(p=>p.id===i.id) }))
    .filter(l=>l.product);
}

function cartSubtotal(){
  return cartLines().reduce((sum,l)=> sum + l.product.price * l.qty, 0);
}

function formatPrice(n){
  return "R " + n.toLocaleString("en-ZA", { minimumFractionDigits:2, maximumFractionDigits:2 });
}

/* ---------------- icon glyphs (SVG, drawn — no photos) ---------------- */
function glyphFor(category, color){
  const stroke = "var(--navy-800)";
  const accent = "var(--orange-600)";
  const fillMap = { black:"#1B1F27", cyan:"#1AA7D8", magenta:"#D8207C", yellow:"#F5C518" };
  const fill = fillMap[color] || accent;
  if(category === "toner"){
    return `<svg class="glyph" viewBox="0 0 64 64" fill="none"><rect x="14" y="18" width="36" height="30" rx="4" stroke="${stroke}" stroke-width="2.5"/><rect x="20" y="10" width="24" height="10" rx="2" stroke="${stroke}" stroke-width="2.5"/><rect x="20" y="26" width="24" height="14" rx="2" fill="${fill}"/></svg>`;
  }
  if(category === "drum"){
    return `<svg class="glyph" viewBox="0 0 64 64" fill="none"><rect x="10" y="20" width="44" height="24" rx="12" stroke="${stroke}" stroke-width="2.5"/><circle cx="32" cy="32" r="6" stroke="${accent}" stroke-width="2.5"/><line x1="18" y1="20" x2="18" y2="44" stroke="${stroke}" stroke-width="2"/><line x1="46" y1="20" x2="46" y2="44" stroke="${stroke}" stroke-width="2"/></svg>`;
  }
  if(category === "printer"){
    return `<svg class="glyph" viewBox="0 0 64 64" fill="none"><rect x="12" y="22" width="40" height="20" rx="3" stroke="${stroke}" stroke-width="2.5"/><rect x="18" y="10" width="28" height="12" rx="2" stroke="${stroke}" stroke-width="2.5"/><rect x="18" y="42" width="28" height="12" fill="${accent}"/><circle cx="44" cy="28" r="2" fill="${accent}"/></svg>`;
  }
  return `<svg class="glyph" viewBox="0 0 64 64" fill="none"><circle cx="32" cy="32" r="20" stroke="${stroke}" stroke-width="2.5"/><path d="M32 20v12l8 6" stroke="${accent}" stroke-width="2.5" stroke-linecap="round"/></svg>`;
}

function productImagePath(product){
  const paths = {
    toner:"assets/images/product-toner.webp",
    drum:"assets/images/product-drum.webp",
    printer:"assets/images/product-printer.webp"
  };
  return paths[product.category] || "";
}

function productVisual(product, detail=false){
  const src = productImagePath(product);
  if(!src) return glyphFor(product.category, product.color);
  return `<img class="product-photo${detail?' product-photo-detail':''}" src="${src}" alt="Representative ${CATEGORY_LABELS[product.category].toLowerCase()} product image" loading="${detail?'eager':'lazy'}">`;
}

function stockBadge(product){
  const map = { in:"In stock", low:"Low stock", out:"Out of stock" };
  return `<span class="badge ${product.stock}">${map[product.stock]}</span>`;
}

function yieldGauge(product){
  if(!product.yield) return "";
  const pct = Math.min(100, Math.round((product.yield/product.yieldMax)*100));
  return `
    <div class="yield-gauge">
      <div class="bar"><span style="width:${pct}%"></span></div>
      <div class="label"><span>${product.yield.toLocaleString()} PG YIELD</span><span>${pct}%</span></div>
    </div>`;
}

function productCard(p){
  const swatch = p.color ? `<span class="swatch ${p.color}"></span>` : "";
  return `
    <a href="product.html?id=${p.id}" class="card">
      <div class="card-media"><span class="media-label">Representative image</span>${productVisual(p)}</div>
      <div class="card-body">
        <span class="card-cat">${CATEGORY_LABELS[p.category]} · ${p.brand}</span>
        <h3 class="card-title">${p.name}</h3>
        <span class="card-sku">${p.id}</span>
        ${yieldGauge(p)}
        <div class="card-foot">
          <span class="price">${formatPrice(p.price)}</span>
          ${stockBadge(p)}
        </div>
      </div>
    </a>`;
}
