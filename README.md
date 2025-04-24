# Mill 4 Starter Theme

**Mill 4** is a WordPress starter theme adapted from the [Timber Starter Theme](https://timber.github.io/docs/v2/installation/installation/#use-the-starter-theme), which provides a no-frills starting point for building a WordPress theme with Timber.

In addition to the Twig templating that Timber provides, **Mill 4** includes the following features:

* A simple router for handling custom GET/POST/PUT/DELETE routes.
* An object-oriented Hooks system for registering actions and filters.
* A basic service container for dependency injection.
* An object-oriented interface for registering custom post types, taxonomies, and Gutenberg blocks.
* A basic front-end build process for compiling Sass and JavaScript using Vite.

## Table of Contents

- [Installation](#installation)
- [Hooks!](#hooks)
- [Custom Routes](#custom-routes)
- [Custom Post Types](#custom-post-types)
- [Custom Taxonomies](#custom-taxonomies)
- [Gutenberg Blocks](#gutenberg-blocks)
- [View Composers](#view-composers)
- [Commands](#commands)
- [Jobs](#jobs)
- [Recurring Events](#scheduling-recurring-events-via-wp-cron)
- [Admin Pages](#admin-pages)

## Installation

Best way to go about installing this thing is probably to just download the zip file and drop it into your `wp-content/themes` directory. Then you'll want to rename the folder to your own slug and edit the details in the `style.css` file.

Next, `cd` to the theme folder and run the following:

```bash
% composer install
% npm install
% npm run build
```

This will install the dependencies and compile the assets.

Finally, you'll want to activate the theme in the WordPress admin.

## Hooks!

Mill 4 includes a basic hooks system for registering actions and filters. While it's not strictly necessary to use it (there's nothing stopping you from registering your own actions and filters in the theme's `functions.php` file), it's a handy way to organize your code.

Take a look at the `app/Hooks` directory to see the included hooks classes. To create your own Hooks class, create a new class that implements `App\Hooks\Contracts\HooksInterface` and register it in the `app/bootstrap.php` file. Here's an example:

```php
// app/Hooks/MyHooks.php

namespace App\Hooks;

use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Contracts\HooksInterface;

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
use App\Services\Container;

$container = new Container();
$hooks = $container->get(Hooks\Registrar::class);
$hooks->register(Hooks\MyHooks::class);
```

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

*There's a handy command in the theme to flush the rewrite rules, so you can just run `wp mill4 flush-rewrite-rules` to flush the rules when needed.*

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

use App\Services\Router\MiddlewareInterface;
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

use App\Http\Controllers\Controller;

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

To register a custom post type in your theme, follow these steps:

1. **Create a Custom Post Type Class**:
   Create a new class for your custom post type. This class should extend the `PostType` class provided by the theme. For example:

   ```php
   <?php

   // app/PostTypes/Movie.php

   namespace App\PostTypes;

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

2. **Register the Custom Post Type**:
   Update the `POST_TYPES` constant in the `PostTypeHooks` class to include your new post type:

   ```php
    // app/Hooks/PostTypeHooks.php

    class PostTypeHooks implements HooksInterface
    {
        public const POST_TYPES = [
            PostTypes\Movie::class,
        ];
        
        ...
    ```

## Custom Taxonomies

To register a custom taxonomy in your theme, follow these steps:

1. **Create a Custom Taxonomy Class**:
   Create a new class for your custom taxonomy. This class should extend the `Taxonomy` class provided by the theme. For example:

   ```php
    <?php

    // app/Taxonomies/Genre.php

    namespace App\Taxonomies;

    use App\PostTypes\Movie;

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

2. **Register the Custom Taxonomy**:
   Update the `TAXONOMIES` constant in the `TaxonomyHooks` class to include your new taxonomy:

   ```php
    // app/Hooks/TaxonomyHooks.php

    class TaxonomyHooks implements HooksInterface
    {

        public const TAXONOMIES = [
            Taxonomies\Genre::class,
        ];

        ...
    ```

## Gutenberg Blocks

To register a Gutenberg block in your theme, follow these steps:

1. **Create a Gutenberg Block Class**:
   Create a new class for your Gutenberg block. This class should extend the `Block` class provided by the theme. For example:

   ```php
    <?php

    // app/Blocks/GenericCtaBlock.php

    namespace App\Blocks;

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

2. **Register the Gutenberg Block**:
   Update the `BLOCKS` constant in the `BlockHooks` class to include your new block:

   ```php
    // app/Hooks/BlockHooks.php

    class BlockHooks implements HooksInterface
    {
        public const BLOCKS = [
            Blocks\GenericCtaBlock::class,
        ];

        ...
    ```
3. **Create a Gutenberg Block Template**:
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

    protected function withContext(): array
    {
        return ['foo' => 'bar'];
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

## View Composers

Mill 4 includes a basic view composer system for adding custom context data to your Twig templates. To create a new view composer, follow these steps:

 1. **Create a View Composer Class**:
Create a new class for your view composer. This class should extend the `ViewComposer` class provided by the theme. For example:

    ```php
    <?php

    // app/ViewComposers/FooComposer.php

    namespace App\ViewComposers;

    class FooComposer extends ViewComposer
    {
        // The views that the composer should be applied to.
        public array $views = [
            'index.twig',
        ];

        // The context data to add to the view.
        public function withContext(): array
        {
            $data['foo'] = 'bar';

            return $data;
        }
    }
    ```

 2. **Register the View Composer**:
   Update the `VIEW_COMPOSERS` constant in the `TemplateHooks` class to include your new view composer:

    ```php
    // app/Hooks/TemplateHooks.php

    public const VIEW_COMPOSERS = [
        ViewComposers\FooComposer::class,
    ];

    ...
    ```

    Now your view composer will be applied to the specified views.

## Commands

Mill 4 includes a basic command system for running custom commands. To create a new command, follow these steps:

1. **Create a Command Class**:
   Create a new class for your command. This class should extend the `Command` class provided by the theme. Make sure you add a `__invoke()` method to your command class for standalone commands. For example:

   ```php
    // app/Commands/FooCommand.php

    namespace App\Commands;

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

2. **Register the Command**:
   After you've created your command, update the `COMMANDS` constant in the `CommandHooks` class to include your new command:

   ```php
    // app/Hooks/CommandHooks.php

    class CommandHooks implements HooksInterface
    {
        public const COMMANDS = [
            Commands\FooCommand::class,
        ];

        ...
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

To create a new job, follow these steps:

1. **Create a Job Class**:
   Create a new class for your job. This class should extend the `Job` class provided by the theme. For example:

   ```php
    // app/Jobs/MyGreatJob.php

    namespace App\Jobs;

    class MyGreatJob extends Job
    {
        public function handle(): void
        {
            die('I did a thing!');
        }
    }
    ```

2. **Register the Job**:
   Update the `JOBS` constant in the `JobHooks` class to include your new job:

   ```php
    // app/Hooks/JobHooks.php

    class JobHooks implements HooksInterface
    {
        public const JOBS = [
            Jobs\MyGreatJob::class,
        ];
    }
    ```

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
// app/Jobs/MyGreatJob.php

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

Mill 4 includes a basic system for creating custom admin pages. To create a new admin page, follow these steps:

1. **Create a Admin Page Class**:
   Create a new class for your admin page. This class should extend the `AdminPage` class provided by the theme. There are quite a few properties that you can set on the class to customize the page's location, appearance and behavior. For example:

   ```php
    // app/AdminPages/LogViewerPage.php

    namespace App\AdminPages;

    class LogViewerPage extends AdminPage
    {
        protected string $slug = 'logs';

        protected string $title = 'Log Viewer';

        protected string $capability = 'manage_options';

        protected int $menuPosition = 10;

        protected string $icon = 'dashicons-admin-tools';

        protected ?string $template = 'admin/log-viewer.twig';

        protected string $parentSlug = 'options-general.php';

        protected function withContext(): array
        {
            return [
                'logs' => 'foo',
            ];
        }
    }
    ```

    If parent slug is set, the admin page will be added as a submenu page to the parent page you specify (and the $icon property will be ignored).

    The `withContext()` method is used to pass any context data you'd like to pass to the template. 

2. **Register the Admin Page**:
   Update the `ADMIN_PAGES` constant in the `AdminPageHooks` class to include your new admin page:

   ```php
    // app/Hooks/AdminPageHooks.php

    use App\AdminPages;

    class AdminPageHooks implements HooksInterface
    {
        public const ADMIN_PAGES = [
            AdminPages\LogViewerPage::class,
        ];
    }
    ```

    Now your admin page should be available in the WordPress admin.
