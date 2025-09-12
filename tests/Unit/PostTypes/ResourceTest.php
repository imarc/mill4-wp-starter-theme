<?php

namespace App\Tests\Unit\PostTypes;

use App\PostTypes\Resource;
use App\Tests\BaseTestCase;

/**
 * Unit tests for Resource post type
 */
class ResourceTest extends BaseTestCase
{
    private Resource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new Resource();
    }

    /**
     * Test post type constants are properly defined
     */
    public function test_post_type_constants()
    {
        $this->assertEquals('resources', Resource::SLUG);
    }

    /**
     * Test post type labels are properly configured
     */
    public function test_post_type_labels()
    {
        $this->assertEquals('Resource', $this->resource->singularLabel);
        $this->assertEquals('Resources', $this->resource->pluralLabel);
    }

    /**
     * Test post type path configuration
     */
    public function test_post_type_path()
    {
        $this->assertEquals('resources', $this->resource->path);
    }

    /**
     * Test post type arguments configuration
     */
    public function test_post_type_args()
    {
        $this->assertIsArray($this->resource->args);
        $this->assertArrayHasKey('menu_icon', $this->resource->args);
        $this->assertEquals('dashicons-format-aside', $this->resource->args['menu_icon']);
    }

    /**
     * Test that the resource extends the base PostType class
     */
    public function test_extends_post_type_class()
    {
        $this->assertInstanceOf('Imarc\Millyard\PostTypes\PostType', $this->resource);
    }

    /**
     * Test that the RegistersPostType attribute is present
     */
    public function test_has_registers_post_type_attribute()
    {
        $reflection = new \ReflectionClass(Resource::class);
        $attributes = $reflection->getAttributes();

        $hasRegisterAttribute = false;
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === 'Imarc\Millyard\Attributes\RegistersPostType') {
                $hasRegisterAttribute = true;
                break;
            }
        }

        $this->assertTrue($hasRegisterAttribute, 'Resource should have RegistersPostType attribute');
    }

    /**
     * Test post type properties are accessible
     */
    public function test_properties_are_accessible()
    {
        $reflection = new \ReflectionClass($this->resource);

        // Test that all expected properties exist
        $this->assertTrue($reflection->hasProperty('singularLabel'));
        $this->assertTrue($reflection->hasProperty('pluralLabel'));
        $this->assertTrue($reflection->hasProperty('path'));
        $this->assertTrue($reflection->hasProperty('args'));
    }

    /**
     * Test that the slug constant matches the path
     */
    public function test_slug_matches_path()
    {
        $this->assertEquals(Resource::SLUG, $this->resource->path);
    }

    /**
     * Test that args is properly structured for WordPress
     */
    public function test_args_structure()
    {
        $args = $this->resource->args;

        $this->assertIsArray($args);

        // Test that menu_icon is a valid dashicon
        $this->assertStringStartsWith('dashicons-', $args['menu_icon']);
    }

    /**
     * Test that labels follow WordPress conventions
     */
    public function test_label_conventions()
    {
        // Singular label should not end with 's'
        $this->assertStringEndsNotWith('s', $this->resource->singularLabel);

        // Plural label should be different from singular
        $this->assertNotEquals($this->resource->singularLabel, $this->resource->pluralLabel);

        // Both labels should be non-empty strings
        $this->assertNotEmpty($this->resource->singularLabel);
        $this->assertNotEmpty($this->resource->pluralLabel);
        $this->assertIsString($this->resource->singularLabel);
        $this->assertIsString($this->resource->pluralLabel);
    }
}
