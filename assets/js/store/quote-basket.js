import {
  clearBasket,
  MAX_QUANTITY,
  readBasket,
  removeBasketItem,
  saveBasket,
  updateBasketQuantity,
  basketSummary,
} from './basket.js';
import { catalogueData, refreshBasketCount } from './store-shell.js';

const page = document.querySelector('[data-quote-basket-page]');
const products = catalogueData();
const productMap = new Map(products.map((product) => [product.id, product]));
const allowedIds = products.map((product) => product.id);

function element(tag, className, text) {
  const node = document.createElement(tag);
  if (className) node.className = className;
  if (text !== undefined) node.textContent = text;
  return node;
}

function createBasketLine(item) {
  const product = productMap.get(item.id);
  const line = element('article', 'quote-basket-line');
  line.dataset.basketProduct = item.id;

  const media = element('div', 'quote-basket-line__media');
  if (product.imageUrl) {
    const image = document.createElement('img');
    image.src = product.imageUrl;
    image.alt = product.imageAlt || '';
    image.width = 120;
    image.height = 120;
    media.append(image);
  } else {
    media.append(element('span', 'quote-basket-line__fallback', product.type.slice(0, 2).toUpperCase()));
  }

  const details = element('div', 'quote-basket-line__details');
  details.append(element('span', 'quote-basket-line__category', product.categoryLabel));
  details.append(element('h2', '', product.name));
  details.append(element('p', '', `${product.code} · ${product.brand}`));

  const controls = element('div', 'quote-basket-line__controls');
  const label = element('label', '', 'Quantity');
  const quantityGroup = element('div', 'quantity-control');
  const decrease = element('button', '', '−');
  decrease.type = 'button';
  decrease.dataset.quantityDecrease = item.id;
  decrease.setAttribute('aria-label', `Decrease quantity for ${product.name}`);
  const input = document.createElement('input');
  input.type = 'number';
  input.name = `quantity_${item.id}`;
  input.value = String(item.quantity);
  input.min = '1';
  input.max = String(MAX_QUANTITY);
  input.inputMode = 'numeric';
  input.dataset.quantityInput = item.id;
  input.setAttribute('aria-label', `Quantity for ${product.name}`);
  const increase = element('button', '', '+');
  increase.type = 'button';
  increase.dataset.quantityIncrease = item.id;
  increase.setAttribute('aria-label', `Increase quantity for ${product.name}`);
  quantityGroup.append(decrease, input, increase);
  const remove = element('button', 'text-button', 'Remove');
  remove.type = 'button';
  remove.dataset.removeBasketItem = item.id;
  remove.setAttribute('aria-label', `Remove ${product.name} from Quote Basket`);
  controls.append(label, quantityGroup, remove);
  line.append(media, details, controls);
  return line;
}

function renderBasket(message = '') {
  let basket = readBasket(window.localStorage, allowedIds);
  basket = saveBasket(window.localStorage, basket, allowedIds);
  const empty = page.querySelector('[data-basket-empty]');
  const content = page.querySelector('[data-basket-content]');
  const lines = page.querySelector('[data-basket-lines]');
  const status = page.querySelector('[data-basket-status]');
  empty.hidden = basket.length !== 0;
  content.hidden = basket.length === 0;
  lines.replaceChildren(...basket.map(createBasketLine));
  const summary = basketSummary(basket);
  page.querySelector('[data-basket-product-count]').textContent = String(summary.products);
  page.querySelector('[data-basket-total-quantity]').textContent = String(summary.units);
  status.textContent = message;
  refreshBasketCount(products);
}

page?.addEventListener('click', (event) => {
  const decrease = event.target.closest('[data-quantity-decrease]');
  const increase = event.target.closest('[data-quantity-increase]');
  const remove = event.target.closest('[data-remove-basket-item]');

  if (decrease || increase) {
    const id = (decrease ?? increase).dataset[decrease ? 'quantityDecrease' : 'quantityIncrease'];
    const basket = readBasket(window.localStorage, allowedIds);
    const item = basket.find((entry) => entry.id === id);
    if (!item) return;
    const nextQuantity = item.quantity + (increase ? 1 : -1);
    updateBasketQuantity(window.localStorage, id, nextQuantity, allowedIds);
    renderBasket('Quote Basket quantity updated.');
  }

  if (remove) {
    const product = productMap.get(remove.dataset.removeBasketItem);
    removeBasketItem(window.localStorage, remove.dataset.removeBasketItem, allowedIds);
    renderBasket(`${product?.name ?? 'Product'} removed from your Quote Basket.`);
  }
});

page?.addEventListener('change', (event) => {
  const input = event.target.closest('[data-quantity-input]');
  if (!input) return;
  updateBasketQuantity(window.localStorage, input.dataset.quantityInput, input.value, allowedIds);
  renderBasket('Quote Basket quantity updated.');
});

page?.querySelector('[data-clear-basket]')?.addEventListener('click', () => {
  if (!window.confirm('Clear every item from your Quote Basket?')) return;
  clearBasket(window.localStorage);
  renderBasket('Your Quote Basket has been cleared.');
});

if (page) renderBasket();
