import './bootstrap';

/* ═══════════════════════════════════════════════════
   NEXOVA DESK — Page transitions
   Spinner circular centrado + backdrop blur + fade-in
═══════════════════════════════════════════════════ */
function initPageTransitions() {
    // Crear overlay si no existe
    let loader = document.getElementById('nx-page-loader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'nx-page-loader';
        loader.innerHTML = '<div class="nx-spinner"></div>';
        document.body.appendChild(loader);
    }

    let hideTimer = null;

    function showLoader() {
        clearTimeout(hideTimer);
        loader.classList.add('nx-loader--visible');
    }

    function hideLoader() {
        clearTimeout(hideTimer);
        loader.classList.remove('nx-loader--visible');

        // Fade-in del contenido principal
        hideTimer = setTimeout(() => {
            const target = document.querySelector('.fi-main')
                        || document.querySelector('.fi-page-content')
                        || document.querySelector('main');
            if (target) {
                target.classList.remove('nx-page-entering');
                void target.offsetWidth; // reflow para reiniciar animación
                target.classList.add('nx-page-entering');
                setTimeout(() => target.classList.remove('nx-page-entering'), 300);
            }
        }, 50);
    }

    // Livewire 3 wire:navigate
    document.addEventListener('livewire:navigate',   showLoader);
    document.addEventListener('livewire:navigated',  hideLoader);

    // Navegación normal (no wire:navigate) — full page load
    window.addEventListener('beforeunload', showLoader);
    window.addEventListener('pageshow',     hideLoader);

    // Click en links normales de Filament que no usan wire:navigate
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a[href]');
        if (!link) return;
        const href = link.getAttribute('href') || '';
        const isInternal = href.startsWith('/') || href.startsWith(window.location.origin);
        const isHash = href.startsWith('#');
        const isExternal = link.target === '_blank';
        const hasWireNav = link.hasAttribute('wire:navigate');
        if (isInternal && !isHash && !isExternal && !hasWireNav) {
            showLoader();
        }
    });

    // Ocultar al cargar por primera vez
    hideLoader();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPageTransitions);
} else {
    initPageTransitions();
}
