/**
 * View your website at your own local server.
 * Example: if you're using WP-CLI then the common URL is: http://localhost:8080.
 *
 * http://localhost:5173 is serving Vite on development. Access this URL will show empty page.
 *
 */
import fs from 'fs';
import { defineConfig } from "vite";
import { resolve } from 'path';
import vue from '@vitejs/plugin-vue';

// Get the relative path of the vite.config.js file for the alias
const fullPath = import.meta.url.slice(0, import.meta.url.lastIndexOf('/'));
const getWpContentIndex = fullPath.indexOf('wp-content');
const wpContentPath = fullPath.slice(getWpContentIndex);

export default defineConfig(({ mode }) => {
    // Path to the .hot file
    const hotFilePath = resolve(__dirname, '.hot');
  
    if (mode === 'development') {
      // Create the .hot file in development mode
      fs.writeFileSync(hotFilePath, 'HMR is active');
    } else {
      // Ensure the .hot file is removed in production mode
      if (fs.existsSync(hotFilePath)) {
        fs.unlinkSync(hotFilePath);
      }
    }

    return {
        base: './',

        plugins: [
            vue({
                template: {
                    transformAssetUrls: {
                        includeAbsolute: true,
                    }
                }
            }),
            {
                handleHotUpdate({ file, server }) {
                    if (file.endsWith('.php')) {
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
                '@': process.env.NODE_ENV === 'development' ? resolve(wpContentPath + '/static') : '/static',
                '@styles': resolve(__dirname, 'resources/styles'),
            }
        }
    };
});