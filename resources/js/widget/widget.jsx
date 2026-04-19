import { createRoot } from 'react-dom/client';
import NexovaChatWidget, { WidgetErrorBoundary } from './NexovaChatWidget';

/**
 * Monta el widget en el DOM.
 * Busca #nexova-chat-root; si no existe, lo crea y lo anexa a <body>.
 * Configurable vía: window.NexovaChatConfig = { apiUrl: 'https://...' }
 *
 * NOTE: StrictMode is intentionally omitted in production to prevent
 * double-invocation of effects that causes visible flicker in the chat.
 */
const mount = () => {
    let container = document.getElementById('nexova-chat-root');

    if (!container) {
        container = document.createElement('div');
        container.id = 'nexova-chat-root';
        document.body.appendChild(container);
    }

    createRoot(container).render(
        <WidgetErrorBoundary>
            <NexovaChatWidget />
        </WidgetErrorBoundary>
    );
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
} else {
    mount();
}
