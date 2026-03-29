import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import NexovaChatWidget from './NexovaChatWidget';

/**
 * Monta el widget en el DOM.
 * Busca #nexova-chat-root; si no existe, lo crea y lo anexa a <body>.
 * Configurable vía: window.NexovaChatConfig = { apiUrl: 'https://...' }
 */
const mount = () => {
    let container = document.getElementById('nexova-chat-root');

    if (!container) {
        container = document.createElement('div');
        container.id = 'nexova-chat-root';
        document.body.appendChild(container);
    }

    createRoot(container).render(
        <StrictMode>
            <NexovaChatWidget />
        </StrictMode>
    );
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
} else {
    mount();
}
