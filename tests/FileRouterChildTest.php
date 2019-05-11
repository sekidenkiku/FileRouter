<?php

use sekidenkiku\FileRouter\FileRouter;
use sekidenkiku\FileRouter\FileRouterChild;
use PHPUnit\Framework\TestCase;

class FileRouterChildTest extends TestCase
{
    /**
     * getSiteDir関数のテスト。
     */
    public function getSiteDir()
    {
        $f = new FileRouter();
        $f->setSiteDir('test');
        $fc = new FileRouterChild($f);
        $this->assertEquals($fc->getSiteDir(), 'test');
    }

    /**
     * getFilesDir関数のテスト。
     */
    public function testGetFilesDir()
    {
        $f = new FileRouter();
        $f->setFilesDir('test');
        $fc = new FileRouterChild($f);
        $this->assertEquals($fc->getFilesDir(), 'test');
    }

    /**
     * getRouters関数のテスト。
     */
    public function testGetRoutes()
    {
        $f = new FileRouter();
        $f->addRoute('/', 'index.php');
        $f->addRoute('/test', 'test.php');
        $fc = new FileRouterChild($f);
        $expected = [
            ['url' => '/', 'file' => 'index.php'],
            ['url' => '/test', 'file' => 'test.php'],
        ];
        $this->assertEquals($fc->getRoutes(), $expected);
    }

    /**
     * getRoute関数のテスト。
     */
    public function testGetRoute()
    {
        $f = new FileRouter();
        $f->addRoute('/', 'index.php');
        $fc = new FileRouterChild($f);
        $this->assertEquals($fc->getRoute('/'), 'index.php');
    }

    /**
     * getVar関数のテスト。
     */
    public function testGetVar()
    {
        $f = new FileRouter();
        $f->addVar('user_name', 'suzuki');
        $f->addVar('array', ['subject' => '登録完了', 'body' => '登録完了しました。']);
        $obj = new stdClass();
        $obj->val = 'test';
        $f->addVar('obj', $obj);
        $fc = new FileRouterChild($f);
        $this->assertEquals($fc->getVar('user_name'), 'suzuki');
        $this->assertEquals($fc->getVar('array'), ['subject' => '登録完了', 'body' => '登録完了しました。']);
        $this->assertEquals($fc->getVar('obj'), $obj);
    }

}
