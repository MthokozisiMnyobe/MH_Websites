import './store-shell.js';

export const COMPATIBILITY_DRAFT_KEY = 'mh_compatibility_draft_v1';

const form = document.querySelector('[data-compatibility-form]');
const status = document.querySelector('[data-compatibility-status]');

try {
  const saved = JSON.parse(window.sessionStorage.getItem(COMPATIBILITY_DRAFT_KEY) ?? 'null');
  if (saved && form) {
    for (const name of ['device_type', 'manufacturer', 'model', 'current_component', 'product_id', 'notes']) {
      if (form.elements[name] && typeof saved[name] === 'string') form.elements[name].value = saved[name];
    }
  }
} catch {
  window.sessionStorage.removeItem(COMPATIBILITY_DRAFT_KEY);
}

form?.addEventListener('submit', (event) => {
  event.preventDefault();
  if (!form.reportValidity()) return;
  const data = Object.fromEntries(new FormData(form).entries());
  const draft = {};
  for (const name of ['device_type', 'manufacturer', 'model', 'current_component', 'product_id', 'notes']) {
    draft[name] = String(data[name] ?? '').slice(0, name === 'notes' ? 1200 : 120);
  }
  window.sessionStorage.setItem(COMPATIBILITY_DRAFT_KEY, JSON.stringify(draft));
  status.textContent = 'Compatibility details saved in this browser session. They have not been sent.';
});
