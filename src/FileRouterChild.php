<?php
/**
 * 変数$FileRouterの元になるクラスです。ファイル展開後の使用可能な機能を定義します。
 */

namespace sekidenkiku\FileRouter;

/**
 * 変数$FileRouterの元になるクラス。
 * @copyright (c) Takahisa Ishida <sekidenkiku@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/sekidenkiku/FileRouter
 * @version 1.0.1
 */
class FileRouterChild
{
    /**
     * @var FileRouter FileRouterクラスのインスタント。
     */
    protected $file_router;

    public function __construct(FileRouter $file_router)
    {
        $this->file_router = $file_router;
    }


    /**
     * 設定したサイトのディレクトリを返します。
     * @return string ディレクトリ名
     */
    public function getSiteDir()
    {
        return $this->file_router->getSiteDir();
    }

    /**
     * 実行ファイルのディレクトリを返します。
     * @return string 実行ファイルのディレクトリ。
     */
    public function getFilesDir()
    {
        return $this->file_router->getFilesDir();
    }

    /**
     * 全ルーティング情報を返します。
     * @return array ルーティング情報。
     */
    public function getRoutes()
    {
        return $this->file_router->getRoutes();
    }

    /**
     * URLパスに一致するルート先ファイル名を返します。
     * @param string $url URL。
     * @return string ルート先ファイル名。存在しない場合、空の文字列を返します。
     */
    public function getRoute($url)
    {
        return $this->file_router->getRoute($url);
    }

    /**
     * 実行ファイルに渡した値を取得します。
     * @param string $name 名前。
     * @return mixed|null 値。存在しない名前を指定した場合nullを返します。
     */
    public function getVar($name)
    {
        return $this->file_router->getVar($name);
    }

}