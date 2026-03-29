import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';
import path from 'path';

/**
 * Build separado para el widget — salida IIFE con CSS inlineado.
 * Uso: npm run build:widget
 */
function inlineCssPlugin() {
    return {
        name: 'inline-css',
        closeBundle() {
            const jsPath = path.resolve('public/widget.js');
            if (!fs.existsSync(jsPath)) return;

            // Leer CSS del build principal
            const assetsDir = path.resolve('public/build/assets');
            let cssContent = '';
            if (fs.existsSync(assetsDir)) {
                const cssFile = fs.readdirSync(assetsDir).find(
                    f => f.startsWith('widget-') && f.endsWith('.css')
                );
                if (cssFile) {
                    cssContent = fs.readFileSync(path.resolve(assetsDir, cssFile), 'utf-8');
                }
            }

            if (!cssContent) {
                console.warn('⚠ widget CSS not found — run npm run build first');
                return;
            }

            const js      = fs.readFileSync(jsPath, 'utf-8');
            const escaped = cssContent.replace(/\\/g, '\\\\').replace(/`/g, '\\`');
            const prefix  = `(function(){if(!document.getElementById('nxw-css')){var s=document.createElement('style');s.id='nxw-css';s.textContent=\`${escaped}\`;document.head.appendChild(s);}})();\n`;

            fs.writeFileSync(jsPath, prefix + js);
            console.log('✓ CSS inlineado en widget.js');
        },
    };
}

export default defineConfig({
    plugins: [
        react(),
        tailwindcss(),
        inlineCssPlugin(),
    ],
    define: {
        'process.env.NODE_ENV': '"production"',
    },
    build: {
        outDir: 'public',
        emptyOutDir: false,
        cssCodeSplit: false,
        lib: {
            entry:    'resources/js/widget/widget.jsx',
            name:     'NexovaWidget',
            fileName: () => 'widget.js',
            formats:  ['iife'],
        },
        rollupOptions: {
            output: {
                inlineDynamicImports: true,
            },
        },
    },
});
