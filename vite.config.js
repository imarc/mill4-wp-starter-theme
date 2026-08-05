/**
 * View your website at your own local server.
 * Example: if you're using WP-CLI then the common URL is: http://localhost:8080.
 *
 * http://localhost:5173 is serving Vite on development. Access this URL will show empty page.
 *
 */
import { defineConfig } from "vite";
import { resolve } from 'path';
import vue from '@vitejs/plugin-vue';
import vitrine from '@imarc/vitrine'
import { hotFilePlugin } from './hotFilePlugin.js';

// Get the relative path of the vite.config.js file for the alias
const fullPath = import.meta.url.slice(0, import.meta.url.lastIndexOf('/'));
const getWpContentIndex = fullPath.indexOf('wp-content');
const wpContentPath = fullPath.slice(getWpContentIndex);

export default defineConfig(() => {
    return {
        base: './',

        plugins: [
            ...hotFilePlugin(resolve(__dirname, '.hot')),
            vue({
                template: {
                    transformAssetUrls: {
                        includeAbsolute: true,
                    }
                }
            }),
            vitrine({
              basePaths: [
        
                /**
                 * This should be set to the base directory for your front end files.
                 */
                'resources',
              ],
              includes: [
                /**
                 * These are the entry points to include. These will also need to get
                 * included into your project.
                 */
                '/resources/js/index.js',
              ],
            }),
            {
                handleHotUpdate({ file, server }) {
                    if (file.endsWith('.php') || file.endsWith('.twig')) {
                        server.ws.send({ type: 'full-reload', path: '*' });
                    }
                }
            }
        ],

        css: {
            devSourcemap: true,
        },

        build: {
            // emit manifest so PHP can find the hashed files
            manifest: true,

            outDir: resolve(__dirname, 'dist/'),

            // don't base64 images
            assetsInlineLimit: 0,

            assetsDir: '',

            rollupOptions: {
                input: [
                'resources/js/index.js',
                'resources/styles/index.scss',
                'resources/styles/editor.scss',
                'resources/images/main-icons-sprite.svg',
                ],
                output: {
                entryFileNames: '[hash].js',
                assetFileNames: '[hash].[ext]',
                },
            },
        },

        server: {
            // required to load scripts from custom host
            cors: {
                origin: "*"
            },

            // We need a strict port to match on PHP side.
            strictPort: true,
            port: 5173,
        },

        resolve: {
            alias: {
                '@': resolve(__dirname, 'resources/styles'),
                'vue': 'vue/dist/vue.esm-bundler.js',
                '/main-icons-sprite.svg': resolve(__dirname, 'resources/images/main-icons-sprite.svg')
            }
        }
    };
});