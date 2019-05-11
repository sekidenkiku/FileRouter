<?php
/**
 * シンプルなURL振り分け機能クラス
 */

namespace sekidenkiku\FileRouter;

use sekidenkiku\FileRouter\FileRouterChild;

/**
 * シンプルなURL振り分け機能を提供します。
 * @copyright (c) Takahisa Ishida <sekidenkiku@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/sekidenkiku/FileRouter
 * @version 1.0.2
 */
class FileRouter
{
    /**
     * @var array ルート情報
     */
    private $routes = array();

    /**
     * @var string 実行ファイルのディレクトリ。
     */
    private $files_dir = 'files';

    /**
     * @var string サイトのディレクトリ。ルート情報のページURLのベースとなるディレクトリ。
     */
    private $site_dir = '/';

    /**
     * @var array 変数リスト
     */
    private $var = array();

    /**
     * サイトのディレクトリを設定します。先頭と最後のスラッシュは省略できます。空文字は指定できません。
     * @param string $dir ディレクトリ名
     */
    public function setSiteDir($dir)
    {
        if ("" === strval($dir)) {
            throw new \BadFunctionCallException('ディレクトリ名が空です。');
        }
        $this->site_dir = $dir;
    }

    /**
     * サイトのディレクトリを返します。
     * @return string サイトのディレクトリ。
     */
    public function getSiteDir()
    {
        return $this->site_dir;
    }

    /**
     * 実行ファイルのディレクトリを設定します。ルート情報で設定する実行ファイルの設置ディレクトリです。先頭と最後のスラッシュは省略できます。空文字は指定できません。
     * @param string $dir 実行ファイルのディレクトリ。
     */
    public function setFilesDir($dir)
    {
        if ("" === strval($dir)) {
            throw new \BadFunctionCallException('ディレクトリ名が空です。');
        }
        $this->files_dir = $dir;
    }

    /**
     * 実行ファイルのディレクトリを返します。
     * @return string 実行ファイルのディレクトリ。
     */
    public function getFilesDir()
    {
        return $this->files_dir;
    }

    /**
     * ルート情報を追加します。
     * @param string $url ページURL。先頭の/は省略できます。空文字は指定できません。
     * @param string $file 実行ファイル名。実行ファイルディレクトリ内の相対パスになります。先頭の/は省略できます。空文字は指定できません。
     */
    public function addRoute($url, $file)
    {
        if ("" === strval($url)) {
            throw new \BadFunctionCallException('ページURLが空です。');
        }
        if ("" === strval($file)) {
            throw new \BadFunctionCallException('実行ファイル名が空です。');
        }
        $this->routes[] = array('url' => $url, 'file' => $file);
    }

    /**
     * 全ルート情報を返します。
     * @return array ルート情報。
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * 現在のURLから、ホスト名やクエリストリングなどを除いたパスの部分を返します。
     * @return string URLのパス名。
     */
    public static function getUrlPath()
    {
        $request_url = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI']) : array();
        $path = is_array($request_url) && isset($request_url['path']) ? $request_url['path'] : '/';
        if ("" === $path) {
            $path = '/';
        }
        return (string)$path;
    }

    /**
     * ルート情報にマッチする実行ファイルを実行します。マッチしない場合は、notFoundPage404関数を実行します。
     * @param bool $exit trueの場合、ファイル読み込み後に処理を終了します。falseの場合、終了しません。
     */
    public function run($exit = true)
    {
        $FileRouter = new FileRouterChild($this);
        $file = $this->getRoute(self::getUrlPath());
        if ("" !== $file) {
            $router_file_path = $this->getMergedFilePath($this->getFilesdir(), $file);
            if (file_exists($router_file_path)) {
                include($router_file_path);
            } else {
                throw new \LogicException("ファイルが見つかりません。 {$router_file_path}.");
            }
            if ($exit) {
                exit();
            }
        } else {
            self::notFoundPage404($exit);
        }
    }

    /**
     * URLパスに一致するルート先ファイル名を返します。
     * @param string $url URLパス。
     * @return string ルート先ファイル名。存在しない場合、空の文字列を返します。
     */
    public function getRoute($url)
    {
        $res = '';
        foreach ($this->routes as $route) {
            $router_url_path = $this->getMergedUrlPath($this->getSiteDir(), $route['url']);
            if ($url === $router_url_path) {
                $res = $route['file'];
                break;
            }
        }
        return $res;
    }

    /**
     * 404 Not Foundページを表示します。
     * @param bool $exit trueの場合、ページを表示後に処理を終了します。falseの場合、終了しません。
     */
    public static function notFoundPage404($exit = true)
    {
        $url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        $url = htmlspecialchars($url, ENT_QUOTES);
        header("HTTP/1.1 404 Not Found");
        echo <<< HTML
<!DOCTYPE html>
<html lang="ja">
<head>
    <title>404 Not Found</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<h1>アクセスされたURLが見つかりません。</h1>
<p>{$url}</p>
</body>
</html>
HTML;
        if ($exit) {
            exit();
        }
    }

    /**
     * 実行ファイルを実行する関数内のスコープに渡す値(実行ファイル参照値)を追加します。
     * @param string $name キー名。
     * @param mixed $value 値。
     */
    public function addVar($name, $value)
    {
        $this->var[$name] = $value;
    }

    /**
     * 実行ファイル参照値を取得します。
     * @param string $name キー名。
     * @return mixed|null 値。キー名が見つからない場合nullを返します。
     */
    public function getVar($name)
    {
        return isset($this->var[$name]) ? $this->var[$name] : null;
    }

    /**
     * サイトのディレクトリとルート情報のURLを連結して返します。
     * @param string $site_dir サイトのディレクトリ。
     * @param string $url ルート情報のURLパス。
     * @return string URLパス。
     */
    protected function getMergedUrlPath($site_dir, $url)
    {
        $site_dir = '/' . trim($site_dir, '/');
        $url = '/' . ltrim($url, '/');
        if ('/' === $site_dir) {
            $res = $url;
        } elseif ('/' === $url) {
            $res = $site_dir . '/';
        } else {
            $res = $site_dir . $url;
        }
        return $res;
    }

    /**
     * 実行ファイルのディレクトリとルート情報のファイルを連結して返します。
     * @param string $files_dir 実行ファイルのディレクトリ。
     * @param string $file ルート情報のファイル名。
     * @return string ファイルパス。
     */
    protected function getMergedFilePath($files_dir, $file)
    {
        $files_dir = rtrim($files_dir, '/');
        $file = '/' . ltrim($file, '/');
        if ('/' === $files_dir) {
            $res = $file;
        } elseif ('/' === $file) {
            $res = $files_dir . '/';
        } else {
            $res = $files_dir . $file;
        }
        return $res;
    }

}