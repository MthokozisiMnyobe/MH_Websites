import { readBasket } from './basket.js';
import { catalogueData } from './store-shell.js';

const COMPATIBILITY_DRAFT_KEY = 'mh_compatibility_draft_v1';
const products = catalogueData();
const productMap = new Map(products.map((product) => [product.id, product]));
const basket = readBasket(window.localStorage, products.map((product) => product.id));
const items = document.querySelector('[data-print-items]');
const empty = document.querySelector('[data-print-empty]');

if (items) {
  const rows = basket.map((item) => {
    const product = productMap.get(item.id);
    const row = document.createElement('article');
    row.className = 'print-request-line';
    const content = document.createElement('div');
    const title = document.createElement('h3');
    const meta = document.createElement('p');
    const quantity = document.createElement('strong');
    title.textContent = product.name;
    meta.textContent = `${product.code} · ${product.categoryLabel}`;
    quantity.textContent = `Quantity: ${item.quantity}`;
    content.append(title, meta);
    row.append(content, quantity);
    return row;
  });
  items.replaceChildren(...rows);
  empty.hidden = basket.length !== 0;
}

try {
  const draft = JSON.parse(window.sessionStorage.getItem(COMPATIBILITY_DRAFT_KEY) ?? 'null');
  const panel = document.querySelector('[data-print-compatibility]');
  const summary = document.querySelector('[data-print-compatibility-summary]');
  if (draft && panel && summary) {
    const fields = [
      ['device_type', 'Device type'],
      ['manufacturer', 'Manufacturer'],
      ['model', 'Model'],
      ['current_component', 'Current component'],
      ['notes', 'Notes'],
    ];
    for (const [key, label] of fields) {
      if (!draft[key]) continue;
      const wrapper = document.createElement('div');
      const term = document.createElement('dt');
      const definition = document.createElement('dd');
      term.textContent = label;
      definition.textContent = draft[key];
      wrapper.append(term, definition);
      summary.append(wrapper);
    }
    panel.hidden = false;
  }
} catch {
  window.sessionStorage.removeItem(COMPATIBILITY_DRAFT_KEY);
}

document.querySelector('[data-print-request]')?.addEventListener('click', () => window.print());
