import { addBasketItem } from './basket.js';
import { catalogueData, refreshBasketCount } from './store-shell.js';

const products = catalogueData();
const button = document.querySelector('[data-add-to-quote]');
const status = document.querySelector('[data-store-status]');

button?.addEventListener('click', () => {
  const product = products.find((item) => item.id === button.dataset.addToQuote);
  if (!product) return;
  addBasketItem(window.localStorage, product.id, 1, products.map((item) => item.id));
  refreshBasketCount(products);
  status.textContent = `${product.name} added to your Quote Basket.`;
  button.textContent = 'Added to Quote Basket';
});
