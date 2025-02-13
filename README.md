# Mill 4 Starter Theme

**Mill 4** is a WordPress starter theme adapted from the [Timber Starter Theme](https://timber.github.io/docs/v2/installation/installation/#use-the-starter-theme), which provides a no-frills starting point for building a WordPress theme with Timber.

In addition to the Twig templating that Timber provides, **Mill 4** includes the following features:

* A simple router for handling custom GET/POST/PUT/DELETE routes.
* An object-oriented Hooks system for registering actions and filters.
* A basic service container for dependency injection.
* An object-oriented interface for registering custom post types, taxonomies, and Gutenberg blocks.
* A basic front-end build process for compiling Sass and JavaScript using Vite.

## Registering Custom Routes

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


## Registering Custom Post Types

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
   Update the `registerPostTypes()` method in the `PostTypeHooks` class to include your new post type:

   ```php

    // app/Hooks/PostTypeHooks.php

    public function registerPostTypes(): void
    {
        $postTypes = [
            PostTypes\Movie::class,
        ];

        ...
    ```

## Registering Custom Taxonomies

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
   Update the `registerTaxonomies()` method in the `TaxonomyHooks` class to include your new taxonomy:

   ```php
    // app/Hooks/TaxonomyHooks.php

    public function registerTaxonomies(): void
    {
        $taxonomies = [
            Taxonomies\Genre::class,
        ];

        ...
    }
    ```

## Registering Gutenberg Blocks

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
   Update the `registerBlocks()` method in the `BlockHooks` class to include your new block:

   ```php
    // app/Hooks/BlockHooks.php

    public function registerBlocks(): void
    {
        $registrar = new Blocks\Registrar();

        $blocks = [
            Blocks\GenericCtaBlock::class,
        ];
        ...
    }
    ```
3. **Create a Gutenberg Block Template**:
   Create a new twig template file for your block. This file should be named after the block name and placed in the `views/blocks` directory. For example:

   ```php
    // views/blocks/generic-cta-block.twig

    {% extends "base.twig" %}

    {% block content %}
        <div class="generic-cta-block">
            <h2>{{ block.title }}</h2>
        </div>
    {% endblock %}
    ```
    *Note: Any ACF data associated with the block will automatically be included in a template variable called `block`. If you'd like to pass any additional context to the template, you may override the getAdditionalContext() method in your block class:*

    ```php
    //  app/Blocks/GenericCtaBlock.php

    protected function getAdditionalContext(): array
    {
        return ['foo' => 'bar'];
    }

    // views/blocks/generic-cta-block.twig

    {% extends "base.twig" %}

    {% block content %}
        <div class="generic-cta-block">
            <h2>{{ block.title }}</h2>
            <p>{{ foo }}</p> <!-- This is the foo context variable -->
        </div>
    {% endblock %}  
    ```
