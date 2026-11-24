import { resolve } from 'node:path';

import { defineConfig } from 'vite';

const root = resolve(import.meta.dirname, 'src/web/assets/cp');

// Each script is its own classic IIFE, so the asset bundles keep loading them as plain
// `<script>` tags in the same order and `Craft.OrdersWidget` stays a global.
const scripts = {
    snipcart: 'general.js',
    overview: 'overview.js',
    OrdersWidget: 'OrdersWidget.js',
    'field-product-details': 'field-product-details.js',
    'settings-discount': 'settings-discount.js',
    'settings-plugin': 'settings-plugin.js',
    kit: 'kit.js',
};

const styles = {
    snipcart: 'general.css',
    charts: 'charts.css',
    'field-product-details': 'field-product-details.css',
    'settings-plugin': 'settings-plugin.css',
    kit: 'kit.css',
};

export default defineConfig(({ mode }) => {
    const isCss = mode === 'css';

    if (!isCss && !scripts[mode]) {
        throw new Error(`Unknown asset build mode: ${mode}`);
    }

    const input = isCss
        ? Object.fromEntries(Object.entries(styles).map(([name, file]) => [name, resolve(root, 'src/css', file)]))
        : resolve(root, 'src/js', scripts[mode]);

    const output = isCss ? {
        assetFileNames: 'css/[name][extname]',
    } : {
        codeSplitting: false,
        entryFileNames: `js/${mode}.js`,
        format: 'iife',
    };

    return {
        root,
        base: '',
        build: {
            outDir: resolve(root, 'dist'),
            // `dist/img` is committed and isn't built, so each build only overwrites its own files.
            emptyOutDir: false,
            assetsDir: '',
            cssTarget: ['chrome61', 'safari10'],
            minify: 'oxc',
            sourcemap: false,
            target: 'es2015',
            // ApexCharts alone is about 500 kB, and the Overview and Orders widget scripts each carry it.
            chunkSizeWarningLimit: 600,
            rolldownOptions: {
                input,
                output,
            },
        },
    };
});
