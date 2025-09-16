<?php

namespace App\Tests\Unit\Http\Controllers\API;

use App\Tests\BaseTestCase;

/**
 * Simple unit tests for CsrfTokenAction controller structure
 * Avoids config loading issues by testing class structure only
 */
class CsrfTokenActionSimpleTest extends BaseTestCase
{
    /**
     * Test that the controller class exists and is properly structured
     */
    public function test_controller_class_exists()
    {
        $this->assertTrue(class_exists('App\Http\Controllers\API\CsrfTokenAction'));
    }

    /**
     * Test that the controller extends the base Controller class
     */
    public function test_extends_controller_class()
    {
        $reflection = new \ReflectionClass('App\Http\Controllers\API\CsrfTokenAction');
        $this->assertTrue($reflection->isSubclassOf('Imarc\Millyard\Http\Controller'));
    }

    /**
     * Test that the controller has the __invoke method
     */
    public function test_has_invoke_method()
    {
        $reflection = new \ReflectionClass('App\Http\Controllers\API\CsrfTokenAction');
        $this->assertTrue($reflection->hasMethod('__invoke'));

        $method = $reflection->getMethod('__invoke');
        $this->assertTrue($method->isPublic());
    }

    /**
     * Test that the __invoke method returns JsonResponse type hint
     */
    public function test_invoke_method_return_type()
    {
        $reflection = new \ReflectionClass('App\Http\Controllers\API\CsrfTokenAction');
        $method = $reflection->getMethod('__invoke');

        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('Symfony\Component\HttpFoundation\JsonResponse', $returnType->getName());
    }
}

