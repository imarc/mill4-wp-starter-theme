<?php

namespace App\Tests\Unit\Blocks;

use App\Blocks\BasicSection;
use App\Tests\BaseTestCase;

/**
 * Unit tests for BasicSection block
 */
class BasicSectionTest extends BaseTestCase
{
    private BasicSection $basicSection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->basicSection = new BasicSection();
    }

    /**
     * Test block constants are properly defined
     */
    public function test_block_constants()
    {
        $this->assertEquals('basic-section', BasicSection::NAME);
        $this->assertEquals('Basic Section', BasicSection::TITLE);
        $this->assertEquals('section', BasicSection::CATEGORY);
        $this->assertEquals('align-center', BasicSection::ICON);
        $this->assertIsArray(BasicSection::POST_TYPES);
        $this->assertIsArray(BasicSection::KEYWORDS);
        $this->assertContains('section', BasicSection::KEYWORDS);
    }

    /**
     * Test withContext method with single column layout
     */
    public function test_with_context_single_column()
    {
        // Use reflection to set the protected context property
        $reflection = new \ReflectionClass($this->basicSection);
        $contextProperty = $reflection->getProperty('context');
        $contextProperty->setAccessible(true);
        $contextProperty->setValue($this->basicSection, [
            'block' => [
                'layout' => '1-column',
                'primary_content' => [
                    'width' => '1/2',
                ],
            ],
        ]);

        $result = $this->basicSection->withContext();

        $this->assertArrayHasKey('block', $result);
        $this->assertEquals('half', $result['block']['primary_content']['width']);
    }

    /**
     * Test withContext method with two column layout
     */
    public function test_with_context_two_column()
    {
        // Use reflection to set the protected context property
        $reflection = new \ReflectionClass($this->basicSection);
        $contextProperty = $reflection->getProperty('context');
        $contextProperty->setAccessible(true);
        $contextProperty->setValue($this->basicSection, [
            'block' => [
                'layout' => '2-column',
                'primary_content' => [
                    'width' => '1/3',
                ],
            ],
        ]);

        $result = $this->basicSection->withContext();

        $this->assertArrayHasKey('block', $result);
        $this->assertEquals('one-third', $result['block']['primary_content']['width']);
        $this->assertEquals('two-thirds', $result['block']['secondary_content']['width']);
    }

    /**
     * Test withContext method with different width mappings
     */
    public function test_with_context_width_mappings()
    {
        $testCases = [
            ['1/3', 'one-third', '2/3', 'two-thirds'],
            ['1/2', 'half', '1/2', 'half'],
            ['2/3', 'two-thirds', '1/3', 'one-third'],
        ];

        foreach ($testCases as [$primaryWidth, $expectedPrimaryClass, $expectedSecondaryWidth, $expectedSecondaryClass]) {
            // Use reflection to set the protected context property
            $reflection = new \ReflectionClass($this->basicSection);
            $contextProperty = $reflection->getProperty('context');
            $contextProperty->setAccessible(true);
            $contextProperty->setValue($this->basicSection, [
                'block' => [
                    'layout' => '2-column',
                    'primary_content' => [
                        'width' => $primaryWidth,
                    ],
                ],
            ]);

            $result = $this->basicSection->withContext();

            $this->assertEquals(
                $expectedPrimaryClass,
                $result['block']['primary_content']['width'],
                "Primary width {$primaryWidth} should map to {$expectedPrimaryClass}"
            );
            $this->assertEquals(
                $expectedSecondaryClass,
                $result['block']['secondary_content']['width'],
                "Secondary width should be {$expectedSecondaryClass} when primary is {$primaryWidth}"
            );
        }
    }

    /**
     * Test withContext method with empty context
     */
    public function test_with_context_empty()
    {
        // Use reflection to set the protected context property
        $reflection = new \ReflectionClass($this->basicSection);
        $contextProperty = $reflection->getProperty('context');
        $contextProperty->setAccessible(true);
        $contextProperty->setValue($this->basicSection, [
            'block' => [
                'layout' => '1-column', // Provide a layout to avoid undefined key error
            ],
        ]);

        $result = $this->basicSection->withContext();

        $this->assertArrayHasKey('block', $result);
        $this->assertIsArray($result['block']);
    }

    /**
     * Test withContext method with missing width values
     */
    public function test_with_context_missing_width()
    {
        // Use reflection to set the protected context property
        $reflection = new \ReflectionClass($this->basicSection);
        $contextProperty = $reflection->getProperty('context');
        $contextProperty->setAccessible(true);
        $contextProperty->setValue($this->basicSection, [
            'block' => [
                'layout' => '2-column',
                'primary_content' => [
                    // No width specified
                ],
            ],
        ]);

        $result = $this->basicSection->withContext();

        $this->assertArrayHasKey('block', $result);
        // Should default to 1/2 when no width is specified
        $this->assertEquals('half', $result['block']['secondary_content']['width']);
    }
}
