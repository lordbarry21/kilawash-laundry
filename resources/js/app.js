import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Smooth KPI Count-Up, SVG Ring Sweep, and Cursor Spotlight Sheen
document.addEventListener('DOMContentLoaded', () => {
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // 1. Cursor Spotlight Reflection on Studio Cards
    document.querySelectorAll('.kw-spotlight').forEach((card) => {
        card.addEventListener('pointermove', (e) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${e.clientX - rect.left}px`);
            card.style.setProperty('--my', `${e.clientY - rect.top}px`);
        }, { passive: true });
    });

    // 2. Smooth Quartic Ease-Out Number Count-Up
    document.querySelectorAll('[data-countup]').forEach((el) => {
        const target = Number(el.dataset.countup || 0);
        const prefix = el.dataset.prefix || '';
        const suffix = el.dataset.suffix || '';
        const format = (v) => prefix + Math.round(v).toLocaleString('id-ID') + suffix;

        if (prefersReduced || target === 0) {
            el.textContent = format(target);
            return;
        }

        const duration = Number(el.dataset.duration || 800);
        const start = performance.now();

        const tick = (now) => {
            const progress = Math.min(1, (now - start) / duration);
            const eased = 1 - Math.pow(1 - progress, 4);
            el.textContent = format(target * eased);
            if (progress < 1) {
                requestAnimationFrame(tick);
            } else {
                el.textContent = format(target);
            }
        };

        requestAnimationFrame(tick);
    });

    // 3. Smooth SVG Circular Progress Ring Sweep
    document.querySelectorAll('[data-ring-target]').forEach((circle) => {
        const targetOffset = circle.dataset.ringTarget;
        if (prefersReduced) {
            circle.style.strokeDashoffset = targetOffset;
            return;
        }
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                circle.style.strokeDashoffset = targetOffset;
            });
        });
    });
});
