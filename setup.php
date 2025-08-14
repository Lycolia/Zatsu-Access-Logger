<?php
$dbFile = 'database.db';

try {
    // SQLiteデータベースへの接続（ファイルが存在しない場合は自動作成）
    $pdo = new PDO('sqlite:' . $dbFile);

    // エラーモードを例外に設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "データベースファイル '{$dbFile}' に接続しました。\n";

    // テーブル作成のSQL文
    $sql = "CREATE TABLE IF NOT EXISTS access_log (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        trackingId TEXT,
        url TEXT,
        referer TEXT,
        ipaddr TEXT,
        useragent TEXT,
        timestamp TEXT
    )";

    // SQL文の実行
    $pdo->exec($sql);

    echo "テーブル 'access_log' を作成しました。\n";

    // テーブル構造の確認（オプション）
    $query = "PRAGMA table_info(access_log)";
    $stmt = $pdo->query($query);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\nテーブル構造:\n";
    echo "-------------------\n";
    foreach ($columns as $column) {
        echo "列名: {$column['name']}, 型: {$column['type']}\n";
    }

    // データベース接続を閉じる
    $pdo = null;

    echo "\nデータベースの作成が完了しました。\n";

} catch (PDOException $e) {
    // エラーハンドリング
    echo "エラーが発生しました: " . $e->getMessage() . "\n";
    exit(1);
}

// ファイルの存在確認
if (file_exists($dbFile)) {
    echo "データベースファイル '{$dbFile}' が正常に作成されました。\n";
} else {
    echo "データベースファイルの作成に失敗しました。\n";
}
