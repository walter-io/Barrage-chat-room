<?php
// 入口文件

// 开启自动加载
spl_autoload_register();

// 开启swoole
$sw = new MySwoole\WebSocket();
$sw->run();

/**
 * 后面的工作：
 * 1. 新建数据库
 * 2. 测试redis是否能用
 * 3. 把聊天记录放到mysql
 * 4. 把客户端连接信息放到redis，并实时统计在线人数
 */

