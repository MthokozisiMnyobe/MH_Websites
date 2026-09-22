import { addBasketItem } from './basket.js';
import {
  catalogueSearchParams,
  catalogueSuggestions,
  filterProducts,
  readCatalogueState,
} from './catalogue-core.js';
import { catalogueData, refreshBasketCount } from './store-shell.js';

const app = document.querySelector('[data-catalogue-app]');
const products = catalogueData();

if (app && products.length) {
  const form = app.querySelector('[data-catalogue-form]');
  const search = app.querySelector('[data-catalogue-search]');
  const suggestionList = app.querySelector('[data-search-suggestions]');
  const cards = new Map(Array.from(app.querySelectorAll('[data-product-card]')).map((card) => [card.dataset.productId, card]));
  const grid = app.querySelector('[data-product-grid]');
  const resultStatus = app.querySelector('[data-results-status]');
  const storeStatus = app.querySelector('[data-store-status]');
  const noResults = app.querySelector('[data-no-results]');
  const filterToggle = app.querySelector('[data-filter-toggle]');
  const filterPanel = app.querySelector('[data-filter-panel]');
  const filterClose = app.querySelector('[data-filter-close]');
  const filterBackdrop = app.querySelector('[data-filter-backdrop]');
  const categoryChips = Array.from(app.querySelectorAll('[data-category-chip]'));
  let suggestionIndex = -1;
  let filterReturnFocus = null;

  app.classList.add('is-enhanced');
  filterToggle.hidden = false;

  function formState() {
    return {
      q: search.value.trim(),
      category: form.elements.category.value,
      brand: form.elements.brand.value,
      type: form.elements.type.value,
      sort: form.elements.sort.value || (search.value.trim() ? 'relevance' : 'name-asc'),
    };
  }

  function setFormState(state) {
    search.value = state.q ?? '';
    for (const name of ['category', 'brand', 'type']) {
      form.elements[name].value = state[name] ?? '';
    }
    form.elements.sort.value = state.sort || (state.q ? 'relevance' : 'name-asc');
  }

  function updateCategoryChips(category) {
    categoryChips.forEach((chip) => {
      if ((chip.dataset.categoryChip ?? '') === category) chip.setAttribute('aria-current', 'page');
      else chip.removeAttribute('aria-current');
    });
  }

  function updateUrl(state, mode = 'replace') {
    const params = catalogueSearchParams(state);
    const nextUrl = `${window.location.pathname}${params.size ? `?${params}` : ''}`;
    window.history[mode === 'push' ? 'pushState' : 'replaceState'](state, '', nextUrl);
  }

  function renderResults(state, urlMode = null) {
    const matched = filterProducts(products, state);
    const fragment = document.createDocumentFragment();
    const matchedIds = new Set(matched.map((product) => product.id));

    for (const product of matched) {
      const card = cards.get(product.id);
      if (!card) continue;
      card.hidden = false;
      fragment.append(card);
    }
    for (const [id, card] of cards) {
      if (!matchedIds.has(id)) card.hidden = true;
    }
    grid.append(fragment);

    resultStatus.textContent = `${matched.length} ${matched.length === 1 ? 'product' : 'products'} shown`;
    noResults.hidden = matched.length !== 0;
    updateCategoryChips(state.category);
    if (urlMode) updateUrl(state, urlMode);
  }

  function closeSuggestions() {
    suggestionList.hidden = true;
    suggestionList.replaceChildren();
    search.setAttribute('aria-expanded', 'false');
    search.removeAttribute('aria-activedescendant');
    suggestionIndex = -1;
  }

  function chooseSuggestion(product) {
    search.value = product.name;
    if (form.elements.sort.value === 'name-asc') form.elements.sort.value = 'relevance';
    closeSuggestions();
    renderResults(formState(), 'push');
  }

  function renderSuggestions() {
    const suggestions = catalogueSuggestions(products, search.value);
    suggestionList.replaceChildren();
    suggestionIndex = -1;

    if (!suggestions.length) {
      closeSuggestions();
      return;
    }

    suggestions.forEach((product, index) => {
      const item = document.createElement('li');
      item.setAttribute('role', 'option');
      item.id = `catalogue-suggestion-${index}`;
      item.setAttribute('aria-selected', 'false');
      const title = document.createElement('strong');
      const meta = document.createElement('span');
      title.textContent = product.name;
      meta.textContent = `${product.categoryLabel} · ${product.code}`;
      item.append(title, meta);
      item.addEventListener('pointerdown', (event) => event.preventDefault());
      item.addEventListener('click', () => chooseSuggestion(product));
      suggestionList.append(item);
    });

    suggestionList.hidden = false;
    search.setAttribute('aria-expanded', 'true');
  }

  function moveSuggestion(direction) {
    const options = Array.from(suggestionList.querySelectorAll('[role="option"]'));
    if (!options.length) return;
    suggestionIndex = (suggestionIndex + direction + options.length) % options.length;
    options.forEach((option, index) => option.setAttribute('aria-selected', String(index === suggestionIndex)));
    search.setAttribute('aria-activedescendant', options[suggestionIndex].id);
    options[suggestionIndex].scrollIntoView({ block: 'nearest' });
  }

  function openFilters() {
    filterReturnFocus = document.activeElement;
    app.classList.add('filters-open');
    filterToggle.setAttribute('aria-expanded', 'true');
    filterBackdrop.hidden = false;
    document.body.classList.add('has-open-filter');
    filterPanel.querySelector('select, input, button, a')?.focus();
  }

  function closeFilters() {
    app.classList.remove('filters-open');
    filterToggle.setAttribute('aria-expanded', 'false');
    filterBackdrop.hidden = true;
    document.body.classList.remove('has-open-filter');
    filterReturnFocus?.focus();
  }

  search.addEventListener('input', () => {
    if (form.elements.sort.value === 'name-asc' && search.value.trim()) form.elements.sort.value = 'relevance';
    renderSuggestions();
    renderResults(formState(), 'replace');
  });

  search.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowDown') { event.preventDefault(); moveSuggestion(1); }
    if (event.key === 'ArrowUp') { event.preventDefault(); moveSuggestion(-1); }
    if (event.key === 'Escape') closeSuggestions();
    if (event.key === 'Enter' && suggestionIndex >= 0) {
      event.preventDefault();
      chooseSuggestion(catalogueSuggestions(products, search.value)[suggestionIndex]);
    }
  });
  search.addEventListener('blur', () => window.setTimeout(closeSuggestions, 100));

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    renderResults(formState(), 'push');
    closeSuggestions();
    closeFilters();
  });

  form.querySelectorAll('select').forEach((select) => select.addEventListener('change', () => renderResults(formState(), 'push')));
  categoryChips.forEach((chip) => chip.addEventListener('click', (event) => {
    event.preventDefault();
    form.elements.category.value = chip.dataset.categoryChip ?? '';
    renderResults(formState(), 'push');
  }));
  app.querySelector('[data-reset-filters]').addEventListener('click', (event) => {
    event.preventDefault();
    setFormState({ q: '', category: '', brand: '', type: '', sort: 'name-asc' });
    closeSuggestions();
    renderResults(formState(), 'push');
  });

  grid.addEventListener('click', (event) => {
    const button = event.target.closest('[data-add-to-quote]');
    if (!button) return;
    const product = products.find((item) => item.id === button.dataset.addToQuote);
    if (!product) return;
    addBasketItem(window.localStorage, product.id, 1, products.map((item) => item.id));
    refreshBasketCount(products);
    storeStatus.textContent = `${product.name} added to your Quote Basket.`;
    button.textContent = 'Added to Quote';
    window.setTimeout(() => { button.textContent = 'Add to Quote'; }, 1600);
  });

  filterToggle.addEventListener('click', openFilters);
  filterClose.addEventListener('click', closeFilters);
  filterBackdrop.addEventListener('click', closeFilters);
  document.addEventListener('keydown', (event) => {
    if (!app.classList.contains('filters-open')) return;
    if (event.key === 'Escape') {
      closeFilters();
      return;
    }
    if (event.key === 'Tab') {
      const focusable = Array.from(filterPanel.querySelectorAll('a[href], button:not([disabled]), select:not([disabled]), input:not([disabled])'));
      if (!focusable.length) return;
      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }
  });
  window.addEventListener('popstate', () => {
    const state = readCatalogueState(window.location.href);
    setFormState(state);
    renderResults(formState());
  });

  renderResults(formState());
}
