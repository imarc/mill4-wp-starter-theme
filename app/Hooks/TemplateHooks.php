<?php

namespace App\Hooks;

use App\Attributes\RegistersViewComposer;
use App\Hooks\Concerns\DiscoversClasses;
use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Contracts\HooksInterface;
use App\Services\Container;
use App\ViewComposers\ViewComposerRegistry;
use Twig;
use Timber\Site;
use Timber\Timber;

class TemplateHooks implements HooksInterface
{
    use DiscoversClasses;
    use RegistersHooks;

    public function __construct(
        protected Site $site,
        private Container $container,
        private ViewComposerRegistry $viewComposerRegistry
    ) {
    }

    public function initialize(): void
    {
        $this->addFilter('timber/context', array( $this, 'addToContext' ));
        $this->addFilter('timber/twig', array( $this, 'addToTwig' ));
        $this->addFilter('timber/twig/environment/options', [ $this, 'updateTwigEnvironmentOptions' ]);
        $this->addAction('init', [$this, 'registerViewComposers']);
    }

    /**
     * This is where you add some context
     *
     * @param string $context context['this'] Being the Twig's {{ this }}.
     */
    public function addToContext($context)
    {
        $context['primary_navigation']  = Timber::get_menu('primary-navigation');
        $context['footer_navigation']  = Timber::get_menu('footer-navigation');
        $context['utility_navigation'] = Timber::get_menu('utility-navigation');
        $context['site']  = $this->site;

        return $context;
    }

    /**
     * This would return 'foo bar!'.
     *
     * @param string $text being 'foo', then returned 'foo bar!'.
     */
    public function myfoo($text)
    {
        $text .= ' bar!';
        return $text;
    }

    /**
     * This is where you can add your own functions to twig.
     *
     * @param Twig\Environment $twig get extension.
     */
    public function addToTwig($twig)
    {
        /**
         * Required when you want to use Twig's template_from_string.
         * @link https://twig.symfony.com/doc/3.x/functions/template_from_string.html
         */
        // $twig->addExtension( new Twig\Extension\StringLoaderExtension() );

        $twig->addFilter(new Twig\TwigFilter('myfoo', [ $this, 'myfoo' ]));

        return $twig;
    }

    /**
     * Updates Twig environment options.
     *
     * @link https://twig.symfony.com/doc/2.x/api.html#environment-options
     *
     * \@param array $options An array of environment options.
     *
     * @return array
     */
    public function updateTwigEnvironmentOptions($options)
    {
        // $options['autoescape'] = true;

        return $options;
    }

    public function registerViewComposers(): void
    {
        $classes = $this->discoverClassesForAttribute(RegistersViewComposer::class, 'ViewComposers');

        foreach ($classes as $viewComposer) {
            $this->viewComposerRegistry->registerComposer($viewComposer);
        }

        if ($this->viewComposerRegistry->hasComposers()) {
            $this->addFilter('timber/render/data', [$this->viewComposerRegistry, 'filterDataForComposers'], 10, 2);
        }
    }
}
