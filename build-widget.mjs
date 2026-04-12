/**
 * Widget build script — esbuild con React CJS bundleado.
 *
 * FIX TDZ: mainFields: ['browser','main'] fuerza esbuild a usar el build
 * CJS de React (react/cjs/react.production.min.js) que usa var en vez de let/const.
 * Así el bundle IIFE no tiene TDZ posible en los internals de React.
 */
import esbuild from 'esbuild';
import { readFileSync, writeFileSync, readdirSync, existsSync } from 'fs';
import { resolve } from 'path';

// 1. Build JS con esbuild — React bundleado en CJS (sin let/const)
const result = await esbuild.build({
    entryPoints: ['resources/js/widget/widget.jsx'],
    bundle: true,
    format: 'iife',
    globalName: 'NexovaWidget',
    platform: 'browser',
    target: ['chrome80'],
    minify: true,
    jsx: 'automatic',
    define: {
        'process.env.NODE_ENV': '"production"',
    },
    // ── Forzar CJS de React → usa var en vez de let/const → no TDZ ──
    mainFields: ['browser', 'main'],
    conditions: ['browser', 'require', 'default'],
    outfile: 'public/widget.js',
    write: true,
});

if (result.errors.length > 0) {
    console.error('Build errors:', result.errors);
    process.exit(1);
}

console.log('✓ JS built con esbuild (React CJS bundleado, sin TDZ)');

// 2. Leer CSS del build de Vite
const assetsDir = resolve('public/build/assets');
let cssContent = '';
if (existsSync(assetsDir)) {
    const cssFile = readdirSync(assetsDir).find(
        f => f.startsWith('widget-') && f.endsWith('.css')
    );
    if (cssFile) {
        cssContent = readFileSync(resolve(assetsDir, cssFile), 'utf-8');
    }
}

if (!cssContent) {
    console.warn('⚠ widget CSS not found — run npm run build first');
    process.exit(1);
}

// 3. Inline CSS al inicio del JS
const js = readFileSync('public/widget.js', 'utf-8');
const escaped = cssContent.replace(/\\/g, '\\\\').replace(/`/g, '\\`');
const prefix = `(function(){if(!document.getElementById('nxw-css')){var s=document.createElement('style');s.id='nxw-css';s.textContent=\`${escaped}\`;document.head.appendChild(s);}})();\n`;

writeFileSync('public/widget.js', prefix + js);

const finalSize = (prefix + js).length;
console.log(`✓ CSS inlineado en widget.js (${(finalSize / 1024).toFixed(1)} kB)`);
