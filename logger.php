<?php
function setTrakingIdCookie() {
    $id = uniqid('', true);
    setcookie('trcid', $id);
    return $id;
}

function getSafeKey($obj, $key) {
    return isset($obj[$key]) ? $obj[$key] : '';
}

function createLogObj($trakingId) {
    return [
        'trackingId' => $trakingId,
        'url' => getSafeKey($_SERVER, 'HTTP_REFERER'),
        'referer' => getSafeKey($_SERVER, 'QUERY_STRING'),
        'ipaddr' => getSafeKey($_SERVER, 'REMOTE_ADDR'),
        'useragent' => getSafeKey($_SERVER, 'HTTP_USER_AGENT'),
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

function insertLog($logObj) {
    $pdo = new PDO('sqlite:database.db');
    $insertSql = 'INSERT INTO access_log (trackingId, url, referer, ipaddr, useragent, timestamp) VALUES (:trackingId, :url, :referer, :ipaddr, :useragent, :timestamp)';
    $stmt = $pdo->prepare($insertSql);

    $stmt->execute($logObj);

    // データベース接続を閉じる
    $pdo = null;
}

function sent_headers() {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Content-Type: image/gif');
    header('Content-Length: 43');
}

// 1x1透明GIFのバイナリをそのまま出力
function sent_tracking_gif() {
    echo "\x47\x49\x46\x38\x39\x61" // GIF89a
        . "\x01\x00\x01\x00"         // 1x1サイズ
        . "\x80\x00\x00"             // グローバルカラー: 1色
        . "\x00\x00\x00"             // カラー #0: 黒
        . "\xFF\xFF\xFF"             // カラー #1: 白
        . "\x21\xF9\x04\x01\x00\x00\x00\x00" // グラフィック制御拡張 (透明)
        . "\x2C\x00\x00\x00\x00\x01\x00\x01\x00\x00" // 画像ディスクリプタ
        . "\x02\x02\x44\x01\x00"     // 画像データ (LZW圧縮)
        . "\x3B";                    // GIF終了
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $trakingId = getSafeKey($_COOKIE, 'trcid');
    if ($trakingId === '') {
        $trakingId = setTrakingIdCookie();
    }

    // ログ記録
    $log_data = createLogObj($trakingId);
    insertLog($log_data);
}

sent_headers();

sent_tracking_gif();
