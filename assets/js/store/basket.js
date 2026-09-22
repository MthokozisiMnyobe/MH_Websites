export const QUOTE_BASKET_KEY = 'mh_quote_basket_v1';
export const MAX_QUANTITY = 99;

export function sanitiseBasket(items, allowedIds = null) {
  if (!Array.isArray(items)) return [];
  const allowed = allowedIds ? new Set(allowedIds) : null;
  const merged = new Map();

  for (const item of items) {
    const id = typeof item?.id === 'string' ? item.id.trim() : '';
    const quantity = Math.min(MAX_QUANTITY, Math.max(1, Number.parseInt(item?.quantity, 10) || 1));
    if (!id || (allowed && !allowed.has(id))) continue;
    merged.set(id, Math.min(MAX_QUANTITY, (merged.get(id) ?? 0) + quantity));
  }

  return Array.from(merged, ([id, quantity]) => ({ id, quantity }));
}

export function readBasket(storage, allowedIds = null) {
  try {
    return sanitiseBasket(JSON.parse(storage.getItem(QUOTE_BASKET_KEY) ?? '[]'), allowedIds);
  } catch {
    return [];
  }
}

export function saveBasket(storage, items, allowedIds = null) {
  const basket = sanitiseBasket(items, allowedIds);
  storage.setItem(QUOTE_BASKET_KEY, JSON.stringify(basket));
  return basket;
}

export function addBasketItem(storage, id, quantity = 1, allowedIds = null) {
  const basket = readBasket(storage, allowedIds);
  const existing = basket.find((item) => item.id === id);
  if (existing) existing.quantity = Math.min(MAX_QUANTITY, existing.quantity + Math.max(1, Number(quantity) || 1));
  else basket.push({ id, quantity: Math.max(1, Number(quantity) || 1) });
  return saveBasket(storage, basket, allowedIds);
}

export function updateBasketQuantity(storage, id, quantity, allowedIds = null) {
  const basket = readBasket(storage, allowedIds);
  const item = basket.find((entry) => entry.id === id);
  if (!item) return basket;
  item.quantity = Math.min(MAX_QUANTITY, Math.max(1, Number.parseInt(quantity, 10) || 1));
  return saveBasket(storage, basket, allowedIds);
}

export function removeBasketItem(storage, id, allowedIds = null) {
  return saveBasket(storage, readBasket(storage, allowedIds).filter((item) => item.id !== id), allowedIds);
}

export function clearBasket(storage) {
  storage.removeItem(QUOTE_BASKET_KEY);
  return [];
}

export function basketQuantity(items) {
  return sanitiseBasket(items).reduce((total, item) => total + item.quantity, 0);
}

export function basketSummary(items) {
  const basket = sanitiseBasket(items);
  return {
    products: basket.length,
    units: basket.reduce((total, item) => total + item.quantity, 0),
  };
}

export function quoteBasketLabel(quantity) {
  const safeQuantity = Math.max(0, Number.parseInt(quantity, 10) || 0);
  return `Quote Basket, ${safeQuantity} ${safeQuantity === 1 ? 'item' : 'items'}`;
}
