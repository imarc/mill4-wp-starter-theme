<?php

namespace App\Hooks;

use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Contracts\HooksInterface;

class SecurityHooks implements HooksInterface
{
    use RegistersHooks;

    public function initialize(): void
    {
        $this->addAction('send_headers', [$this, 'response_headers'], 10, 0);
    }

    public function response_headers()
    {
        // Referer Policy
        header('Referer-Policy: strict-origin-when-cross-origin');

        // X-Content-Type-Options
        header('X-Content-Type-Options: nosniff');

        // Cache-Control
        if (! is_user_logged_in()) {
            // Public content - cache for 1 hour (3600 seconds)
            header('Cache-Control: public, max-age=3600');
        }

        // Content-Security-Policy
        header('Content-Security-Policy: ' . $this->generate_csp());
    }


    private function generate_csp(): string
    {
        if (! function_exists('get_field')) {
            return '';
        }

        $directives = [
            'default-src',
            'script-src',
            'script-src-elem',
            'connect-src',
            'style-src',
            'font-src',
            'img-src',
            'media-src',
            'frame-src',
            'frame-ancestors',
        ];

        $cspConfig = array_filter(get_field('csp', 'option') ?: []);

        $cspDirectives = [];
        foreach ($directives as $directive) {
            $value = $cspConfig[$directive] ?? '';

            $value = trim(preg_replace('/\s+/', ' ', $value));

            if ($value) {
                $cspDirectives[] = $directive . ' ' . $value;
            }

        }

        if ($cspConfig['upgrade-insecure-requests'] ?? false) {
            $cspDirectives[] = 'upgrade-insecure-requests';
        }

        if ($cspConfig['additional-rules'] ?? false) {
            $cspDirectives[] = $cspConfig['additional-rules'];
        }

        return implode('; ', $cspDirectives);
    }
}
