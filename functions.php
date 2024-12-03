<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 */

use Timber\Timber;

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';

Timber::init();

// Sets the directories (inside your theme) to find .twig files.
Timber::$dirname = [ 'templates', 'views' ];

require_once __DIR__ . '/app/bootstrap.php';
