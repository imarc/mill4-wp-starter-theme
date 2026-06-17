import fs from 'fs';

/**
 * Writes a `.hot` marker file while the Vite dev server is running so PHP
 * can detect HMR mode (`is_hmr()`). Removes it on server shutdown and
 * before production builds.
 */
export function hotFilePlugin(hotFilePath) {
    let cleaned = false;
    let processCleanupBound = false;

    const clean = () => {
        if (cleaned) {
            return;
        }

        if (fs.existsSync(hotFilePath)) {
            fs.rmSync(hotFilePath);
        }

        cleaned = true;
    };

    const bindProcessCleanup = () => {
        if (processCleanupBound) {
            return;
        }

        processCleanupBound = true;

        // SIGINT (Ctrl+C) terminates without calling process.exit(), so the
        // 'exit' event never fires unless we handle signals explicitly.
        process.on('exit', clean);
        process.on('SIGINT', () => process.exit(130));
        process.on('SIGTERM', () => process.exit(143));
        process.on('SIGHUP', () => process.exit(129));
    };

    return [
        {
            name: 'vite-hot-file:serve',
            apply: 'serve',
            configureServer(server) {
                cleaned = false;
                fs.writeFileSync(hotFilePath, 'HMR is active');

                bindProcessCleanup();

                server.httpServer?.once('close', clean);

                const originalClose = server.close.bind(server);
                server.close = async (...args) => {
                    clean();
                    return originalClose(...args);
                };
            },
        },
        {
            name: 'vite-hot-file:build',
            apply: 'build',
            buildStart() {
                clean();
            },
        },
    ];
}