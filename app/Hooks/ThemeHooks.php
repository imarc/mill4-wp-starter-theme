<?php

namespace App\Hooks;

use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;

class ThemeHooks implements HooksInterface
{
    use RegistersHooks;

    public function initialize(): void
    {
        $this->addAction('after_setup_theme', [$this, 'themeSupports']);
    }

    public function themeSupports()
    {
        $this->addBasicThemeSupports();
        $this->addPostThumbnailSupport();
        $this->addHtml5Support();
        $this->addSvgSupport();
        $this->addPostFormatSupport();
        $this->addMenuSupport();
        $this->disableCommenting();
    }

    private function addBasicThemeSupports(): void
    {
        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');
    }

    private function addPostThumbnailSupport(): void
    {
        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');
    }

    private function addHtml5Support(): void
    {
        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support(
            'html5',
            [
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            ]
        );
    }

    private function addPostFormatSupport(): void
    {
        /*
         * Enable support for Post Formats.
         *
         * See: https://codex.wordpress.org/Post_Formats
         */
        add_theme_support(
            'post-formats',
            [
                'aside',
                'image',
                'video',
                'quote',
                'link',
                'gallery',
                'audio',
            ]
        );
    }

    private function addMenuSupport(): void
    {
        add_theme_support('menus');

        // Register navigation menus
        register_nav_menus([
            'primary' => __('Primary Navigation', 'mill4'),
            'mobile' => __('Mobile Navigation', 'mill4'),
            'footer' => __('Footer Navigation', 'mill4'),
            'utility' => __('Utility Navigation', 'mill4'),
        ]);

        // Create and assign menus
        $this->ensureMenuExists('Primary Navigation', 'primary');
        $this->ensureMenuExists('Mobile Navigation', 'mobile');
        $this->ensureMenuExists('Footer Navigation', 'footer');
        $this->ensureMenuExists('Utility Navigation', 'utility');
    }

    private function ensureMenuExists(string $menuName, string $location): void
    {
        $menuExists = wp_get_nav_menu_object($menuName);

        if (! $menuExists) {
            $menuId = wp_create_nav_menu($menuName);

            // Set the menu location
            $locations = get_theme_mod('nav_menu_locations');
            $locations[$location] = $menuId;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }

    private function disableCommenting(): void
    {
        // Disable support for comments and trackbacks in post types
        $this->addAction('init', function () {
            foreach (get_post_types() as $post_type) {
                if (post_type_supports($post_type, 'comments')) {
                    remove_post_type_support($post_type, 'comments');
                    remove_post_type_support($post_type, 'trackbacks');
                }
            }
        });

        // Remove Comment Support from Admin Post Editor
        $this->addAction('admin_init', function () {
            foreach (get_post_types() as $post_type) {
                if (post_type_supports($post_type, 'comments')) {
                    remove_post_type_support($post_type, 'comments');
                    remove_post_type_support($post_type, 'trackbacks');
                }
            }
        });

        // Close existing comments
        $this->addFilter('comments_open', '__return_false', 20, 2);
        $this->addFilter('pings_open', '__return_false', 20, 2);

        // Remove comments from admin UI
        $this->addAction('admin_menu', function () {
            remove_menu_page('edit-comments.php');
        });

        $this->addAction('admin_menu', function () {
            remove_submenu_page('options-general.php', 'options-discussion.php');
        });

        $this->addAction('admin_bar_menu', function ($wp_admin_bar) {
            $wp_admin_bar->remove_node('comments');
        }, 999);

        $this->addAction('wp_before_admin_bar_render', function () {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('comments');
        });

        // hide comments metabox on dashboard
        $this->addAction('wp_dashboard_setup', function () {
            remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
        });

        // Remove comments template output
        $this->addFilter('comments_template', '__return_false', 20, 1);

        // Remove comment RSS feed.
        $this->removeAction('wp_head', 'feed_links_extra', 3);
        $this->addAction('do_feed_rdf', '__return_false', 1);
        $this->addAction('do_feed_rss', '__return_false', 1);
        $this->addAction('do_feed_rss2', '__return_false', 1);
        $this->addAction('do_feed_atom', '__return_false', 1);
        $this->addAction('do_feed_rss2_comments', '__return_false', 1);
        $this->addAction('do_feed_atom_comments', '__return_false', 1);

        // Block access to comment submission.
        $this->addAction('pre_comment_on_post', function () {
            wp_die('Comments are closed.', 'Comments Closed', ['response' => 403]);
        });

        // Disable pingbacks and trackbacks
        $this->addFilter('xmlrpc_methods', function ($methods) {
            unset($methods['pingback.ping']);

            return $methods;
        });
        $this->addFilter('pre_option_default_ping_status', '__return_false');
        $this->addFilter('pre_option_default_comment_status', '__return_false');

        // Remove comment-related widgets
        $this->addAction('widgets_init', function () {
            unregister_widget('WP_Widget_Recent_Comments');
        });
    }

    private function addSvgSupport(): void
    {
        add_filter('upload_mimes', function ($mimes) {
            $mimes['svg'] = 'image/svg+xml';

            return $mimes;
        });
    }
}
