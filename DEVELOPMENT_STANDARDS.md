# Mill4 Development Standards

This document outlines the development standards and best practices for working with the Mill4 starter theme. Please follow these guidelines to ensure consistency and maintainability across the codebase.

## Table of Contents

- [Code Organization](#code-organization)
- [Custom Post Types](#custom-post-types)
- [Custom Taxonomies](#custom-taxonomies)
- [Gutenberg Blocks](#gutenberg-blocks)
- [View Composers](#view-composers)
- [Hooks](#hooks)
- [Routes](#routes)
- [Commands](#commands)
- [Jobs](#jobs)
- [Admin Pages](#admin-pages)
- [Code Style](#code-style)
- [Testing](#testing)
- [File Naming Conventions](#file-naming-conventions)
- [Helper Functions](#helper-functions)
- [Configuration](#configuration)
- [Environment Variables](#environment-variables)
- [Cache Usage](#cache-usage)

---

## Code Organization

The theme follows a structured, object-oriented approach. All custom code should be organized in the `app/` directory:

- `app/Blocks/` - Gutenberg block classes
- `app/Commands/` - WP-CLI command classes
- `app/Hooks/` - WordPress hooks classes
- `app/Http/Controllers/` - Route controllers
- `app/Http/Middleware/` - Route middleware
- `app/Jobs/` - Background job classes
- `app/PostTypes/` - Custom post type classes
- `app/Taxonomies/` - Custom taxonomy classes
- `app/ViewComposers/` - View composer classes
- `app/AdminPages/` - Admin page classes
- `templates/` - Twig template files
- `templates/blocks/` - Block-specific Twig templates
- `templates/partials/` - Reusable Twig partials

---

## Custom Post Types

### Registration

Custom post types should be registered using PHP classes that extend the `PostType` base class and use the `RegistersPostType` attribute. This ensures automatic registration through the theme's hook system.

**Location:** `app/PostTypes/`

**Example:**

```php
<?php

namespace App\PostTypes;

use Imarc\Millyard\Attributes\RegistersPostType;
use Imarc\Millyard\PostTypes\PostType;

#[RegistersPostType]
class Movie extends PostType
{
    public const SLUG = 'movie';

    public string $singularLabel = 'Movie';
    public string $pluralLabel = 'Movies';

    protected array $args = [
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'menu_icon' => 'dashicons-video-alt3',
    ];
}
```

### Key Requirements

1. **Use the `RegistersPostType` attribute** - This enables automatic discovery and registration
2. **Define a `SLUG` constant** - Use lowercase, hyphenated format (e.g., `'movie'`, `'resource'`)
3. **Set `singularLabel` and `pluralLabel`** - These are used for admin UI labels
4. **Override `$args` property** - Configure post type options (public, supports, menu_icon, etc.)
5. **Optional `$path` property** - Customize the archive URL path if different from the slug

### Scaffolding

You can use the WP-CLI command to scaffold a new post type:

```bash
wp millyard make-post-type
```

---

## Custom Taxonomies

### Registration

Custom taxonomies should be registered using PHP classes that extend the `Taxonomy` base class and use the `RegistersTaxonomy` attribute.

**Location:** `app/Taxonomies/`

**Example:**

```php
<?php

namespace App\Taxonomies;

use App\PostTypes\Movie;
use Imarc\Millyard\Attributes\RegistersTaxonomy;
use Imarc\Millyard\Taxonomies\Taxonomy;

#[RegistersTaxonomy]
class Genre extends Taxonomy
{
    public const SLUG = 'genre';

    public string $pluralLabel = 'Genres';
    public string $singularLabel = 'Genre';

    protected array $postTypes = [
        Movie::SLUG,
    ];

    protected bool $registersTopLevelMenuItem = false;
}
```

### Key Requirements

1. **Use the `RegistersTaxonomy` attribute** - Enables automatic registration
2. **Define a `SLUG` constant** - Use lowercase, underscore format (e.g., `'genre'`, `'resource_type'`)
3. **Set `singularLabel` and `pluralLabel`** - Used for admin UI labels
4. **Define `$postTypes` array** - Reference post type slugs using their class constants
5. **Control menu registration** - Use `$registersTopLevelMenuItem` to control admin menu placement

### Scaffolding

You can use the WP-CLI command to scaffold a new taxonomy:

```bash
wp millyard make-taxonomy
```

---

## Gutenberg Blocks

### Registration

Gutenberg blocks should be registered using PHP classes that extend the `Block` base class and use the `RegistersBlock` attribute.

**Location:** `app/Blocks/`

**Example:**

```php
<?php

namespace App\Blocks;

use Imarc\Millyard\Attributes\RegistersBlock;
use Imarc\Millyard\Blocks\Block;

#[RegistersBlock]
class HeroSection extends Block
{
    public const NAME = 'hero-section';
    public const TITLE = 'Hero Section';
    public const CATEGORY = 'section';
    public const ICON = 'cover-image';
    public const POST_TYPES = [];
    public const KEYWORDS = [];
}
```

### Key Requirements

1. **Use the `RegistersBlock` attribute** - Enables automatic registration
2. **Define required constants:**
   - `NAME` - Block name (kebab-case, matches template filename)
   - `TITLE` - Display title in block inserter
   - `CATEGORY` - Block category (e.g., 'section', 'common', 'formatting')
   - `ICON` - WordPress dashicon name
   - `POST_TYPES` - Array of post types where block is available (empty = all)
   - `KEYWORDS` - Array of search keywords for block inserter

### Block Templates

**Location:** `templates/blocks/`

Create a Twig template file named after the block's `NAME` constant:

```twig
{# templates/blocks/hero-section.twig #}

<section class="hero-section">
    <h1>{{ block.title }}</h1>
    <p>{{ block.description }}</p>
</section>
```

### Adding Custom Context

Override the `withContext()` method to pass additional data to the template:

```php
public function withContext(): array
{
    return [
        'custom_data' => get_field('some_field'),
        'computed_value' => $this->calculateSomething(),
    ];
}
```

### ACF Integration

ACF field data is automatically available in the `block` variable in your Twig template. Access fields using dot notation:

```twig
{{ block.title }}
{{ block.background_image.url }}
{{ block.cta_button.text }}
```

### Scaffolding

You can use the WP-CLI command to scaffold a new block:

```bash
wp millyard make-block
```

---

## View Composers

### Purpose

View composers allow you to inject data into Twig templates automatically. They're particularly useful for shared data across multiple templates or partials.

### Registration

View composers should extend the `Composer` base class and use the `RegistersViewComposer` attribute.

**Location:** `app/ViewComposers/`

**Example:**

```php
<?php

namespace App\ViewComposers;

use Imarc\Millyard\Attributes\RegistersViewComposer;
use Imarc\Millyard\Views\Composer;

#[RegistersViewComposer]
class FooterComposer extends Composer
{
    public array $views = [
        'footer.twig',
    ];

    public function withContext(): array
    {
        $social = get_field('social_media', 'option');

        return [
            'social_links' => $social['links'] ?? [],
        ];
    }
}
```

### Key Requirements

1. **Use the `RegistersViewComposer` attribute** - Enables automatic registration
2. **Define `$views` array** - List of template files this composer applies to
3. **Implement `withContext()` method** - Return array of data to inject

### Using with Partials

**Important:** When rendering partials that need view composer data, use `{% render_partial %}` instead of `{% include %}`. The standard `{% include %}` tag does not trigger the filter that view composers rely on.

```twig
{# Correct - triggers view composers #}
{% render_partial 'footer.twig' %}

{# Also supports passing additional context #}
{% render_partial 'footer.twig' with {
    'additional_data': 'value',
} %}

{# Incorrect - does NOT trigger view composers #}
{% include 'footer.twig' %}
```

---

## Hooks

### Registration

All WordPress hooks (actions and filters) should be organized in hook classes that implement `HooksInterface` and use the `RegistersHooks` trait.

**Location:** `app/Hooks/`

**Example:**

```php
<?php

namespace App\Hooks;

use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;

class MyHooks implements HooksInterface
{
    use RegistersHooks;

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'doSomething']);
        $this->addFilter('the_content', [$this, 'modifyContent'], 10, 1);
    }
    
    public function doSomething(): void
    {
        // Your logic here
    }

    public function modifyContent(string $content): string
    {
        return $content . '<p>Custom content</p>';
    }
}
```

### Key Requirements

1. **Implement `HooksInterface`** - Required interface
2. **Use `RegistersHooks` trait** - Provides helper methods for registering hooks
3. **Implement `initialize()` method** - Register all hooks here
4. **Register in `app/bootstrap.php`** - Add your hook class to the registrar

### Registering in Bootstrap

Add your hook class to `app/bootstrap.php`:

```php
$hooks->register(Hooks\MyHooks::class);
```

### Available Methods

The `RegistersHooks` trait provides:

- `addAction($hook, $callback, $priority = 10, $acceptedArgs = 1)` - Register an action
- `addFilter($hook, $callback, $priority = 10, $acceptedArgs = 1)` - Register a filter
- `removeAction($hook, $callback, $priority = 10)` - Remove an action
- `removeFilter($hook, $callback, $priority = 10)` - Remove a filter

### Best Practices

- Group related hooks in the same class
- Use descriptive class names (e.g., `AssetHooks`, `TemplateHooks`)
- Keep hook logic focused and single-purpose
- Prefer hook classes over adding hooks directly in `functions.php`

---

## Routes

### Registration

Custom routes are registered in `app/routes.php` using the Router instance.

**Location:** `app/routes.php`

**Example:**

```php
<?php

use App\Http\Controllers;
use App\Http\Middleware\VerifyCsrfToken;
use Imarc\Millyard\Routing\Router;

$router = Router::getInstance();
$router->setDefaultMiddleware([
    VerifyCsrfToken::class,
]);

$router->get('/api/example', Controllers\ExampleController::class);
$router->post('/api/submit', Controllers\SubmitController::class);
```

### Controllers

Controllers should extend the base `Controller` class and implement `__invoke()`:

**Location:** `app/Http/Controllers/`

**Example:**

```php
<?php

namespace App\Http\Controllers;

use Imarc\Millyard\Http\Controller;

class ExampleController extends Controller
{
    public function __invoke(): void
    {
        $this->render('example.twig', [
            'message' => 'Hello from route!',
        ]);
    }
}
```

### Middleware

Middleware classes should implement the middleware interface and handle request/response:

**Location:** `app/Http/Middleware/`

---

## Commands

### Registration

WP-CLI commands should extend the `Command` base class and use the `RegistersCommand` attribute.

**Location:** `app/Commands/`

**Example:**

```php
<?php

namespace App\Commands;

use Imarc\Millyard\Attributes\RegistersCommand;
use Imarc\Millyard\Commands\Command;

#[RegistersCommand]
class MyCommand extends Command
{
    protected string $name = 'my-command';
    protected string $shortDescription = 'Does something useful';

    public function __invoke($args, $assoc_args)
    {
        $this->line('Command executed!');
    }
}
```

### Usage

Run commands via WP-CLI:

```bash
wp millyard my-command
```

---

## Jobs

### Registration

Background jobs should extend the `Job` base class and use the `RegistersJob` attribute.

**Location:** `app/Jobs/`

**Example:**

```php
<?php

namespace App\Jobs;

use Imarc\Millyard\Attributes\RegistersJob;
use Imarc\Millyard\Jobs\Job;

#[RegistersJob]
class ProcessDataJob extends Job
{
    public function handle(): void
    {
        // Job logic here
    }
}
```

### Dispatching Jobs

Dispatch jobs programmatically:

```php
use App\Jobs\ProcessDataJob;
use Imarc\Millyard\Jobs\Dispatcher;

Dispatcher::dispatch(ProcessDataJob::class);
```

---

## Admin Pages

### Registration

Custom admin pages should extend the `AdminPage` base class and use the `RegistersAdminPage` attribute.

**Location:** `app/AdminPages/`

**Example:**

```php
<?php

namespace App\AdminPages;

use Imarc\Millyard\Attributes\RegistersAdminPage;
use Imarc\Millyard\AdminPages\AdminPage;

#[RegistersAdminPage]
class SettingsPage extends AdminPage
{
    public string $pageTitle = 'Settings';
    public string $menuTitle = 'Settings';
    public string $capability = 'manage_options';
    public string $menuSlug = 'my-settings';
}
```

---

## Code Style

### PHP Code Style

The theme uses PHP-CS-Fixer for code style enforcement. Configuration is in `.php-cs-fixer.php`.

**Check code style:**
```bash
composer cs-check
```

**Fix code style:**
```bash
composer cs-fix
```

### General Guidelines

- Follow PSR-12 coding standards
- Use type hints for all method parameters and return types
- Use property promotion in constructors when appropriate
- Use strict types: `declare(strict_types=1);` at the top of PHP files
- Use meaningful variable and method names
- Add PHPDoc comments for classes and public methods
- Keep methods focused and single-purpose

---

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run unit tests only
composer test:unit

# Run integration tests only
composer test:integration

# Generate coverage report
composer test:coverage
```

### Test Structure

- **Unit Tests:** `tests/Unit/` - Test individual classes and methods
- **Integration Tests:** `tests/Integration/` - Test WordPress integration

### Testing Tools

- **PHPUnit** - Testing framework
- **Brain Monkey** - Mocking framework for WordPress functions
- **WorDBless** - WordPress core functions without database

### Best Practices

- Write tests for new functionality
- Aim for high code coverage
- Use descriptive test method names
- Test edge cases and error conditions

---

## File Naming Conventions

### PHP Classes

- Use PascalCase: `HeroSection.php`, `ResourceType.php`
- Match class name to filename exactly
- One class per file

### Twig Templates

- Use kebab-case: `hero-section.twig`, `footer.twig`
- Block templates: `templates/blocks/{block-name}.twig`
- Partial templates: `templates/partials/{partial-name}.twig`
- Page templates: `templates/{template-name}.twig`

### Constants and Properties

- **Constants:** UPPER_SNAKE_CASE: `SLUG`, `NAME`, `TITLE`
- **Properties:** camelCase: `$singularLabel`, `$postTypes`
- **Private properties:** Use property promotion in constructors

---

## Helper Functions

### Available Helpers

The theme provides several helper functions in `app/helpers.php`:

- `config($key, $default = null)` - Get configuration value
- `env($key, $default = null)` - Get environment variable
- `cache()` - Get cache service instance
- `cache_remember($key, $value, $ttl = null)` - Remember value in cache
- `cache_get($key)` - Get value from cache
- `cache_set($key, $value, $ttl = null)` - Set value in cache
- `cache_forget($key)` - Remove value from cache
- `cache_flush()` - Clear all cache
- `is_hmr()` - Check if HMR is active
- `response($content, $status, $headers)` - Create HTTP response
- `json_response($data, $status, $headers)` - Create JSON response
- `csrf_token()` - Get CSRF token
- `csrf_token_key()` - Get CSRF token key

### Usage Examples

```php
// Configuration
$value = config('sessions.enabled');

// Environment
$apiKey = env('API_KEY', 'default-value');

// Cache
cache_remember('my-key', function() {
    return expensive_operation();
}, 3600);
```

---

## Configuration

### Configuration File

Configuration is stored in `app/config.php` and accessed via the `config()` helper.

**Example:**

```php
// app/config.php
return [
    'api' => [
        'key' => env('API_KEY'),
        'timeout' => env('API_TIMEOUT', 30),
    ],
];

// Usage
$apiKey = config('api.key');
$timeout = config('api.timeout', 30);
```

### Best Practices

- Use environment variables for sensitive data
- Provide sensible defaults
- Group related settings
- Document configuration options

---

## Environment Variables

### Loading Environment Variables

Environment variables are loaded from a `.env` file in the theme root (if it exists).

### Common Variables

- `VITE_HOST` - Vite dev server host (default: `http://localhost:5173`)
- `VITE_MANIFEST_PATH` - Path to Vite manifest
- `VITE_DIST_PATH` - Path to Vite dist directory
- `SESSIONS_ENABLED` - Enable session support
- `CACHE_TTL` - Default cache TTL in seconds

### Accessing Variables

Use the `env()` helper function:

```php
$value = env('MY_VARIABLE', 'default-value');
```

---

## Cache Usage

### Cache Service

The theme includes a cache service for storing and retrieving data.

### Methods

```php
// Remember a value (get or compute)
$value = cache_remember('key', function() {
    return expensive_operation();
}, 3600);

// Get a value
$value = cache_get('key');

// Set a value
cache_set('key', $value, 3600);

// Remove a value
cache_forget('key');

// Clear all cache
cache_flush();
```

### Object Caching Plugins

**Important:** Millyard's caching service uses WordPress's native `wp_cache_get()` and `wp_cache_set()` functions under the hood. For production environments, especially those with multiple servers or high traffic, it's strongly recommended to install an object caching plugin to ensure that cached data persists across multiple requests and is shared across all server instances.

Recommended object caching plugins:

- **[Redis Object Cache](https://wordpress.org/plugins/redis-cache/)** - A persistent object cache backend powered by Redis. Supports Predis, PhpRedis (PECL), Relay, replication, sentinels, and clustering.
- **[Memcached Object Cache](https://wordpress.org/plugins/memcached/)** - A persistent object cache backend powered by Memcached.

Without an object caching plugin, WordPress will use the default in-memory object cache, which means:
- Cache data is lost between page requests
- Cache is not shared across multiple server instances
- Performance benefits are limited to single request cycles

With an object caching plugin installed, the theme's cache helpers will automatically use the persistent cache backend, providing true cross-request caching and improved performance.

### Best Practices

- Cache expensive operations (database queries, API calls)
- Set appropriate TTL values
- Use descriptive cache keys
- Clear cache when data changes
- Consider cache invalidation strategies

---

## Additional Resources

- **Theme README:** See `README.md` for more detailed documentation
- **Millyard Framework:** The theme is built on [Millyard](https://github.com/imarc/millyard)
- **Timber Documentation:** [Timber Docs](https://timber.github.io/docs/)
- **Twig Documentation:** [Twig Docs](https://twig.symfony.com/doc/)

---

## Questions?

If you have questions about these standards or need clarification, please reach out to the project maintainer.

