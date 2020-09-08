<?php
/**
 * WebSocket class
 * author Walter Liang: https://github.com/sgyouyou
 * refer to: https://wiki.swoole.com/#/start/start_ws_server
 */

class WebSocket
{
    protected $ws;

    public function __construct()
    {
        $this->ws = new Swoole\WebSocket\Server("0.0.0.0", 9502);
        $this->ws->onOpen();
        $this->ws->onMessage();
        $this->ws->onClose();
    }

    /**
     * event: open 开启事件，用于开启时候传给前端
     */
    public function onOpen()
    {
        // Happens when open websocket 监听WebSocket连接打开事件
        $this->ws->on('open', function ($ws, $request) {
            $ws->push($request->fd, "ready? it's coming 准备好了吗，来咯来咯" . PHP_EOL);
        });
    }

    /**
     * listen message 监听消息
     */
    public function onMessage()
    {
        $this->ws->on('message', function ($ws, $frame) {
            $msg = $frame->data . PHP_EOL;
            // Sends a message to all connected windows
            $start_fd = 0;
            while (true) {
                // connection_list函数获取现在连接中的fd
                $conn_list = $ws->connection_list($start_fd, 100);   // 获取从fd之后一百个进行发送
                if ($conn_list === false || count($conn_list) === 0) {
                    echo "finish" . PHP_EOL;
                    return;
                }

                $start_fd = end($conn_list);
                foreach ($conn_list as $fd) {
                    $ws->push($fd, $msg);
                }
            }
        });
    }

    /**
     * event: close, close connect 关闭事件, 断掉连接
     */
    public function onClose()
    {
        $this->ws->on('close', function ($ws, $fd) {
            echo "client-{$fd} is closed\n";
            $ws->close($fd);   // 销毁fd链接信息
        });
    }
}

$webSocket = new WebSocket();
$webSocket->start();