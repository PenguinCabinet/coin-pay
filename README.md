# coin-pay

> [!WARNING]
> セキュリティ上、重大な脆弱性を抱えている可能性が高いです     
> 絶対に本番環境での運用をしないでください

PHPの勉強用に作成しました。   
PHPを学ぶため、フレームワークを使わずに、PHPで実装しました。

![img]()    

## パス説明
- /home.php - ユーザーホーム画面。ユーザーID、ユーザー名、現在の所有コイン数が取得できる。ユーザーログインをしていなくてもアクセスできるが、ユーザーデータを取得できず、ログインを求めるHTMLが表示される
- /register.php - ユーザー登録画面。ユーザー登録ができる。ユーザー登録後、/homeへリダイレクトされる。
- /login.php - ユーザーログイン画面。ユーザーログインができる。ログインに成功すると、/homeへリダイレクトされる。ログインに失敗した場合、リダイレクトされない
- /pay.php - 送金画面。送金先ユーザのIDと送金金額を指定する。ユーザーログインしていなくてもアクセスできるが、送金に必ず失敗する
- /api/register.php - ユーザー登録を受け付けるapi。ユーザー名とパスワードをPOSTし、登録する。登録が完了すると作ったアカウントのトークンを返す。トークンの有効期限は1時間
- /api/login.php - ログインを受け付けるapi。ユーザー名とパスワードをPOSTし、ログインする。ログインが完了すると、アカウントのトークンを返す。トークンの有効期限は1時間
- /api/home.php - /homeのデータを取得するためのapi。ヘッダーAuthorizationのBearerにトークンを付与して送信する
- /api/pay.php - 送金を受け付けるapi。トランザクションを使用しており、送金が失敗した場合ロールバックし、原子性を担保している。ヘッダーAuthorizationのBearerにトークンを付与して送信する

## 内部構造の説明
- ~~ログイン時に取得できる認可用のtokenはlocalstorageに保存しています~~
  - [XSS攻撃の恐れがあるため](https://cheatsheetseries.owasp.org/cheatsheets/HTML5_Security_Cheat_Sheet.html#:~:text=A%20single%20Cross%20Site%20Scripting%20can%20be%20used%20to%20steal%20all%20the%20data%20in%20these%20objects%2C%20so%20again%20it%27s%20recommended%20not%20to%20store%20sensitive%20information%20in%20local%20storage.)、[PHPのSESSION機能を使う方法](https://www.php.net/manual/ja/book.session.php)に[変更しました](https://github.com/PenguinCabinet/coin-pay/pull/4)。内部的にはcookieにセッションIDが保管されています
- データベースはSQLliteを使用しています
  - usersテーブルにユーザー名、ユーザーID、パスワードのハッシュが保存されています
  - users_dataテーブルにユーザーの所有するコイン数を保存しています

## 使い方
### 初期化
```bash
php config/init.php
```

```bash
php -S localhost:8000 -t public
```
