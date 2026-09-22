export function normaliseSearchText(value) {
  return String(value ?? '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, ' ')
    .trim();
}

export function productSearchText(product) {
  const specifications = Object.entries(product.specifications ?? {})
    .flatMap(([label, value]) => [label, value]);
  return normaliseSearchText([
    product.name,
    product.code,
    product.brand,
    product.categoryLabel,
    product.type,
    product.summary,
    ...(product.keywords ?? []),
    ...specifications,
  ].join(' '));
}

export function rankProduct(product, query) {
  const cleanedQuery = normaliseSearchText(query);
  if (!cleanedQuery) return 0;

  const tokens = cleanedQuery.split(/\s+/).filter(Boolean);
  const name = normaliseSearchText(product.name);
  const code = normaliseSearchText(product.code);
  const brand = normaliseSearchText(product.brand);
  const category = normaliseSearchText(product.categoryLabel);
  const type = normaliseSearchText(product.type);
  const searchable = productSearchText(product);
  let score = name.includes(cleanedQuery) ? 80 : 0;

  for (const token of tokens) {
    if (!searchable.includes(token)) return -1;
    if (name.includes(token)) score += 30;
    if (code.includes(token)) score += 24;
    if (brand.includes(token)) score += 18;
    if (category.includes(token)) score += 14;
    if (type.includes(token)) score += 12;
    score += 4;
  }

  return score;
}

export function filterProducts(products, state) {
  const query = String(state.q ?? '').trim();
  const results = products
    .filter((product) => !state.category || product.category === state.category)
    .filter((product) => !state.brand || product.brand === state.brand)
    .filter((product) => !state.type || product.type === state.type)
    .map((product) => ({ product, score: rankProduct(product, query) }))
    .filter(({ score }) => !query || score >= 0);

  results.sort((left, right) => {
    if (state.sort === 'name-desc') return right.product.name.localeCompare(left.product.name);
    if (state.sort === 'relevance') {
      return right.score - left.score || left.product.name.localeCompare(right.product.name);
    }
    return left.product.name.localeCompare(right.product.name);
  });

  return results.map(({ product }) => product);
}

export function readCatalogueState(urlValue) {
  const url = urlValue instanceof URL ? urlValue : new URL(String(urlValue), 'http://localhost/');
  return {
    q: url.searchParams.get('q') ?? '',
    category: url.searchParams.get('category') ?? '',
    brand: url.searchParams.get('brand') ?? '',
    type: url.searchParams.get('type') ?? '',
    sort: url.searchParams.get('sort') ?? '',
  };
}

export function catalogueSearchParams(state) {
  const params = new URLSearchParams();
  for (const key of ['q', 'category', 'brand', 'type', 'sort']) {
    const value = String(state[key] ?? '').trim();
    if (value && !(key === 'sort' && value === 'name-asc')) params.set(key, value);
  }
  return params;
}

export function catalogueSuggestions(products, query, limit = 6) {
  const cleanedQuery = normaliseSearchText(query);
  if (cleanedQuery.length < 2) return [];
  return products
    .map((product) => ({ product, score: rankProduct(product, cleanedQuery) }))
    .filter(({ score }) => score >= 0)
    .sort((left, right) => right.score - left.score || left.product.name.localeCompare(right.product.name))
    .slice(0, limit)
    .map(({ product }) => product);
}
