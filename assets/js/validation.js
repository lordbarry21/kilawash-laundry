/**
 * ============================================================================
 * FILE: assets/js/validation.js
 * FUNGSI: Sinkronisasi status validasi visual (:user-invalid) dengan atribut
 *         aksesibilitas (aria-invalid, aria-describedby, & live region),
 *         sesuai panduan modern-web-guidance (`validate-input-after-interaction`
 *         & `accessible-error-announcement`).
 * ============================================================================
 */

(() => {
  'use strict';

  // 1. Hubungkan pesan error ke aria-describedby HANYA ketika field sedang invalid
  const linkError = (el, shown) => {
    const errorId = el.dataset.error;
    if (!errorId) return;
    const ids = (el.getAttribute('aria-describedby') || '')
      .split(/\s+/)
      .filter((id) => id && id !== errorId);
    if (shown) ids.push(errorId);
    if (ids.length) el.setAttribute('aria-describedby', ids.join(' '));
    else el.removeAttribute('aria-describedby');
  };

  // 2. Satu polite live region untuk mengumumkan error ke screen reader
  const announcer = document.createElement('div');
  announcer.setAttribute('aria-live', 'polite');
  announcer.classList.add('visually-hidden');
  document.body.append(announcer);

  let announceTimer;
  let announcedField;

  const spokenText = (node) => {
    const copy = node.cloneNode(true);
    copy.querySelectorAll('[aria-hidden="true"]').forEach((n) => n.remove());
    copy
      .querySelectorAll('[role="img"][aria-label]')
      .forEach((n) => n.replaceWith(`${n.getAttribute('aria-label')}: `));
    return copy.textContent.replace(/\s+/g, ' ').trim();
  };

  const announceError = (el) => {
    const error = document.getElementById(el.dataset.error);
    if (!error) return;
    const label = el.labels?.[0] ? `${spokenText(el.labels[0])}: ` : '';
    clearTimeout(announceTimer);
    announcer.textContent = '';
    announcedField = el;
    announceTimer = setTimeout(() => {
      announcer.textContent = label + spokenText(error);
    }, 500);
  };

  // 3. Jalur Native (:user-invalid didukung)
  const supportsUserInvalid = CSS.supports('selector(:user-invalid)');

  if (supportsUserInvalid) {
    const flagged = new WeakSet();
    const announced = new WeakSet();

    const syncAria = (el, announce = false) => {
      if (!el?.matches?.('input, select, textarea')) return;
      const invalid = el.matches(':user-invalid');

      if (invalid) {
        if (announce && !announced.has(el)) {
          announceError(el);
          announced.add(el);
        }
        el.setAttribute('aria-invalid', 'true');
        linkError(el, true);
        flagged.add(el);
      } else if (flagged.has(el) || el.getAttribute('aria-invalid') !== 'true') {
        el.setAttribute('aria-invalid', 'false');
        linkError(el, false);
        flagged.delete(el);
        announced.delete(el);
        if (announcedField === el) {
          clearTimeout(announceTimer);
          announcer.textContent = '';
        }
      }
    };

    // Mulai setiap field dengan aria-invalid="false" agar tidak diumumkan error saat load
    document.querySelectorAll('input, select, textarea').forEach((el) => syncAria(el));

    document.addEventListener('blur', (e) => syncAria(e.target, true), true);
    document.addEventListener('input', (e) => {
      if (e.target.hasAttribute?.('aria-invalid')) syncAria(e.target);
    });
    document.addEventListener('change', (e) => syncAria(e.target, true), true);
    document.addEventListener(
      'invalid',
      (e) => {
        announced.add(e.target);
        syncAria(e.target);
      },
      true
    );
    document.addEventListener(
      'reset',
      (e) => {
        setTimeout(() => [...(e.target.elements || [])].forEach((el) => syncAria(el)));
      },
      true
    );
  } else {
    // 4. Fallback untuk browser lama tanpa :user-invalid (menggunakan WeakMap)
    const dirtyState = new WeakMap();

    const updateFallback = (input) => {
      const isValid = input.validity.valid;
      input.classList.toggle('user-invalid-fallback', !isValid);
      input.setAttribute('aria-invalid', isValid ? 'false' : 'true');
      linkError(input, !isValid);
    };

    document.querySelectorAll('input, select, textarea').forEach((el) => {
      if (!el.hasAttribute('aria-invalid')) el.setAttribute('aria-invalid', 'false');
    });

    const onEvent = (event) => {
      const input = event.target;
      if (event.type === 'reset') {
        for (const control of input.elements || []) {
          dirtyState.delete(control);
          control.classList.remove('user-invalid-fallback');
          if (control.matches?.('input, select, textarea')) {
            control.setAttribute('aria-invalid', 'false');
            linkError(control, false);
          }
        }
        return;
      }

      if (!input?.checkValidity) return;

      if (event.type === 'invalid') {
        dirtyState.set(input, { interacted: true, blurred: true });
        updateFallback(input);
        return;
      }

      const state = dirtyState.get(input) || { interacted: false, blurred: false };
      if (event.type === 'input' || event.type === 'change') {
        state.interacted = true;
        dirtyState.set(input, state);
        if (state.blurred) updateFallback(input);
      } else if (event.type === 'blur') {
        state.blurred = true;
        dirtyState.set(input, state);
        if (state.interacted) updateFallback(input);
      }
    };

    document.addEventListener('blur', onEvent, true);
    document.addEventListener('input', onEvent);
    document.addEventListener('change', onEvent);
    document.addEventListener('invalid', onEvent, true);
    document.addEventListener('reset', onEvent, true);
  }
})();
