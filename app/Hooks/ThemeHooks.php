<?php

namespace App\Hooks;

use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Contracts\HooksInterface;

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
        $this->addPostFormatSupport();
        $this->addMenuSupport();
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
            array(
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            )
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
            array(
                'aside',
                'image',
                'video',
                'quote',
                'link',
                'gallery',
                'audio',
            )
        );
    }

    private function addMenuSupport(): void
    {
        add_theme_support('menus');

        // Register navigation menus
        register_nav_menus([
            'primary' => __('Primary Navigation', 'mill4'),
            'footer' => __('Footer Navigation', 'mill4'),
        ]);

        // Create and assign menus
        $this->ensureMenuExists('Primary Navigation', 'primary');
        $this->ensureMenuExists('Footer Navigation', 'footer');
    }

    private function ensureMenuExists(string $menuName, string $location): void
    {
        $menuExists = wp_get_nav_menu_object($menuName);

        if (!$menuExists) {
            $menuId = wp_create_nav_menu($menuName);

            // Set the menu location
            $locations = get_theme_mod('nav_menu_locations');
            $locations[$location] = $menuId;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
}
