import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');

async function importSource(relativePath) {
  const source = fs.readFileSync(path.join(root, relativePath), 'utf8');
  return import(`data:text/javascript;base64,${Buffer.from(source).toString('base64')}`);
}

const core = await importSource('assets/js/store/catalogue-core.js');
const basket = await importSource('assets/js/store/basket.js');
let assertions = 0;

function check(condition, label) {
  assertions += 1;
  if (!condition) throw new Error(`${label} failed.`);
}

const products = [
  { id: 'toner', code: 'CAT-TNR-001', name: 'Black Toner Cartridge', brand: 'Various manufacturers', category: 'toner-ink', categoryLabel: 'Toner & Ink', type: 'Toner cartridge', summary: 'Black laser supply', keywords: ['black', 'laser'], specifications: { Colour: 'Black' } },
  { id: 'cable', code: 'CAT-ACC-001', name: 'USB Printer Cable', brand: 'Universal accessories', category: 'accessories', categoryLabel: 'Accessories', type: 'Printer cable', summary: 'Wired connection', keywords: ['usb'], specifications: { Connection: 'USB' } },
];

check(core.normaliseSearchText('Toner & INK') === 'toner ink', 'Search normalisation');
check(core.rankProduct(products[0], 'black toner') > core.rankProduct(products[0], 'laser'), 'Name phrase receives stronger relevance');
check(core.rankProduct(products[1], 'black toner') === -1, 'Unrelated search result excluded');
check(core.filterProducts(products, { q: 'usb', category: '', brand: '', type: '', sort: 'relevance' })[0].id === 'cable', 'Search filters and ranks');
check(core.filterProducts(products, { q: '', category: 'toner-ink', brand: '', type: '', sort: 'name-asc' }).length === 1, 'Category filter works');
check(core.catalogueSuggestions(products, 'ton').length === 1, 'Suggestions use relevance search');

const params = core.catalogueSearchParams({ q: 'black toner', category: 'toner-ink', brand: '', type: '', sort: 'relevance' });
check(params.get('q') === 'black toner' && params.get('category') === 'toner-ink', 'URL parameters preserve filter state');
const restored = core.readCatalogueState(`http://localhost/store/?${params}`);
check(restored.q === 'black toner' && restored.sort === 'relevance', 'URL state restores correctly');

class MemoryStorage {
  constructor() { this.values = new Map(); }
  getItem(key) { return this.values.has(key) ? this.values.get(key) : null; }
  setItem(key, value) { this.values.set(key, String(value)); }
  removeItem(key) { this.values.delete(key); }
}

const storage = new MemoryStorage();
basket.addBasketItem(storage, 'toner', 1, ['toner', 'cable']);
basket.addBasketItem(storage, 'toner', 2, ['toner', 'cable']);
check(basket.readBasket(storage, ['toner', 'cable'])[0].quantity === 3, 'Adding duplicate products updates quantity');
basket.updateBasketQuantity(storage, 'toner', 0, ['toner', 'cable']);
check(basket.readBasket(storage, ['toner'])[0].quantity === 1, 'Quantity has a lower boundary');
basket.updateBasketQuantity(storage, 'toner', 500, ['toner', 'cable']);
check(basket.readBasket(storage, ['toner'])[0].quantity === 99, 'Quantity has an upper boundary');
basket.addBasketItem(storage, 'tampered-id', 1, ['toner', 'cable']);
check(basket.readBasket(storage, ['toner', 'cable']).length === 1, 'Unknown product IDs are rejected client-side');
basket.removeBasketItem(storage, 'toner', ['toner', 'cable']);
check(basket.readBasket(storage, ['toner', 'cable']).length === 0, 'Product removal works');
basket.addBasketItem(storage, 'cable', 2, ['toner', 'cable']);
check(basket.basketQuantity(basket.readBasket(storage)) === 2, 'Basket quantity total works');
const summaryStorage = new MemoryStorage();
basket.addBasketItem(summaryStorage, 'toner', 3, ['toner', 'cable']);
basket.addBasketItem(summaryStorage, 'cable', 2, ['toner', 'cable']);
const summary = basket.basketSummary(basket.readBasket(summaryStorage, ['toner', 'cable']));
check(summary.products === 2 && summary.units === 5, 'Basket summary distinguishes products from total units');
check(basket.quoteBasketLabel(1) === 'Quote Basket, 1 item', 'Basket label uses accessible singular wording');
check(basket.quoteBasketLabel(5) === 'Quote Basket, 5 items', 'Basket label uses accessible plural wording');
basket.clearBasket(storage);
check(basket.readBasket(storage).length === 0, 'Clear basket works');

console.log(`Checkpoint 4 client tests passed (${assertions} assertions).`);
