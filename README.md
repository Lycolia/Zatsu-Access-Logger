# Zatsu Access Logger

何もかも雑なアクセスロガー。

## 機能

- ユーザートラッキングCookieの発行
- アクセスログの記録

## 記録できる内容

- トラッキングID
- アクセスされたURL
- アクセスしてきたIPアドレス
- アクセスしてきたUserAgent
- アクセス時刻

## 動作確認環境

- PHP 8.3.8
- さくらのレンタルサーバー スタンダードプラン
- CDNなし

## 使い方

1. 適当にディレクトリを作って配置
2. `php setup.php`を実行し、記録用のDBを作成
3. 次の形式でHTMLに埋め込む
   `<img src="https://example.com/cgi-bin/access_logger/logger.php?<ここにリファラの値>">`

### 埋め込み例

いずれの場合もHEADタグの中に以下を記述する必要がある。記述しない場合、urlがホスト名しか取れなくなる。

```html
<meta name="referrer" content="no-referrer-when-downgrade"/>
```

それぞれ`</body>`の前など、適当な位置に記述する。

#### 素のHTMLの場合

HTMLファイルの拡張子を`.html`から`.shtml`にリネームし、SSIを利用して記述する。

```html
<img src="https://example.com/cgi-bin/access_logger/logger.php?<!--#echo var="HTTP_REFERER" -->">
```

#### PukiwikiなどのPHPスクリプト

`skin/pukiwiki.skin.php`などに以下を記述。

```html
<img src="https://example.com/cgi-bin/access_logger/logger.php?<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''; ?>">
```

#### adiary Extends

[adiary Extends](https://github.com/Lycolia/adiary-extends)を利用する場合、Version 0.4.1以降が必要。

`skel.local/_frame.html`に以下を記述。

```html
<img src="https://example.com/cgi-bin/access_logger/logger.php?<@v.HttpReferer>">
```

## ログの見方

`sqlite3 database.db`で記録用DBを開き、適当にクエリを投げる。

**参考例：**

```sql
SELECT * FROM access_log;
```

## 記録DBのカラム

全てTEXT型。

| カラム名   | 役割                      |
| ---------- | ------------------------- |
| id         | プライマリキー            |
| trackingId | トラッキングID            |
| url        | アクセスされたURL         |
| referer    | リファラ                  |
| ipaddr     | クライアントのIPアドレス  |
| useragent  | アクセスしてきたUserAgent |
| timestamp  | 記録日時                  |
