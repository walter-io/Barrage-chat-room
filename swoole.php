<?php
/**
 * WebSocket class
 * author Walter Liang: https://github.com/sgyouyou
 * refer to: https://wiki.swoole.com/#/start/start_ws_server
 */

namespace MySwoole;

class WebSocket
{
    protected $ws;

    public function __construct()
    {
        $this->ws = new Swoole\WebSocket\Server("0.0.0.0", 9502);
        $this->run();
    }

    /**
     * boot 启动
     */
    public function run()
    {
        $this->onOpen();
        $this->onMessage();
        $this->onClose();
        $this->ws->start();
    }

    /**
     * event: open 开启事件，用于开启时候传给前端
     */
    public function onOpen()
    {
        // Happens when open websocket 监听WebSocket连接打开事件
        $this->ws->on('open', function ($ws, $request) {
            // 用户信息
            $clientInfo = $ws->getClientInfo($request->fd);
            $ws->push($request->fd, $this->encodeStruct(0, '', [
                'name' => '',
                'content' => "ready? it's coming 准备好了吗，来咯来咯"
            ]));
        });
    }

    /**
     * listen message 监听消息
     */
    public function onMessage()
    {
        $this->ws->on('message', function ($ws, $frame) {
            $receive = $this->getData($frame->data);
            // Sends a message to all connected windows
            $start_fd = 0;
            while (true) {
                // connection_list函数获取现在连接中的fd
                $conn_list = $ws->connection_list($start_fd);
                if ($conn_list === false || count($conn_list) === 0) {
                    echo "finish" . PHP_EOL;
                    return;
                }
                $start_fd = end($conn_list);
                foreach ($conn_list as $fd) {
                    $ws->push($fd, $this->encodeStruct('0', '', [
                        'name'    => $receive['name'],
                        'content' => $receive['content']
                    ]));
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
//            echo "client-{$fd} is closed\n";
            $ws->close($fd);   // 销毁fd链接信息
        });
    }

    /**
     * 返回结构加密
     * @param string $code
     * @param string $msg
     * @param array
     * @return string
     */
    public function encodeStruct($code = '0', $msg = '', $data = [])
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
        return base64_encode(json_encode($result));
    }

    /**
     * 返回结构解密
     * @param $data
     * @return mixed
     */
    public function getData($data)
    {
        return json_decode(base64_decode($data), true);
    }
}

//$webSocket = new WebSocket();
//$webSocket->run();