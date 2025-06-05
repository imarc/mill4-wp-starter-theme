<?php

namespace App\Hooks;

use Imarc\Millyard\Attributes\RegistersViewComposer;
use Imarc\Millyard\Concerns\DiscoversClasses;
use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;
use Imarc\Millyard\Services\Container;
use Imarc\Millyard\Twig\RenderPartialTokenParser;
use Imarc\Millyard\Views\ComposerRegistry;
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
        private ComposerRegistry $viewComposerRegistry
    ) {
    }

    public function initialize(): void
    {
        $this->addFilter('timber/context', array( $this, 'addToContext' ));
        $this->addFilter('timber/twig', array( $this, 'extendTwig' ));
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
        $context['mobile_navigation'] = Timber::get_menu('mobile-navigation');
        $context['mega_menu'] = get_field('mega_menu', 'option');
        $context['site']  = $this->site;

        return $context;
    }

    /**
     * This is where you can add your own functions to twig.
     *
     * @param Twig\Environment $twig get extension.
     */
    public function extendTwig($twig)
    {
        /**
         * This is a custom parser that allows you to render a partial template.
         * Why? Because {% include %} will not work with the view composers, since
         * composers rely on the "timber/render/data" filter to update the context.
         */
        $twig->addTokenParser(new RenderPartialTokenParser());

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
