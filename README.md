# BookShelf

BookShelfは、書籍・レビュー・読書計画を一元管理できるLaravel製の読書管理アプリケーションです。

書籍の登録や検索だけでなく、お気に入り、レビューへのいいね、ランキング、読書レポート、読了期限の通知など、継続的な読書管理を支援する機能を実装しています。

## 主な機能

### ユーザー機能

- ユーザー登録
- ログイン・ログアウト
- パスワード再設定
- プロフィール編集
- 二要素認証

### 書籍管理

- 書籍の一覧・詳細表示
- 書籍の登録・編集・削除
- ISBNを利用した書籍情報検索
- ジャンルの登録・編集・削除
- 書籍へのジャンル設定

### レビュー・お気に入り

- 書籍レビューの投稿・編集・削除
- 1ユーザーにつき同じ書籍へ1件まで投稿可能
- 他ユーザーのレビューへのいいね
- 自分のレビューへのいいね禁止
- 書籍のお気に入り登録・解除
- お気に入り書籍一覧

### 集計・ランキング

- 評価の高い書籍ランキング
- お気に入り数ランキング
- 読書計画数・読了冊数の集計
- 平均評価の表示
- 好きなジャンルの集計
- 高評価書籍・レビュー評価書籍のTOP5表示

### 読書計画・通知

- 読了期限と読書状態の管理
- 状態による読書計画の絞り込み
- 任意の通知日時設定
- 読了期限が近い書籍の通知
- 期限切れ計画の自動更新
- 通知一覧から対象書籍への移動

### API

- 書籍一覧・詳細の取得
- 書籍の登録・更新・削除
- レビュー情報を含むJSONレスポンス
- 入力エラー時のJSONレスポンス

## 使用技術

- PHP
- Laravel 10
- Laravel Fortify
- Laravel Sail
- MySQL 8
- Blade
- HTML / CSS
- JavaScript
- Vite
- PHPUnit
- Google Books API
- Docker

## 環境構築

### 1. リポジトリを取得

```bash
git clone https://github.com/o1tan/bookshelf.git
cd bookshelf

```

### 2. PHPパッケージをインストール

Composerを利用できる場合：

```bash
composer install
```

Composerをホスト環境に導入していない場合：

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php85-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. 環境設定ファイルを作成

```bash
cp .env.example .env
```

### 4. Dockerコンテナを起動

```bash
./vendor/bin/sail up -d
```

### 5. アプリケーションキーを生成

```bash
./vendor/bin/sail artisan key:generate
```

### 6. データベースを作成

```bash
./vendor/bin/sail artisan migrate --seed
```

### 7. フロントエンドを準備

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### 8. アプリケーションを開く

```text
http://localhost
```

## 読書計画の定期処理

通知時刻を迎えた計画への通知と、期限切れ計画の更新を実行します。

```bash
./vendor/bin/sail artisan reading-plans:process
```

本番環境では、Laravelのスケジューラーを定期実行する設定が必要です。

## テスト

### 全テスト

```bash
./vendor/bin/sail artisan test
```

### 特定テスト

```bash
./vendor/bin/sail artisan test --filter=ReadingPlanTest
```

### カバレッジ測定

```bash
XDEBUG_MODE=coverage ./vendor/bin/sail artisan test --coverage
```

最終確認時点：

- 82テスト成功
- 291アサーション成功
- コードカバレッジ 89.6%

## コード整形

```bash
./vendor/bin/sail pint
```

## 設計上の主な制約

- 同じユーザーは、同じ書籍へ複数のレビューを投稿できません。
- 自分のレビューにはいいねできません。
- 使用中のジャンルは削除できません。
- 読書計画は、同じユーザーと書籍の組み合わせで重複登録できません。
- 他ユーザーの書籍・レビュー・読書計画は変更できません。
- 通知は同じ読書計画に対して重複送信されません。

## ライセンス

本プロジェクトは学習用の模擬案件として制作しています。
