import { basketQuantity, quoteBasketLabel, readBasket } from './basket.js';

export function catalogueData() {
  const node = document.querySelector('#catalogue-data');
  if (!node) return [];
  try {
    const products = JSON.parse(node.textContent ?? '[]');
    return Array.isArray(products) ? products : [];
  } catch {
    return [];
  }
}

export function refreshBasketCount(products = catalogueData()) {
  const allowedIds = products.length ? products.map((product) => product.id) : null;
  const quantity = basketQuantity(readBasket(window.localStorage, allowedIds));
  document.querySelectorAll('[data-basket-count]').forEach((node) => {
    node.textContent = String(quantity);
  });
  document.querySelectorAll('[data-basket-link]').forEach((link) => {
    link.setAttribute('aria-label', quoteBasketLabel(quantity));
  });
  return quantity;
}

window.addEventListener('storage', () => refreshBasketCount());
document.addEventListener('quote-basket:change', () => refreshBasketCount());
refreshBasketCount();
