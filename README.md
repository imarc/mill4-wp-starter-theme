# Mill 4 Starter Theme

**Mill 4** is a WordPress starter theme adapted from the [Timber Starter Theme](https://timber.github.io/docs/v2/installation/installation/#use-the-starter-theme), which provides a no-frills starting point for building a WordPress theme with Timber.

In addition to the Twig templating that Timber provides, **Mill 4** includes the following features:

* A simple router for handling custom GET/POST/PUT/DELETE routes.
* An object-oriented Hooks system for registering actions and filters.
* A basic service container for dependency injection.
* An object-oriented interface for registering custom post types, taxonomies, and Gutenberg blocks.
* A basic front-end build process for compiling Sass and JavaScript using Vite.

## Registering Custom Routes

Custom routes are registered in `app/routes.php`. You can use either a closure or an invokable controller to handle the route.

```php

use App\Http\Controllers\SubmitContactFormAction;

$router->get('/foo', function () {
    return 'Hello world!';
});

$router->post('/contact/submit', SubmitContactFormAction::class);

```

## Registering Custom Post Types

To register a custom post type in your theme, follow these steps:

1. **Create a Custom Post Type Class**:
   Create a new class for your custom post type. This class should extend the `PostType` class provided by the theme. For example:

   ```php
   <?php

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
    public function registerTaxonomies(): void
    {
        $taxonomies = [
            Taxonomies\Genre::class,
        ];

        ...
    }
    ```
