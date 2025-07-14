# Mill 4 Starter Theme

**Mill 4** is a WordPress starter theme built on top of the [Timber starter theme](https://timber.github.io/docs/v2/installation/installation/#use-the-starter-theme), which provides a no-frills starting point for building a WordPress theme with Timber.

In addition to the Twig templating that Timber provides, **Mill 4** includes the following features:

* PHP League's [container](https://container.thephpleague.com/) package for dependency injection.
* Symfony's [HttpFoundation](https://symfony.com/doc/current/components/http_foundation.html) package for convenient request and response handling.
* [DotEnv](https://github.com/vlucas/phpdotenv) for loading environment variables.
* A router for handling custom GET/POST/PUT/DELETE routes.
* An object-oriented hooks system for registering actions and filters.
* An object-oriented interfaces for registering custom post types, taxonomies, Gutenberg blocks, admin pages and more!
* A system for creating jobs and scheduling them and other recurring events via WP-Cron.
* View composers for adding custom context data to your Twig templates.
* [Imarc's Pronto component library](https://github.com/imarc/pronto) for consistent UI across the site.
* A basic front-end build process for compiling Sass and JavaScript using Vite.

Let's get into it.

## Table of Contents

- [Installation](#installation)
- [Frontend Build](#frontend-build)
- [Hooks](#hooks)
- [Configuration](#configuration)
- [Custom Routes](#custom-routes)
- [Custom Post Types](#custom-post-types)
- [Custom Taxonomies](#custom-taxonomies)
- [Gutenberg Blocks](#gutenberg-blocks)
- [View Composers](#view-composers)
- [Commands](#commands)
- [Jobs](#jobs)
- [Recurring Events](#scheduling-recurring-events-via-wp-cron)
- [Admin Pages](#admin-pages)
- [Cache](#cache)
- [Pantheon + GitHub Actions Workflow](#pantheon--github-actions-workflow)

## Installation

Best way to go about installing this thing is probably to just download the zip file and drop it into your `wp-content/themes` directory. Then you'll want to rename the folder to your own slug and edit the details in the `style.css` file.

Next, `cd` to the theme folder and run the following:

```bash
% composer install
% npm install
% npm run build
```

This will install the dependencies and compile the assets. The primary dependency is [Millyard](https://github.com/imarc/millyard), which provides most ofthis theme's core functionality.

Finally, you'll want to activate the theme in the WordPress admin.

## Frontend Build

Mill 4 includes a basic frontend build process for compiling Sass and JavaScript using Vite. To compile the assets, run the following command:

```bash
% npm run build
```

This will compile the Sass and JavaScript files and place them in the `dist` directory.

### Hot Module Replacement

Mill 4 includes a basic hot module replacement (HMR) setup for the frontend build. This is enabled when you run the following command:

```bash
% npm run dev
```

Your browser will automatically refresh when you make changes to the Sass or JavaScript files. **Note: HMR is only available when running WordPress in development mode.** This can be controlled by setting the `WP_ENVIRONMENT_TYPE` constant in your `wp-config-local.php` file or equivalent:

```php
<?php
// wp-config-local.php

define('WP_ENVIRONMENT_TYPE', 'development');
```

HMR is configured by default to use `http://localhost:5173` for serving assets. If you alter the `vite.config.js` file to change this default, you'll also need to set the `VITE_HOST` var in your theme's `.env` file. Also available are `VITE_MANIFEST_PATH` and `VITE_DIST_PATH` vars for customizing the manifest and dist paths, respectively. Consult `app/config.php` to view the default values.

When running `npm run dev`, you'll still be using the normal URL for your development environment when viewing the site in your browser, since vite is configured to only have assets served up by Node.

## Hooks

Mill 4 includes a basic hooks system for registering actions and filters. While it's not strictly necessary to use it (there's nothing stopping you from registering your own actions and filters in the theme's `functions.php` file), it's a handy way to organize your code.

Take a look at the `app/Hooks` directory to see the included hooks classes. To create your own Hooks class, create a new class that implements `Imarc\Millyard\Contracts\HooksInterface` and register it in the `app/bootstrap.php` file. Here's an example:

```php
// app/Hooks/MyHooks.php

namespace App\Hooks;

use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;

class MyHooks implements HooksInterface
{
    use RegistersHooks;

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'doSomething']);
    }
    
    public function doSomething(): void
    {
        // ... your logic here
    }
}
```

The `RegistersHooks` trait provides a few helpful methods for registering actions and filters:

* `addAction($hook, $callback, $priority = 10, $acceptedArgs = 1)`: Registers an action.
* `addFilter($hook, $callback, $priority = 10, $acceptedArgs = 1)`: Registers a filter.
* `removeAction($hook, $callback, $priority = 10)`: Removes an action.
* `removeFilter($hook, $callback, $priority = 10)`: Removes a filter.

There's nothing stopping you from using WP's native `add_action()` and `add_filter()` functions, but using the trait's methods could come in handy down the line.

Then, you'll want to register the hooks in the `app/bootstrap.php` file.

```php
// app/bootstrap.php

use App\Hooks;
use Imarc\Millyard\Services\Container;

$container = new Container();
$hooks = $container->get(Hooks\Registrar::class);
$hooks->register(Hooks\MyHooks::class);
```

## Configuration

Mill 4 includes a basic system for loading configuration settings. The settings are stored in the `app/config.php` file, and you can access them anywhere in your theme using the `config()` helper function. `config()` accepts a dot-notation path to the setting you'd like to access, and a default value if the setting is not found. For example:

```php
// app/config.php

return [
    'sessions' => [
        'enabled' => env('SESSIONS_ENABLED', false),
    ],
];

// elsewhere in your theme...

echo config('sessions.enabled');
// false

echo config('sessions.foo', 'bar');
// "bar"
```

The `env()` helper function is used to access environment variables, either set in your theme's `.env` file or through the `$_ENV` superglobal. And like `config()`, it also accepts a default value if the variable is not found. A good rule of thumb is to exclusively access environment variables through the `config()` helper function.

## Custom Routes

Custom routes are registered in `app/routes.php`. You can use either a closure, an invokable controller class, or a specific class method to handle the route.

```php
<?php
// app/routes.php

use App\Http\Controllers\SubmitContactFormAction;

$router->get('/foo', function () {
    return 'Hello world!';
});

$router->post('/foo/submit', '\App\Http\Controllers\FooController@submit');

$router->post('/contact/submit', SubmitContactFormAction::class);
```

*Note: The logic for custom routes depends on WordPress's native rewrite rules, which are cached. If you add, edit, or remove routes, you may need to flush the rewrite rules. You can do this by visiting the **Permalinks Settings** page in the WordPress admin and clicking the "Save Changes" button.*

*There's a handy command in the theme to flush the rewrite rules, so you can just run `wp millyard flush-rewrite-rules` to flush the rules when needed.*

### Dependency Injection

Your route actions can accept dependencies as parameters. These dependencies will be automatically resolved from the service container. For example:

```php
// app/routes.php

use App\Services\MyService;

$router->get('/foo', function (MyService $service) {
    $service->doSomething();
});
```

The router will also resolve dependencies for controller actions.

### Route Parameters

The router supports parameters! Just place parameter names in curly braces and the values will be passed to the route action as parameters. Here's how it works...

```php
// app/routes.php

$router->get('/foo/{bar}', function ($bar) {
    return $bar;
});
```

You may mix parameters and dependencies in the same route action. Just ensure that the parameters are listed before any dependencies in the action signature. For example:

```php
// app/routes.php

$router->get('/foo/{bar}', function (string $bar, MyService $service) {
    $service->doSomething($bar);

    return 'success!';
});
```

The injection of parameters into route actions supports casting to the following types:

* `int`
* `string`
* `bool`
* `float`
* `array`

### Middleware

Mill 4 includes a basic middleware system for injecting custom logic into the request lifecycle of custom routes. Middleware classes must implement the `MiddlewareInterface`. For example:

```php
// app/Middleware/VerifyCsrfToken.php

namespace App\Middleware; 

use Imarc\Millyard\Contracts\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        // custom logic...

        return $next($request);
    }
}
```

Then you can attach the middleware to a route by passing single middleware class or an array of middleware classes to the `middleware()` method.

```php
// app/routes.php
...

use App\Middleware\VerifyCsrfToken;
use App\Http\Actions\SubmitContactFormAction;

$router->post('/foo', SubmitContactFormAction::class)
    ->middleware(VerifyCsrfToken::class);
```

You can also define the default middleware for the router by calling the `setDefaultMiddleware()` method.

```php
// app/routes.php
...

$router->setDefaultMiddleware([VerifyCsrfToken::class]);
$router->post('/foo', SubmitContactFormAction::class)
```

### Request Object

Mill 4 includes [Symfony's HttpFoundation](https://symfony.com/doc/current/components/http_foundation.html) component, which, among other things, provides a handy `Request` object. You can access this object in your route actions by declaring a parameter of type `Request` in the action signature. For example:

```php
// app/routes.php

use Symfony\Component\HttpFoundation\Request;

$router->get('/foo', function (Request $request) {
    print_r($request->query->all());
});

```

### Responses

Mill 4 includes helpers for returning a variety of responses.

#### JSON Responses

The `json_response()` function is a shortcut for returning a JSON response. It uses Symfony's `JsonResponse` class behind the scenes. It takes an array of data, an optional status code, and an array of headers. For example:

```php
// app/routes.php

$router->get('/foo', function() {
    return json_response(['message' => 'Hello from FooController!']);
});

```

#### HTML Responses

The `response()` function is a shortcut for returning an HTML response. It uses Symfony's `Response` class behind the scenes. It takes a string of content, an optional status code, and an array of headers. For example:

```php
// app/routes.php

$router->get('/foo', function() {
    return response('Hello from FooController!');
});

```

#### Twig Template Responses

Mill 4's base controller class includes a `render()` method that renders a Twig template and returns the rendered HTML. For example:

```php
// app/Http/Controllers/FooController.php

use Imarc\Millyard\Http\Controller;

class FooController extends Controller
{
    public function __invoke(): void
    {
        $this->render('foo.twig', ['message' => 'Hello from FooController!']);
    }
}

```

```twig
<!-- templates/foo.twig -->

{% extends "base.twig" %}

{% block content %}
    <h1>{{ message }}</h1>
{% endblock %}
```

## Custom Post Types

To register a custom post type in your theme, create a new class for your custom post type in the `app/PostTypes` directory. This class should extend the `PostType` class provided by Millyard and use the `RegistersPostType` attribute. For example:

```php
<?php

// app/PostTypes/Movie.php

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
    ];
}
```

### Scaffolding a Custom Post Type

Millyard provides a scaffolding command for creating new post types. You can run `wp millyard make post-type` to create a new post type. This will create a new class in the `app/PostTypes` directory and register it with the theme.

```bash
wp millyard make post-type
```

## Custom Taxonomies

To register a custom taxonomy in your theme, create a new class for your custom taxonomy in the `app/Taxonomies` directory. Your class should extend the `Taxonomy` class provided by Millyard and use the `RegistersTaxonomy` attribute. For example:

```php
<?php

// app/Taxonomies/Genre.php

namespace App\Taxonomies;

use Imarc\Millyard\Attributes\RegistersTaxonomy;
use Imarc\Millyard\Taxonomies\Taxonomy;
use App\PostTypes\Movie;

#[RegistersTaxonomy]
class Genre extends Taxonomy
{
    public const SLUG = 'genre';

    public string $pluralLabel = 'Genres';

    public string $singularLabel = 'Genre';

    protected array $postTypes = [
        Movie::SLUG,
    ];
}
```

Adding the `RegistersTaxonomy` attribute to the class will automatically register the taxonomy with the theme (this happens in the `TaxonomyHooks` class).

### Scaffolding a Custom Taxonomy

Millyard provides a scaffolding command for creating new taxonomies. You can run `wp millyard make taxonomy` to create a new taxonomy. This will create a new class in the `app/Taxonomies` directory and register it with the theme.

```bash
wp millyard make taxonomy
```

## Gutenberg Blocks

To register a Gutenberg block in your theme, follow these steps:

1. **Create a Gutenberg Block Class**:
   Create a new class for your Gutenberg block. This class should extend the `Block` class provided by Millyard. It should live in the `app/Blocks` directory and use the `RegistersBlock` attribute. For example:

   ```php
    <?php

    // app/Blocks/GenericCtaBlock.php

    namespace App\Blocks;

    use Imarc\Millyard\Attributes\RegistersBlock;
    use Imarc\Millyard\Blocks\Block;

    #[RegistersBlock]
    class GenericCtaBlock extends Block
    {
        public const NAME = 'generic-cta-block';
        public const TITLE = 'Generic CTA Block';
        public const CATEGORY = 'section';
        public const ICON = 'admin-comments';
        public const POST_TYPES = [];
        public const KEYWORDS = [];
    }
    ```
    Adding the `RegistersBlock` attribute to the class will automatically register the block with the theme (this happens in the `BlockHooks` class).

2. **Create a Gutenberg Block Template**:
   Create a new twig template file for your block. This file should be named after the block name and placed in the `templates/blocks` directory. For example:

   ```php
    // templates/blocks/generic-cta-block.twig

    {% extends "base.twig" %}

    {% block content %}
        <div class="generic-cta-block">
            <h2>{{ block.title }}</h2>
        </div>
    {% endblock %}
    ```
    Any ACF data associated with the block will automatically be included in a template variable called `block`. If you'd like to pass any additional context to the template, you may override the withContext() method in your block class:

    ```php
    //  app/Blocks/GenericCtaBlock.php

    public function withContext(): array
    {
        return [
            'foo' => 'bar',
        ];
    }

    // templates/blocks/generic-cta-block.twig

    {% extends "base.twig" %}

    {% block content %}
        <div class="generic-cta-block">
            <h2>{{ block.title }}</h2>
            <p>{{ foo }}</p> <!-- This is the foo context variable -->
        </div>
    {% endblock %}  
    ```

### Scaffolding a Gutenberg Block

Millyard provides a scaffolding command for creating new Gutenberg blocks. You can run `wp millyard make block` to create a new block. This will create a new class in the `app/Blocks` directory as well as a twig template in the `templates/blocks` directory and register it with the theme.

```bash
wp millyard make block
```

## View Composers

Mill 4 includes a basic view composer system for adding custom context data to your Twig templates. To create a new view composer, create a new class for it in the `app/ViewComposers` directory. Your class should extend the `Composer` class provided by Millyard and use the `RegistersViewComposer` PHP attribute. For example:

```php
<?php

// app/ViewComposers/FooComposer.php

namespace App\ViewComposers;

use Imarc\Millyard\Attributes\RegistersViewComposer;
use Imarc\Millyard\Views\Composer;

#[RegistersViewComposer]
class FooComposer extends Composer
{
    // The views that the composer should be applied to.
    public array $views = [
        'index.twig',
    ];

    // The context data to add to the view.
    public function withContext(): array
    {
        $currentContextData = $this->getContextData();
        
        return [
            'foo' => 'bar',
        ];
    }
}
```

Adding the `RegistersViewComposer` attribute to the class will automatically register the view composer with the theme (this happens in the `TemplateHooks` class).

Now your view composer will be applied to the specified views.

**NOTE:** If you'd like to render a partial template, you can use Mill4's `{% render_partial %}` tag. This is necessary because the standard `{% include %}` tag does not trigger the `timber/render/data` filter that view composers rely on. For example:

```twig
{% render_partial 'footer.twig' %}
```

`{% render_partial %}` also supports passing additional context data to the partial just like `{% include %}` does. For example:

```twig
{% render_partial 'footer.twig' with {
    'foo': 'bar',
} %}
```

## Commands

Mill 4 includes a basic command system for running custom commands.

To create a new command, create a new class for it. This class should extend the `Command` class provided by Millyard. Make sure you add a `__invoke()` method to your command class for standalone commands. For example:

```php
<?php
// app/Commands/FooCommand.php

namespace App\Commands;

use Imarc\Millyard\Attributes\RegistersCommand;
use Imarc\Millyard\Commands\Command;

#[RegistersCommand]
class FooCommand extends Command
{
    protected string $name = 'foo';

    protected string $shortDescription = 'Prints a message to the console';

    public function __invoke($args, $assoc_args)
    {
        $this->line('Hello from FooCommand!');
    }
}
```

The `RegistersCommand` attribute will automatically register the command with the theme (this happens in the `CommandHooks` class).

Alternatively, if you'd like namespaced commands, you can do this by creating a single command class without an `__invoke()` method. Then, each public method in the class will be treated as a subcommand. For example:

```php
// app/Commands/FooCommand.php

class FooCommand extends Command
{
    public function bar()
    {
        $this->line('Hello from FooCommand!');
    }
}
```

Now you can run `wp foo bar` to execute the `bar` method. Running simply `wp foo` will return a list of available subcommands.

If you'd like the subcommand's name to be different from the method name, you can set the `@subcommand` attribute on the method. For example:

```php
// app/Commands/FooCommand.php

class FooCommand extends Command
{
    /**
     * @subcommand my-great-command
     */
    public function myGreatCommand()
    {
        $this->line('Hello from FooCommand!');
    }
}
```

Now your command should be available to run using WordPress' `wp` command.

Mill 4's base Command class provides a few helpful methods:

* `line($message)`: Prints a message to the console.
* `success($message)`: Prints a success message to the console.
* `error($message)`: Prints an error message to the console.
* `warning($message)`: Prints a warning message to the console.
* `confirm($question, $assoc_args)`: Asks the user to confirm an action.

These commands align with those available in [WP-CLI's API](https://make.wordpress.org/cli/handbook/references/internal-api/).

## Jobs

Mill 4 includes a basic job system for running custom jobs. These jobs can be scheduled to run at a specific time or immediately, and can be configured to use the WordPress cron system.

To create a new job, create a new class for your job in the `app/Jobs` directory. This class should extend the `Job` class provided by Millyard and use the `RegistersJob` attribute. For example:

```php
<?php
// app/Jobs/MyGreatJob.php

namespace App\Jobs;

use Imarc\Millyard\Attributes\RegistersJob;
use Imarc\Millyard\Jobs\Job;

#[RegistersJob]
class MyGreatJob extends Job
{
    public function handle(): void
    {
        die('I did a thing!');
    }
}
```

Adding the `RegistersJob` attribute to the class will automatically register the job with the theme (this happens in the `JobHooks` class).

Now your job should be available to be dispatched.

```php
MyGreatJob::dispatch()
    ->at('2025-03-29 12:00:00')
    ->execute();
```

This will schedule the job to run at the specified time. By default, jobs are executed via the WordPress cron system. If you'd like to execute the job immediately and bypass the cron system, you can use pass `false` as the argument to the `execute()` method.

```php
MyGreatJob::dispatch()
    ->execute(false);
```

As with other parts of Mill 4, Jobs support dependency injection, so feel free to add a `__construct()` method to your job class and inject any dependencies you need.

```php
<?php
// app/Jobs/MyGreatJob.php

namespace App\Jobs;

use Imarc\Millyard\Attributes\RegistersJob;
use App\Services\Logger;

#[RegistersJob]
class MyGreatJob extends Job
{
    public function __construct(private Logger $logger)
    {
    }
}
```

Finally, you can pass arguments to the job when it is dispatched.

```php
MyGreatJob::dispatch('bar')
    ->now()
    ->execute();
```

The arguments will be passed to the job's `handle()` method as parameters:

```php  
// app/Jobs/MyGreatJob.php

    public function handle(?string $foo = null): void
    {
        die('MyGreatJob ' . $foo);
    }
```

## Scheduling Recurring Events via WP-Cron

Mill 4 includes functionality for easily scheduling recurring events via WP-Cron. The scheduling of event is handled in the `registerCronJobs()` method of the `CronHooks` class:

```php
// app/Hooks/CronHooks.php

class CronHooks implements HooksInterface
{
    public function registerCronJobs(): void
    {
        $this->schedule('my_great_event', 'hourly', function () {
            echo 'Success!';
        });
    }
}
```
The `schedule()` method accepts three arguments:

1. The name of the event.
2. The recurrence of the event (hourly, twicedaily, daily, weekly)
3. A callback function.
4. An optional timestamp to schedule the first occurrence of the event at a specific time.

Alternatively, if your event's logic is already encapsulated in a job class, you can schedule the job instead:

```php
// app/Hooks/CronHooks.php

use App\Jobs\MyGreatJob;

class CronHooks implements HooksInterface
{
    public function registerCronJobs(): void
    {
        $this->scheduleJob(MyGreatJob::class, 'daily');
    }
}
```

## Admin Pages

Mill 4 includes a basic system for creating custom admin pages.

To create an admin page, create a new class for it in the `app/AdminPages` directory. Your class should extend the `AdminPage` class provided by Millyard, and use the `RegistersAdminPage` attribute.

There are quite a few properties that you can set on the class to customize the page's location, appearance and behavior. For example:

```php
<?php
// app/AdminPages/LogViewerPage.php

namespace App\AdminPages;

use Imarc\Millyard\Attributes\RegistersAdminPage;
use Imarc\Millyard\AdminPages\AdminPage;

#[RegistersAdminPage]
class LogViewerPage extends AdminPage
{
    protected string $slug = 'logs';

    protected string $title = 'Log Viewer';

    protected string $capability = 'manage_options';

    protected int $menuPosition = 10;

    protected string $icon = 'dashicons-admin-tools';

    protected ?string $template = 'admin/log-viewer.twig';

    protected string $parentSlug = 'options-general.php';

    public function withContext(): array
    {
        return [
            'logs' => 'foo',
        ];
    }
}
```

Adding the `RegistersAdminPage` attribute to the class will automatically register the admin page with the theme (this happens in the `AdminPageHooks` class).

If parent slug is set, the admin page will be added as a submenu page to the parent page you specify (and the $icon property will be ignored).

The `withContext()` method is used to pass any context data you'd like to pass to the template. 

Now your admin page should be available in the WordPress admin.

## Cache

Mill 4 includes a developer-friendly caching service that acts as a wrapper around WordPress's native caching functions. Like everything else in Mill 4, the cache is designed to be used as a service, so you can inject it into your classes using dependency injection.

```php
<?php
// app/Http/Controllers/MyController.php

use Imarc\Millyard\Services\Cache;

class MyController extends Controller
{
    public function __invoke(Cache $cache): void
    {
        $cache->remember('featured-posts', function () {
            return get_posts([
                'post_type' => 'post',
                'posts_per_page' => 10,
                'orderby' => 'date',
                'order' => 'DESC',
                'post_status' => 'publish',
                'meta_query' => [
                    [
                        'key' => 'featured',
                        'value' => '1',
                        'compare' => '=',
                    ],
                ],
            ]);
        }, 60);
    }

}
```
 * `remember($key, $value, $ttl = null)`: Remember a value in the cache. The first argument is the  cache key, the second is the value to cache, and the third is the TTL (time to live) in  seconds. If the value is already cached, it will be returned immediately. This value can be a callable, in which case the result of the callback will be cached.
 * `forget($key)`: Forget a value from the cache.
 * `flush()`: Flush the cache.
 * `get($key)`: Get a value from the cache.
 * `set($key, $value, $ttl = null)`: Set a value in the cache.

The cache's default TTL is 24 hours, and can be changed by setting `CACHE_TTL` in your `.env` file. The value is represented in seconds.

**NOTE:** It's worth noting that since we're using `wp_cache_get()` and `wp_cache_set()` under the hood, *the cache will only be available to the current request by default*. It's highly recommended that you install a plugin like [Redis Object Cache](https://wordpress.org/plugins/redis-cache/) or [Memcached Object Cache](https://wordpress.org/plugins/memcached/). Once either of those are installed, the cached data will persist across requests.

Mill 4 also includes a helper function for each of the cache methods, so you can use them in your theme without having to inject the cache service.

 * `cache_remember($key, $value, $ttl = null)`: Remember a value in the cache. Once again, `$value` can be a callable, in which case the result of the callback will be cached.
 * `cache_forget($key)`: Forget a value in the cache.
 * `cache_flush()`: Flush the cache.
 * `cache_get($key)`: Get a value from the cache.
 * `cache_set($key, $value, $ttl = null)`: Set a value in the cache.
    
## Pantheon + GitHub Actions Workflow

Mill 4 works pretty well on Pantheon using their composer-based upstream, but there are a few gotchas to be aware of. Here's how to get it working:

1. Create a new site on Pantheon using the wordpress composer upstream. You'll need to get [Terminus](https://docs.pantheon.io/terminus) set up beforehand. To create the site, run the following command:

    ```bash
    % terminus site:create --org imarc --region us -- [site-slug] "[site-title]" wordpress-composer-managed
    ```

1. After the site is created, you'll want to hit the dev url (you'll find it in the Pantheon dashboard) to complete the initial WordPress installation. You'll be prompted to create a user.

1. Clone the site from the Pantheon repository to your local machine. The repository address is available in the Pantheon dashboard.

1. Create a new GitHub repository for the site.

1. Update the origin remote to point to the new GitHub repository.

    ```bash
    % git remote set-url origin [github-repository-url]
    ```
    Alternatively, you could just remove the `.git` directory and run `git init` to create a new repository.

1. Download the zip for this theme and unpack it into `wp/app/themes/`. Rename the theme directory and update the information in the `style.css` file.

1. ACF Pro is required for the theme to work and is not available to install via Composer. You'll need to download the plugin from the [Advanced Custom Fields website](https://www.advancedcustomfields.com/pro/) and upload it to the `wp/app/mu-plugins` directory.

    Then, create an autoloader for the plugin by adding a file named `acf-pro-autoloader.php` to the `wp/app/mu-plugins` directory:
    ```php
    <?php

    // wp/app/mu-plugins/acf-pro-autoloader.php

    require_once __DIR__ . '/advanced-custom-fields-pro/acf.php';

    ```

1. Pantheon will only run `composer install` for the `composer.json` file in the project root. So, you'll need to move the dependencies in the theme's `composer.json` over to the top-level one. As a result, your `composer.json` will look something like this:
    ```json
    "require": {
        "php": ">=7.4",
        "composer/installers": "^2.2",
        "vlucas/phpdotenv": "^5.5",
        "oscarotero/env": "^2.1",
        "roots/bedrock-autoloader": "*",
        "roots/bedrock-disallow-indexing": "*",
        "roots/wordpress": "*",
        "roots/wp-config": "*",
        "roots/wp-password-bcrypt": "*",
        "wpackagist-theme/twentytwentytwo": "^1.2",
        "pantheon-systems/pantheon-mu-plugin": "*",
        "pantheon-upstreams/upstream-configuration": "dev-main",
        "wpackagist-plugin/pantheon-advanced-page-cache": "*",
        "wpackagist-plugin/wp-native-php-sessions": "*",
        "cweagans/composer-patches": "^1.7",
        "imarc/millyard": "dev-main" // <--- add millyard dependency
    },

    ...

    "autoload": {
        "classmap": ["upstream-configuration/scripts/ComposerScripts.php"],
        "psr-4": {
            "App\\": "web/app/themes/<your-theme>/app/"
        },
        "files": ["web/app/themes/<your-theme>/app/helpers.php"]
    },
    ```

    Feel free to remove your theme's `composer.json` file entirely, since you'll be instead using the one in the project root.

    Also, you'll need to update the `functions.php` file for the theme to remove the Composer vendor autoloading. Since the theme's `composer.json` file is not being used on Pantheon, the autoloader will not be available.

    ```php
    // wp/app/themes/<your-theme>/functions.php

    require_once __DIR__ . '/vendor/autoload.php'; // <--- remove this line
    ```

1. Add the following line to the `.gitignore` under `web/app/mu-plugins/*/` to ensure that ACF Pro is not ignored:
    ```
    !/web/app/mu-plugins/advanced-custom-fields-pro/
    ```

1. Duplicate the `.gitignore` file in the project root and name it `.gitignore.pantheon`. We do this to distinguish between files that should be ignored on Pantheon and those that should be ignored on GitHub.
    ```
    % cp .gitignore .gitignore.pantheon
    ```

1. Run `composer install` to install the dependencies.

1. Run `npm install && npm run build` from the theme directory to install the dependencies and compile the assets for your theme.

1. Update the `.gitignore` file accordingly:
    * Remove the entry for `pantheon.upstream.yml`. It's critical that this file is committed to the repository, or else the site will not be able to deploy.
    * Add the following entries for your theme and ACF Pro:
        ```
        /web/app/themes/<your-theme>/.hot
        /web/app/themes/<your-theme>/node_modules/
        /web/app/themes/<your-theme>/dist/*
        ```

1. Remove the `.gitignore` from the theme, since that's now being handled in the project root's `.gitignore` file.

1. Push the changes to GitHub.
    ```bash
    % git push origin master
    ```

1. Set up the GitHub Actions Workflow. Create a new file in the `.github/workflows` directory called `deploy.yml`. Add the following content to the file, swapping out the `<your-theme>` placeholder with the name of your theme.
    ```yaml
    name: Deploy master

    on:
      workflow_dispatch:
      push:
        branches:
        - 'master'
        - 'main'

    concurrency:
      group: ${{ github.workflow }}-master
      cancel-in-progress: false

    jobs:
      push:
        permissions:
          deployments: write
          contents: read
          pull-requests: read
        runs-on: ubuntu-latest
        steps:
        - uses: actions/checkout@v4
        - run: |
            cp -f .gitignore.pantheon .gitignore
            cd web/app/themes/<your-theme>
            npm ci && npm run build
        - name: Push to Pantheon
          uses: pantheon-systems/push-to-pantheon@0.6.0
          with:
            ssh_key: ${{ secrets.PANTHEON_SSH_KEY }}
            machine_token: ${{ secrets.PANTHEON_MACHINE_TOKEN }}
            site: ${{ vars.PANTHEON_SITE }}
    ```

    The `PANTHEON_SSH_KEY` and `PANTHEON_MACHINE_TOKEN` secrets are already configured for repositories in the Imarc GitHub organization, but you'll need to add the `PANTHEON_SITE` variable to the repository. This can be done by going to the repository's **Settings > Secrets and Variables > Actions > Variables > New repository variable**. The value will be in UUID format and should be accessible within the Pantheon dashboard.

