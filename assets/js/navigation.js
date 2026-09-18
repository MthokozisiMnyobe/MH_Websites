const header = document.querySelector('[data-site-header]');

if (header) {
  const toggle = header.querySelector('[data-nav-toggle]');
  const panel = header.querySelector('[data-nav-panel]');
  const backdrop = header.querySelector('[data-nav-backdrop]');
  const label = header.querySelector('[data-nav-label]');
  const desktopQuery = window.matchMedia('(min-width: 64rem)');
  let previouslyFocused = null;

  const focusableSelector = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])',
  ].join(',');

  const isOpen = () => toggle?.getAttribute('aria-expanded') === 'true';

  const setOpen = (open, returnFocus = true) => {
    if (!toggle || !panel || !backdrop) return;

    toggle.setAttribute('aria-expanded', String(open));
    header.classList.toggle('is-navigation-open', open);
    document.body.classList.toggle('has-open-navigation', open);
    backdrop.hidden = !open;
    if (label) label.textContent = open ? 'Close' : 'Menu';

    if (open) {
      previouslyFocused = document.activeElement;
      panel.querySelector(focusableSelector)?.focus();
    } else if (returnFocus && previouslyFocused instanceof HTMLElement) {
      previouslyFocused.focus();
    }
  };

  const trapFocus = (event) => {
    if (event.key !== 'Tab' || !isOpen() || !panel || !toggle) return;
    const focusable = [toggle, ...panel.querySelectorAll(focusableSelector)]
      .filter((element) => !element.hasAttribute('hidden'));
    if (focusable.length === 0) return;

    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  };

  header.classList.add('is-enhanced');
  toggle?.removeAttribute('hidden');

  toggle?.addEventListener('click', () => setOpen(!isOpen()));
  backdrop?.addEventListener('click', () => setOpen(false));
  panel?.addEventListener('click', (event) => {
    if (event.target.closest('a') && !desktopQuery.matches) setOpen(false, false);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && isOpen()) {
      event.preventDefault();
      setOpen(false);
      return;
    }
    trapFocus(event);
  });

  desktopQuery.addEventListener('change', (event) => {
    if (event.matches && isOpen()) setOpen(false, false);
  });
}
