# FileRouter
シンプルなURL振り分け機能を提供します。ファイル名の振り分けだけです。GET、POSTやURLクエリでの振り分けはできません。

## 特徴
・ページのURLを自由に指定できます。<br>
・index.phpに共通処理をまとめることができます。<br>
・小規模サイト用です。データベースを利用するようなサイトには向いていません。<br>

## 使用方法
**1.サイトのルートディレクトリに.htaccessファイルを設置して下さい。**
```
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule . index.php [L]
</IfModule>
```
**2.クラスファイルを読み込んで実行してください。**
```php
require_once('sekidenkiku/FileRouter.php');

$file_router = new FileRouter();
$file_router->setFilePath('template')
$file_router->addRoute('/', 'index.php');
$file_router->addRoute('/services', 'services.php');
$file_router->addRoute('/company', 'company.php');
$file_router->addRoute('/contact', 'contact.php');
$file_router->run();
```
composerからインストールした場合、require_once('sekidenkiku/FileRouter.php')は不要です。代わりにautoload.phpを読み込んでください。
```php
require_once('vendor/autoload.php');

$file_router = new FileRouter();
```
## インストール方法

・ファイルを直接、読み込む場合は、ファイルをダウンロードしてrequire_onceで読み込んでください。
```php
require_once('sekidenkiku/FileRouter.php');
```
・composerからインストールする場合。
```php
$ composer require sekidenkiku/file-router
```
## ライセンス
MIT License

## 関数リファレンス
### FileRouterクラス
### setSiteDir($dir)
サイトのディレクトリを設定します。ルート情報のURLパスのベースディレクトリです。  
デフォルト値は'/'です。 
先頭と最後のスラッシュは省略できます。空文字は指定できません。  
@param string $dir ディレクトリ名  
@return void  
> サイトディレクトリに'/sub'を設定した場合、addRoute('/example', 'index.php')は、URLパス'/sub/example'にマッチします。

### getSiteDir()
サイトのディレクトリを返します。  
@param void 
@return string サイトのディレクトリ名。  

### setFilesDir($dir)
実行ファイルのディレクトリを設定します。ルート情報の実行ファイルの設置ディレクトリです。  
デフォルト値は'files'です。  
先頭と最後のスラッシュは省略できます。空文字は指定できません。  
@param string $dir 実行ファイルのディレクトリ。  
@return void  
> デフォルトでは、ルートディレクトリに作成したfilesディレクトリが実行ファイルディレクトリとなります。  
> 実行ファイルは、実行ファイルディレクトリ内に設置して下さい。
> 
> ルートディレクトリ  
> ├ .htaccess  
> ├ index.php  
> ├ filesディレクトリ  
> │ ├ 実行ファイル  
> ・ ・  
> ・ ・  

### getFilesDir()
実行ファイルのディレクトリを返します。  
@param void  
@return string 実行ファイルのディレクトリ。  

### addRoute($url, $file)
ルート情報を追加します。  
@param string $url ページURL。先頭の/は省略できます。空文字は指定できません。  
@param string $file 実行ファイル名。実行ファイルディレクトリ内の相対パスになります。先頭の/は省略できます。空文字は指定できません。  
@return void  

### getRoutes()
全ルート情報を返します。  
@param void  
@return array ルート情報。  

### run($exit = true)
ルート情報にマッチする実行ファイルをインクロードします。マッチしない場合は、notFoundPage404関数を実行します。  
@param bool $exit trueの場合、ファイル読み込み後に処理を終了します。falseの場合、終了しません。  
@return void  

### getRoute($url)
URLパスに一致するルート先ファイル名を返します。  
@param string $url URLパス。  
@return string ルート先ファイル名。存在しない場合、空の文字列を返します。  

### addVar($name, $value)
実行ファイル内のスコープで利用できる値(実行ファイル参照値)を追加します。  
@param string $name キー名。  
@param mixed $value 値。  
@return void  

### getVar($name)
実行ファイル参照値を取得します。  
@param string $name キー名。  
@return mixed|null 値。キー名が見つからない場合nullを返します。  

**[static関数]**

### FileRouter::getUrlPath()
現在のURLから、ホスト名やクエリストリングなどを除いたパスの部分を返します。  
@param void  
@return string URLのパス名。  

### FileRouter::notFoundPage404($exit = true)
404 Not Foundページを出力します。  
@param bool $exit trueの場合、ページを表示後に処理を終了します。falseの場合、終了しません。  
@return void  

## 実行ファイル内の変数スコープについて
スコープが違うため、index.php上で定義した変数は、実行ファイル内から参照できません。  
参照したい場合は、getVar関数に変数を渡してください。実行ファイル内の$FileRoute->getVar関数で取得できます。

## 実行ファイル内変数
### $FileRouter
説明：FileRouterクラスの子クラスのインスタントです。FileRouterクラスのプロパティを引き継ぎます。

## 実行ファイル内関数
実行ファイル内で利用可能な関数です。変数$FileRouterのメソッドとして利用できます。

### $FileRoute->getSiteDir()
設定したサイトのディレクトリを返します。
@param void  
@return string ディレクトリ名  

### $FileRoute->getFilesDir()
実行ファイルのディレクトリを返します。  
@param void  
@return string 実行ファイルのディレクトリ。  

### $FileRoute->getRoutes()
全ルーティング情報を返します。  
@param void  
@return array ルーティング情報。  

### $FileRoute->getRoute($url)
URLパスに一致するルート先ファイル名を返します。  
@param string $url URL。  
@return string ルート先ファイル名。存在しない場合、空の文字列を返します。  

### $FileRoute->getVar($name)
実行ファイル参照値を取得します。  
@param string $name キー名。  
@return mixed|null 値。存在しない名前を指定した場合nullを返します。  
