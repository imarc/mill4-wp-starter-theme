<?php

namespace App\Hooks;

use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;
use WP_Error;

class SecurityHooks implements HooksInterface
{
    use RegistersHooks;

    public function initialize(): void
    {
        $this->addAction('send_headers', [$this, 'setResponseHeaders'], 10, 0);
        $this->setSecurityFilters();
        $this->setAdminNotices();
    }

    private function setAdminNotices(): void
    {
        $this->addAction('admin_notices', function () {
            if (
                current_user_can('administrator') &&
                ! defined('DISALLOW_FILE_EDIT')
            ) {
                echo '<div class="notice notice-warning is-dismissible">';
                echo '<p><strong>Security Notice:</strong> The <code>DISALLOW_FILE_EDIT</code> constant is not defined in <code>wp-config.php</code>. For better security, add <code>define(\'DISALLOW_FILE_EDIT\', true);</code> to disable file editing via the WP admin.</p>';
                echo '</div>';
            }

            if (
                current_user_can('administrator') &&
                defined('DISALLOW_FILE_EDIT') &&
                constant('DISALLOW_FILE_EDIT') === false
            ) {
                echo '<div class="notice notice-warning is-dismissible">';
                echo '<p><strong>Security Notice:</strong> The <code>DISALLOW_FILE_EDIT</code> constant is defined in <code>wp-config.php</code> but is set to <code>false</code>. For better security, set <code>define(\'DISALLOW_FILE_EDIT\', true);</code> to disable file editing via the WP admin.</p>';
                echo '</div>';
            }
        });
    }

    private function setSecurityFilters(): void
    {
        $this->addAction('init', function () {

            if (! function_exists('get_field')) {
                return;
            }

            $disableRest = get_field('security_disable_rest_access', 'option');

            if ($disableRest) {
                $this->addFilter('rest_authentication_errors', function ($result) {
                    if (! empty($result)) {
                        return $result;
                    }

                    if (is_user_logged_in()) {
                        return true;
                    }

                    $disableWpEmbeds = (bool) get_field('security_disable_oembeds', 'option');
                    $requestUri = $_SERVER['REQUEST_URI'] ?? '';

                    if (! $disableWpEmbeds && strpos($requestUri, '/wp-json/oembed/') !== false) {
                        return true;
                    }

                    return new WP_Error(
                        'rest_forbidden',
                        __('REST API access is restricted.', 'your-textdomain'),
                        [ 'status' => 403 ]
                    );
                });
            }

            $disableXmlRpc = get_field('security_disable_xmlrpc', 'option');

            if ($disableXmlRpc) {
                $this->addFilter('xmlrpc_enabled', '__return_false');
            }

            $disableEmojiScripts = get_field('security_disable_emoji_scripts', 'option');

            if ($disableEmojiScripts) {
                $this->disableEmojiScripts();
            }

            $disableWpEmbeds = get_field('security_disable_oembeds', 'option');

            if ($disableWpEmbeds) {
                $this->disableWpEmbeds();
            }
        });
    }

    private function disableWpEmbeds(): void
    {
        // Remove REST API endpoint
        $this->removeAction('rest_api_init', 'wp_oembed_register_route');

        // Turn off oEmbed auto discovery
        $this->addFilter('embed_oembed_discover', '__return_false');

        // Remove oEmbed discovery links from head
        $this->removeAction('wp_head', 'wp_oembed_add_discovery_links');

        // Remove oEmbed-specific JavaScript from front-end and back-end
        $this->removeAction('wp_head', 'wp_oembed_add_host_js');

        // Remove oEmbed filters
        $this->removeFilter('the_content', [ $GLOBALS['wp_embed'], 'autoembed' ], 8);

        // Disable embeds in TinyMCE
        $this->addFilter('tiny_mce_plugins', function ($plugins) {
            return is_array($plugins) ? array_diff($plugins, [ 'wpembed' ]) : [];
        });

        // Remove the wp-embed.js script
        $this->addAction('wp_footer', function () {
            wp_deregister_script('wp-embed');
        }, 1);
    }

    private function disableEmojiScripts(): void
    {
        // Front-end
        $this->removeAction('wp_head', 'print_emoji_detection_script', 7);
        $this->removeAction('wp_print_styles', 'print_emoji_styles');

        // Admin
        $this->removeAction('admin_print_scripts', 'print_emoji_detection_script');
        $this->removeAction('admin_print_styles', 'print_emoji_styles');

        // RSS
        $this->removeFilter('the_content_feed', 'wp_staticize_emoji');
        $this->removeFilter('comment_text_rss', 'wp_staticize_emoji');
        $this->removeFilter('wp_mail', 'wp_staticize_emoji_for_email');

        // TinyMCE
        $this->addFilter('tiny_mce_plugins', function ($plugins) {
            return is_array($plugins) ? array_diff($plugins, [ 'wpemoji' ]) : [];
        });

        $this->addFilter('emoji_svg_url', '__return_false');
    }

    public function setResponseHeaders()
    {
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // X-Content-Type-Options
        header('X-Content-Type-Options: nosniff');

        header('X-Frame-Options: SAMEORIGIN');

        header('Permissions-Policy: accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()');

        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

        // Cache-Control
        if (! is_user_logged_in()) {
            // Public content - cache for 1 hour (3600 seconds)
            header('Cache-Control: public, max-age=3600');
        }

        // Content-Security-Policy
        if ($csp = $this->generateCsp()) {
            header('Content-Security-Policy: ' . $csp);
        }
    }

    private function generateCsp(): string
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

        $cspConfig = array_filter(get_field('security_csp', 'option') ?: []);

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
