<?php

use sekidenkiku\FileRouter\FileRouter;
use PHPUnit\Framework\TestCase;

class FileRouterRunTest extends TestCase
{
    /**
     * 定義済みのURLにアクセスの場合、ページが表示される。
     * @runInSeparateProcess
     */
    public function testViewPage()
    {
        $_SERVER["HTTPS"] = 'on';
        $_SERVER["HTTP_HOST"] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/test01';
        $f = new FileRouter();
        $f->setFilesDir(__DIR__ . '/files');
        $f->addRoute('/test01', 'test01.php');
        ob_start();
        $f->run(false);
        $out = ob_get_contents();
        $this->assertTrue(false !== strpos($out, 'test01.php page view'));
        ob_end_clean();
    }

    /**
     * サイトのディレクトリ変更後に、定義済みのURLにアクセスの場合、ページが表示される。
     * @runInSeparateProcess
     */
    public function testViewPage02()
    {
        $_SERVER["HTTPS"] = 'on';
        $_SERVER["HTTP_HOST"] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/sub/test01';
        $f = new FileRouter();
        $f->setSiteDir( 'sub');
        $f->setFilesDir(__DIR__ . '/files');
        $f->addRoute('/test01', 'test01.php');
        ob_start();
        $f->run(false);
        $out = ob_get_contents();
        $this->assertTrue(false !== strpos($out, 'test01.php page view'));
        ob_end_clean();
    }

    /**
     * 未定義のURLにアクセスの場合は404Not Foundページが表示され、そのURLが表示される。
     * @runInSeparateProcess
     */
    public function testNotFoundPage404()
    {
        $_SERVER["HTTPS"] = 'on';
        $_SERVER["HTTP_HOST"] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/nobody.php';
        ob_start();
        FileRouter::notFoundPage404(false);
        $out = ob_get_contents();
        $this->assertTrue(false !== strpos($out, 'https://example.com/nobody.php'));
        ob_end_clean();
    }

    /**
     * 定義済みのURLにアクセスしたがページファイルが見つからない場合、エラーがスローされる。
     * @runInSeparateProcess
     */
    public function testRouter_File_not_found()
    {
        // LogicExceptionがスローされる。
        $this->expectException('\LogicException');
        // メッセージの確認。
        $this->expectExceptionMessageRegExp("/ファイルが見つかりません。/");

        $_SERVER["HTTPS"] = 'on';
        $_SERVER["HTTP_HOST"] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/test01';
        $f = new FileRouter();
        $f->setFilesDir(__DIR__ . '/files');
        $f->addRoute('/test01', 'no_file.php');
        $f->run(false);
    }
}
