import { readBasket } from './basket.js';
import { catalogueData } from './store-shell.js';

const COMPATIBILITY_DRAFT_KEY = 'mh_compatibility_draft_v1';
const products = catalogueData();
const productMap = new Map(products.map((product) => [product.id, product]));
const basket = readBasket(window.localStorage, products.map((product) => product.id));
const itemsContainer = document.querySelector('[data-request-items]');
const emptyState = document.querySelector('[data-request-empty]');
const payload = document.querySelector('[data-basket-payload]');
if (payload) payload.value = JSON.stringify(basket.map(({ id, quantity }) => ({ id, quantity })));

function createSummaryLine(item) {
  const product = productMap.get(item.id);
  const line = document.createElement('article');
  line.className = 'quote-review-line';
  const title = document.createElement('h3');
  title.textContent = product.name;
  const meta = document.createElement('p');
  meta.textContent = `${product.code} · Quantity ${item.quantity}`;
  line.append(title, meta);
  return line;
}

if (itemsContainer) {
  itemsContainer.replaceChildren(...basket.map(createSummaryLine));
  emptyState.hidden = basket.length !== 0;
}

/* Legacy browser drafts are shown only when no authoritative server draft is present. */
try {
  const draft = JSON.parse(window.sessionStorage.getItem(COMPATIBILITY_DRAFT_KEY) ?? 'null');
  const panel = document.querySelector('[data-compatibility-draft]');
  const summary = document.querySelector('[data-compatibility-summary]');
  if (draft && panel && summary && summary.children.length === 0) {
    const labels = {
      device_type: 'Device type',
      manufacturer: 'Manufacturer',
      model: 'Model',
      current_component: 'Current component',
      product_id: 'Product considered',
      notes: 'Notes',
    };
    for (const [key, label] of Object.entries(labels)) {
      if (!draft[key]) continue;
      const wrapper = document.createElement('div');
      const term = document.createElement('dt');
      const definition = document.createElement('dd');
      term.textContent = label;
      definition.textContent = key === 'product_id' ? (productMap.get(draft[key])?.name ?? draft[key]) : draft[key];
      wrapper.append(term, definition);
      summary.append(wrapper);
    }
    panel.hidden = false;
  }
} catch {
  window.sessionStorage.removeItem(COMPATIBILITY_DRAFT_KEY);
}
