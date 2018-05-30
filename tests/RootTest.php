<?php

declare(strict_types=1);

namespace Wtf\Core\Tests;

use PHPUnit\Framework\TestCase;

class RootTest extends TestCase
{
    protected function setUp(): void
    {
        $dir = __DIR__.'/data/config';
        $app = new \Wtf\App(['config_dir' => $dir]);
        $this->root = new \Wtf\Root($app->getContainer());
    }

    public function testRealGetSet(): void
    {
        $this->assertInstanceOf('\Wtf\Root', $this->root->set('property', 'value'));
        $this->assertEquals($this->root->get('property'), 'value');

        $this->assertInstanceOf('\Wtf\Root', $this->root->set('property'));
        $this->assertEquals($this->root->get('property'), null);

        $this->assertInstanceOf('\Wtf\Root', $this->root->set('property'));
        $this->assertEquals($this->root->get('property', false), false);
    }

    public function testMagicCall(): void
    {
        $this->expectException('ArgumentCountError');
        $this->assertInstanceOf('\Wtf\Root', $this->root->__call('set', ['property', 'value']));
        $this->assertInstanceOf('\Wtf\Root', $this->root->__call('set', []));
    }

    public function testMagicGetSet(): void
    {
        $this->assertInstanceOf('\Wtf\Root', $this->root->setProperty('value'));
        $this->assertEquals($this->root->getProperty(), 'value');

        $this->assertInstanceOf('\Wtf\Root', $this->root->setProperty());
        $this->assertEquals($this->root->getProperty(), null);

        $this->assertInstanceOf('\Wtf\Root', $this->root->setProperty());
        $this->assertEquals($this->root->getProperty(false), false);
    }

    public function testGetSetData(): void
    {
        //getData (from scratch)
        $this->assertEquals([], $this->root->getData());
        //setData (from scratch)
        $this->root->setData(['one' => true, 'two' => true]);
        $this->assertTrue($this->root->getOne());
        $this->assertTrue($this->root->getTwo());
        //setData (mege)
        $this->root->setData(['one' => false]);
        $this->assertFalse($this->root->getOne());
        $this->assertTrue($this->root->getTwo());
        //getData (with data)
        $this->assertEquals(['one' => false, 'two' => true], $this->root->getData());
    }

    public function testMagicGet(): void
    {
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $this->root->response);
        $this->assertNull($this->root->undefined);
    }

    public function testMagicContainerMethod(): void
    {
        $this->assertEquals('something', $this->root->config('suit.dummy.has'));
    }

    public function testUndefinedMethod(): void
    {
        $this->expectException('\Exception');
        $this->root->_someUndefinedMethod();
        $this->root->_someUndefinedMethod(2);
    }
}
