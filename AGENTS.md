# AGENTS.md - AI Agent Reference Guide

This document provides essential information for AI coding assistants and other automated tools working with the Mill4 WordPress starter theme.

## Quick Reference

**Theme Type:** WordPress Starter Theme  
**Framework:** Built on [Millyard](https://github.com/imarc/millyard) framework  
**Templating:** Twig (via Timber)  
**PHP Version:** 7.2+  
**WordPress Version:** 4.6+  

## Key Documentation Files

- **[DEVELOPMENT_STANDARDS.md](DEVELOPMENT_STANDARDS.md)** - Comprehensive development standards and best practices
- **[README.md](README.md)** - Full theme documentation and feature overview

## Architecture Overview

### Directory Structure

```
app/
├── Blocks/          # Gutenberg block classes (use #[RegistersBlock] attribute)
├── Commands/       # WP-CLI command classes (use #[RegistersCommand] attribute)
├── Hooks/          # WordPress hooks classes (implement HooksInterface)
├── Http/
│   ├── Controllers/    # Route controllers
│   └── Middleware/     # Route middleware
├── Jobs/           # Background job classes (use #[RegistersJob] attribute)
├── PostTypes/      # Custom post type classes (use #[RegistersPostType] attribute)
├── Taxonomies/     # Custom taxonomy classes (use #[RegistersTaxonomy] attribute)
├── ViewComposers/  # View composer classes (use #[RegistersViewComposer] attribute)
├── AdminPages/     # Admin page classes (use #[RegistersAdminPage] attribute)
├── bootstrap.php   # Hook registration entry point
├── config.php      # Configuration file
├── helpers.php     # Helper functions
└── routes.php      # Custom route definitions

templates/
├── blocks/         # Block-specific Twig templates
├── partials/       # Reusable Twig partials
└── *.twig          # Page templates
```

## Registration Patterns

### Post Types
- **Location:** `app/PostTypes/`
- **Pattern:** Extend `PostType`, use `#[RegistersPostType]` attribute
- **Required:** `SLUG` constant, `$singularLabel`, `$pluralLabel`
- **Example:** See `app/PostTypes/Resource.php`

### Taxonomies
- **Location:** `app/Taxonomies/`
- **Pattern:** Extend `Taxonomy`, use `#[RegistersTaxonomy]` attribute
- **Required:** `SLUG` constant, `$singularLabel`, `$pluralLabel`, `$postTypes` array
- **Example:** See `app/Taxonomies/ResourceType.php`

### Blocks
- **Location:** `app/Blocks/`
- **Pattern:** Extend `Block`, use `#[RegistersBlock]` attribute
- **Required:** `NAME`, `TITLE`, `CATEGORY`, `ICON` constants
- **Template:** `templates/blocks/{block-name}.twig`
- **Example:** See `app/Blocks/HeroSection.php`

### View Composers
- **Location:** `app/ViewComposers/`
- **Pattern:** Extend `Composer`, use `#[RegistersViewComposer]` attribute
- **Required:** `$views` array, `withContext()` method
- **Important:** Use `{% render_partial %}` not `{% include %}` for partials
- **Example:** See `app/ViewComposers/FooterComposer.php`

### Hooks
- **Location:** `app/Hooks/`
- **Pattern:** Implement `HooksInterface`, use `RegistersHooks` trait
- **Required:** `initialize()` method, register in `app/bootstrap.php`
- **Example:** See `app/Hooks/PostTypeHooks.php`

## Important Patterns

### Twig Partials with View Composers
```twig
{# Correct - triggers view composers #}
{% render_partial 'footer.twig' %}

{# Incorrect - does NOT trigger view composers #}
{% include 'footer.twig' %}
```

### Cache Usage
The theme uses WordPress object cache (`wp_cache_get()`, `wp_cache_set()`). For production, recommend Redis Object Cache or Memcached Object Cache plugins.

### Helper Functions
Available in `app/helpers.php`:
- `config($key, $default)` - Get config value
- `env($key, $default)` - Get environment variable
- `cache_remember($key, $value, $ttl)` - Cache with remember pattern
- `cache_get($key)`, `cache_set($key, $value, $ttl)`, etc.

### Namespace
All custom classes use `App\` namespace (PSR-4 autoloaded from `app/` directory).

## Code Style

- **PHP:** PSR-12, enforced with PHP-CS-Fixer
- **File Naming:** PascalCase for PHP classes, kebab-case for Twig templates
- **Type Hints:** Required for method parameters and return types
- **Strict Types:** `declare(strict_types=1);` at top of PHP files

## Common Tasks

### Creating a New Post Type
1. Create class in `app/PostTypes/` extending `PostType`
2. Add `#[RegistersPostType]` attribute
3. Define `SLUG` constant and labels
4. Optionally use: `wp millyard make-post-type`

### Creating a New Block
1. Create class in `app/Blocks/` extending `Block`
2. Add `#[RegistersBlock]` attribute
3. Define required constants (`NAME`, `TITLE`, `CATEGORY`, `ICON`)
4. Create template in `templates/blocks/{block-name}.twig`
5. Optionally use: `wp millyard make-block`

### Adding a View Composer
1. Create class in `app/ViewComposers/` extending `Composer`
2. Add `#[RegistersViewComposer]` attribute
3. Define `$views` array with template names
4. Implement `withContext()` method

### Registering Hooks
1. Create class in `app/Hooks/` implementing `HooksInterface`
2. Use `RegistersHooks` trait
3. Implement `initialize()` method
4. Register in `app/bootstrap.php`

## Testing

- **Framework:** PHPUnit
- **Mocking:** Brain Monkey
- **WordPress Functions:** WorDBless
- **Commands:** `composer test`, `composer test:unit`, `composer test:integration`

## Build Process

- **Frontend:** Vite (Sass + JavaScript)
- **Commands:** `npm run build` (production), `npm run dev` (HMR)
- **HMR:** Only works in development mode (`WP_ENVIRONMENT_TYPE = 'development'`)

## Key Dependencies

- **Millyard:** Core framework (`imarc/millyard`)
- **Timber:** Twig templating for WordPress
- **League Container:** Dependency injection
- **Symfony HttpFoundation:** Request/response handling
- **Pronto:** UI component library

## Configuration

- **Config File:** `app/config.php` (accessed via `config()` helper)
- **Environment:** `.env` file in theme root (accessed via `env()` helper)
- **Bootstrap:** `app/bootstrap.php` (hook registration)

## Common Pitfalls

1. **Using `{% include %}` instead of `{% render_partial %}`** - View composers won't work
2. **Forgetting to register hooks in `bootstrap.php`** - Hooks won't initialize
3. **Missing attributes** - Post types, taxonomies, blocks won't auto-register
4. **Wrong namespace** - Should be `App\` not `Imarc\Millyard\`
5. **Not using object cache plugin** - Cache won't persist across requests

## Additional Resources

- **Millyard Framework:** https://github.com/imarc/millyard
- **Timber Documentation:** https://timber.github.io/docs/
- **Twig Documentation:** https://twig.symfony.com/doc/

---

**Last Updated:** See DEVELOPMENT_STANDARDS.md for the most current development guidelines.

