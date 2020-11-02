<?php
/**
 * WebSocket class
 * author Walter Liang: https://github.com/sgyouyou
 * refer to: https://wiki.swoole.com/#/start/start_http_server
 */

$http = new Swoole\Http\Server("0.0.0.0", 8811);

// setting static folder
$http->set([
    "enable_static_handler" => true,
    "document_root" => "/work/Barrage-chat-room", // 访问方式: http://127.0.0.1:8811/index.html index.html是这个目录下的文件
]);

$http->on("request", function($request, $response) {
    $response->end(json_encode($request->get));
});

$http->start();