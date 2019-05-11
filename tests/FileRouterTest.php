<?php

use sekidenkiku\FileRouter\FileRouter;
use PHPUnit\Framework\TestCase;

class FileRouterTest extends TestCase
{

    /**
     * setFilesDir関数で入力した値は、未加工でgetFilesDir関数で取得できる。
     * @param string $val ディレクトリ名。
     * @param string $expected 結果。
     * @dataProvider addFilesDirFunc
     */
    public function testFilesDirFunc($val, $expected)
    {
        $f = new FileRouter();
        $f->setFilesDir($val);
        $this->assertEquals($f->getFilesDir(), $expected);
    }

    /**
     * データプロバイダ関数
     * @return array テストデータ。
     */
    public function addFilesDirFunc()
    {
        // [入力値, 結果]
        return [
            ['/', '/'],
            ['sub', 'sub'],
            ['/sub', '/sub'],
            ['sub/', 'sub/'],
            ['/sub/', '/sub/'],
            ['sub/sub2', 'sub/sub2'],
            ['/sub/sub2', '/sub/sub2'],
            ['sub/sub2/', 'sub/sub2/'],
            ['/sub/sub2/', '/sub/sub2/'],
        ];
    }

    /**
     * setFilesDir関数に空文字を入れるとエラーがスローされる。
     */
    public function testSetFilesDir_is_empty()
    {
        // BadFunctionCallExceptionがスローされる。
        $this->expectException('\BadFunctionCallException');
        // メッセージの確認。
        $this->expectExceptionMessage('ディレクトリ名が空です。');
        $f = new FileRouter();
        $f->setFilesDir('');
    }

    /**
     * setSiteDir関数で入力した値は、未加工でgetSiteDir関数で取得できる。
     * @param string $val ディレクトリ名。
     * @param string $expected 結果。
     * @dataProvider addSetSiteDirFunc
     */
    public function testSetSiteDirFunc($val, $expected)
    {
        $f = new FileRouter();
        $f->setSiteDir($val);

        $this->assertEquals($f->getSiteDir(), $expected);
    }

    /**
     * データプロバイダ関数
     * @return array テストデータ。
     */
    public function addSetSiteDirFunc()
    {
        // [入力値, 正解値]
        return [
            ['/', '/'],
            ['sub', 'sub'],
            ['/sub', '/sub'],
            ['sub/', 'sub/'],
            ['/sub/', '/sub/'],
            ['sub/sub2', 'sub/sub2'],
            ['/sub/sub2', '/sub/sub2'],
            ['sub/sub2/', 'sub/sub2/'],
            ['/sub/sub2/', '/sub/sub2/'],
        ];
    }

    /**
     * setSiteDir関数に空文字を入れるとエラーがスローされる。
     */
    public function testSetSiteDir_is_empty()
    {
        // BadFunctionCallExceptionがスローされる。
        $this->expectException('\BadFunctionCallException');
        // メッセージの確認。
        $this->expectExceptionMessage('ディレクトリ名が空です。');
        $f = new FileRouter();
        $f->setSiteDir('');
    }

    /**
     * ルート情報は、加工無しで保存される。テスト1。
     * @dataProvider addSetSiteDirFunc
     */
    public function testAddRoute01()
    {
        $f = new FileRouter();
        $f->addRoute('/', 'index.php');
        $f->addRoute('/test1', 'test1.php');
        $f->addRoute('test2/', 'test2.php');
        $f->addRoute('/test3/', 'test3.php');
        $f->addRoute('test4', 'test4.php');
        $expected = [
            ['url' => '/', 'file' => 'index.php'],
            ['url' => '/test1', 'file' => 'test1.php'],
            ['url' => 'test2/', 'file' => 'test2.php'],
            ['url' => '/test3/', 'file' => 'test3.php'],
            ['url' => 'test4', 'file' => 'test4.php'],
        ];
        $routes = $f->getRoutes();
        $this->assertEquals($routes, $expected);
    }

    /**
     * ルート情報は、加工無しで保存される。テスト２。
     */
    public function testAddRoute02()
    {
        $f = new FileRouter();
        $f->addRoute('/', 'index.php');
        $f->addRoute('/test1', '/test1.php');
        $f->addRoute('/test2', 'sub/test2.php');
        $f->addRoute('/test3', '/sub/test3.php');
        $expected = [
            ['url' => '/', 'file' => 'index.php'],
            ['url' => '/test1', 'file' => '/test1.php'],
            ['url' => '/test2', 'file' => 'sub/test2.php'],
            ['url' => '/test3', 'file' => '/sub/test3.php'],
        ];
        $routes = $f->getRoutes();
        $this->assertEquals($routes, $expected);
    }

    /**
     * ルート情報に空のURLパスを入力するとエラーがスルーされる。
     */
    public function testAddRoute_url_is_empty()
    {
        // BadFunctionCallExceptionがスローされる。
        $this->expectException('\BadFunctionCallException');
        // メッセージの確認。
        $this->expectExceptionMessage("ページURLが空です。");
        $f = new FileRouter();
        $f->addRoute('', 'index.php');
    }

    /**
     * ルート情報に空のファイル名を入力するとエラーがスルーされる。
     */
    public function testAddRoute_file_is_empty()
    {
        // BadFunctionCallExceptionがスローされる。
        $this->expectException('\BadFunctionCallException');
        // メッセージの確認。
        $this->expectExceptionMessage("実行ファイル名が空です。");
        $f = new FileRouter();
        $f->addRoute('/', '');
    }

    /**
     * ルート情報の初期値は空の配列が返る。
     */
    public function testGetRouters_get_init_data()
    {
        $f = new FileRouter();
        $this->assertEquals($f->getRoutes(), array());
    }

    /**
     * サイトのディレクトリとルート情報のURLパスの連結テスト。
     * @param string $site_dir サイトのディレクトリ。
     * @param string $url URL。
     * @param string $expected 結果。
     * @throws ReflectionException
     * @dataProvider addgetMergedUrlPath
     */
    public function testgetMergedUrlPath($site_dir, $url, $expected)
    {
        $f = new FileRouter();
        $reflection = new \ReflectionClass($f);
        $method = $reflection->getMethod('getMergedUrlPath');
        // アクセス許可
        $method->setAccessible(true);
        // メソッド実行
        $this->assertEquals($method->invoke($f, $site_dir, $url), $expected);
    }

    /**
     * データプロバイダ関数
     * @return array テストデータ。
     */
    public function addgetMergedUrlPath()
    {
        // [ サイトのディレクトリ, URL, 結果 ]
        // 結果の先頭文字は必ずスラッシュになる。
        return [
            ['/', '/', '/'],

            ['/', '/test', '/test'],
            ['/', 'test/', '/test/'],
            ['/', '/test/', '/test/'],
            ['/', 'test', '/test'],

            ['/', '/test/index.html', '/test/index.html'],
            ['/', 'test/index.html', '/test/index.html'],

            ['/', '/sub/test', '/sub/test'],
            ['/', 'sub/test', '/sub/test'],
            ['/', 'sub/test/', '/sub/test/'],
            ['/', 'sub/test/index.html', '/sub/test/index.html'],

            ['sub', '/', '/sub/'],
            ['/sub', '/', '/sub/'],
            ['sub/', '/', '/sub/'],
            ['/sub/', '/', '/sub/'],

            ['sub', '/test', '/sub/test'],
            ['/sub', '/test', '/sub/test'],
            ['sub/', '/test', '/sub/test'],
            ['/sub/', '/test', '/sub/test'],

            ['sub', '/test/', '/sub/test/'],
            ['/sub', '/test/', '/sub/test/'],
            ['sub/', '/test/', '/sub/test/'],
            ['/sub/', '/test/', '/sub/test/'],

            ['sub', '/test/index.html', '/sub/test/index.html'],
            ['/sub', '/test/index.html', '/sub/test/index.html'],
            ['sub/', '/test/index.html', '/sub/test/index.html'],
            ['/sub/', '/test/index.html', '/sub/test/index.html'],
        ];
    }

    /**
     * 実行ファイルのディレクトリとルート情報のファイル名の連結テスト。
     * @param string $files_dir 実行ファイルのディレクトリ。
     * @param string $file ファイル名。
     * @param string $expected 結果。
     * @throws ReflectionException
     * @dataProvider addgetMergedFilePath
     */
    public function testgetMergedFilePath($files_dir, $file, $expected)
    {
        $f = new FileRouter();
        $reflection = new \ReflectionClass($f);
        $method = $reflection->getMethod('getMergedFilePath');
        // アクセス許可
        $method->setAccessible(true);
        // メソッド実行
        $this->assertEquals($method->invoke($f, $files_dir, $file), $expected);
    }

    /**
     * データプロバイダ関数
     * @return array テストデータ。
     */
    public function addgetMergedFilePath()
    {
        // [ 実行ファイルのディレクトリ, ファイル名, 結果 ]
        return [
            ['/', '/', '/'],

            ['/', 'test.php', '/test.php'],
            ['/', '/test.php', '/test.php'],
            ['/', 'test/test.php', '/test/test.php'],
            ['/', '/test/test.php', '/test/test.php'],

            ['sub', 'test.php', 'sub/test.php'],
            ['sub', '/test.php', 'sub/test.php'],
            ['sub/', 'test.php', 'sub/test.php'],
            ['sub/', '/test.php', 'sub/test.php'],
            ['/sub', 'test.php', '/sub/test.php'],
            ['/sub', '/test.php', '/sub/test.php'],
            ['/sub/', 'test.php', '/sub/test.php'],
            ['/sub/', '/test.php', '/sub/test.php'],

            ['sub', 'test/test.php', 'sub/test/test.php'],
            ['sub', '/test/test.php', 'sub/test/test.php'],
            ['sub/', 'test/test.php', 'sub/test/test.php'],
            ['sub/', '/test/test.php', 'sub/test/test.php'],
            ['/sub', 'test/test.php', '/sub/test/test.php'],
            ['/sub', '/test/test.php', '/sub/test/test.php'],
            ['/sub/', 'test/test.php', '/sub/test/test.php'],
            ['/sub/', '/test/test.php', '/sub/test/test.php'],
        ];
    }

    /**
     */
    /**
     * 現在のURLからパス部分を取得するテスト。
     * @param string $url 現在のURL。
     * @param string $expected 結果。
     * @dataProvider addGetUrlPath
     */
    public function testGetUrlPath($url, $expected)
    {
        $_SERVER['REQUEST_URI'] = $url;
        $this->assertEquals(FileRouter::getUrlPath(), $expected);
    }

    /**
     * データプロバイダ関数
     * @return array テストデータ。
     */
    public function addGetUrlPath()
    {
        // [ 現在のURL, 結果 ]
        return [
            ['', '/'],
            ['http://test.com/', '/'],
            ['http://test.com/index.php', '/index.php'],
            ['/', '/'],
            ['/index.php', '/index.php'],
            ['/index.php?a=1&b=2', '/index.php'],
            ['/index.php#top', '/index.php'],
            ['/index.php/a', '/index.php/a'],
            ['/index.php/a?a=1', '/index.php/a'],
            ['/index.php/a#top', '/index.php/a'],
            ['/index.php/a/', '/index.php/a/'],
            ['/index.php/a/?a=1', '/index.php/a/'],
            ['/index.php/a/#top', '/index.php/a/'],
        ];
    }

    /**
     * URLパスと一致するルート情報が取得できる。一致しない場合は空文字を返す。
     * @param string $url URL。
     * @param string $expected ファイル名。
     * @dataProvider addGetRoute
     */
    public function testGetRoute($url, $expected)
    {
        // [ URL, ファイル名 ]
        $data = [
            ['/', 'index.php'],
            ['/test', 'test.php'],
            ['/test/', 'test_dir.php'],
        ];
        $f = new FileRouter();
        // ルーター情報の入力。
        foreach ($data as $datum) {
            $f->addRoute($datum[0], $datum[1]);
        }
        $file = $f->getRoute($url);
        $this->assertEquals($file, $expected);
    }

    /**
     * データプロバイダ関数
     * @return array テストデータ。
     */
    public function addGetRoute()
    {
        // [ URLパス, 結果 ]
        return [
            ['/', 'index.php'],
            ['/test', 'test.php'],
            ['/test/', 'test_dir.php'],
            ['/error', ''],
        ];
    }

    /**
     * サイトのディレクトリ変更後に、URLパスと一致するルート情報が取得できる。一致しない場合は空文字を返す。
     * @param string $url URLパス。
     * @param string $expected 結果。
     * @dataProvider addGetRoute02
     */
    public function testGetRoute02($url, $expected)
    {
        // [ URL, ファイル名 ]
        $data = [
            ['/', 'index.php'],
            ['/test', 'test.php'],
            ['/test/', 'test_dir.php'],
        ];
        $f = new FileRouter();
        // ルーター情報の入力。
        foreach ($data as $datum) {
            $f->addRoute($datum[0], $datum[1]);
        }
        // サイトのディレクトリ変更。
        $f->setSiteDir('sub');
        $file = $f->getRoute($url);
        $this->assertEquals($file, $expected);
    }

    /**
     * データプロバイダ関数
     * @return array テストデータ。
     */
    public function addGetRoute02()
    {
        // [ URLパス, 結果 ]
        return [
            ['/sub/', 'index.php'],
            ['/sub/test', 'test.php'],
            ['/sub/test/', 'test_dir.php'],
            ['/sub/error', ''],
        ];
    }
}
