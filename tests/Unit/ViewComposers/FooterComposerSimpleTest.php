<?php

namespace App\Tests\Unit\ViewComposers;

use App\Tests\BaseTestCase;
use App\ViewComposers\FooterComposer;

/**
 * Simple unit tests for FooterComposer view composer structure
 */
class FooterComposerSimpleTest extends BaseTestCase
{
    private FooterComposer $composer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->composer = new FooterComposer();
    }

    /**
     * Test that views property is properly configured
     */
    public function test_views_property()
    {
        $this->assertIsArray($this->composer->views);
        $this->assertContains('footer.twig', $this->composer->views);
    }

    /**
     * Test that the composer extends the base Composer class
     */
    public function test_extends_composer_class()
    {
        $this->assertInstanceOf('Imarc\Millyard\Views\Composer', $this->composer);
    }

    /**
     * Test that the RegistersViewComposer attribute is present
     */
    public function test_has_registers_view_composer_attribute()
    {
        $reflection = new \ReflectionClass(FooterComposer::class);
        $attributes = $reflection->getAttributes();

        $hasRegisterAttribute = false;
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === 'Imarc\Millyard\Attributes\RegistersViewComposer') {
                $hasRegisterAttribute = true;

                break;
            }
        }

        $this->assertTrue($hasRegisterAttribute, 'FooterComposer should have RegistersViewComposer attribute');
    }

    /**
     * Test that withContext method exists and is callable
     */
    public function test_with_context_method_exists()
    {
        $this->assertTrue(method_exists($this->composer, 'withContext'));
        $this->assertTrue(is_callable([$this->composer, 'withContext']));
    }

    /**
     * Test the class structure
     */
    public function test_class_structure()
    {
        $reflection = new \ReflectionClass($this->composer);

        // Should have views property
        $this->assertTrue($reflection->hasProperty('views'));

        // Should have withContext method
        $this->assertTrue($reflection->hasMethod('withContext'));

        // withContext should be public
        $method = $reflection->getMethod('withContext');
        $this->assertTrue($method->isPublic());

        // withContext should return array
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    /**
     * Test that views property contains expected templates
     */
    public function test_views_contains_footer_template()
    {
        $this->assertNotEmpty($this->composer->views);
        $this->assertContains('footer.twig', $this->composer->views);

        // Should be an array of strings
        foreach ($this->composer->views as $view) {
            $this->assertIsString($view);
        }
    }
}
