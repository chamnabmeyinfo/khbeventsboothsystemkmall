import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'public/css/floorplan.css',
                'public/js/floorplan.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        // Enable minification for production
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.log in production
                drop_debugger: true,
            },
        },
        // Generate source maps for debugging
        sourcemap: false,
        // Chunk splitting for better caching
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: [], // Add vendor libraries here if needed
                },
            },
        },
    },
});
