$(function () {
    // define name 进来先写名字
    var name = $("input[name=user_name]")
    if (name.val() == "") {
        var name_ipt = prompt("please input your name 请输入名字")
        name.val(name_ipt)
    }

    // websocket 开启websocket
    var webSocket = beginSocket()
    if (!webSocket) {
        alert("not support websocket 您的浏览器不支持WebSocket")
        return
    }

    // click send message 点击发送消息
    $(".send").on("click", function() {
        var data = {
            name: name.val(),
            content: $(".message").val()
        }
        webSocket.send(Base64.encode(JSON.stringify(data)))
    })
})

// websocket event websocket相关事件处理
function beginSocket() {
    if (window.WebSocket) {
        // connect,that address refer to swoole.php 连接,这里的地址要对应swoole.php里面定义的地址和端口
        var webSocket = new WebSocket("ws://www.mychat.com:9502") // 修改这里可能有缓存
        console.log('websocket: ', webSocket)

        // open 开启事件
        webSocket.onopen = function (event) {
            // webSocket.send("Hello,WebSocket!")
        }

        // message 监听消息
        webSocket.onmessage = function (event) {
            onMessage(event)
        }
        return webSocket
    } else {
        return false
    }
}

/**
 * read message 监听到消息
 * @param $text
 */
function onMessage(event) {
    var content = $(".content")
    // data = JSON.parse(Base64.decode(event.data))
    // data = JSON.parse(window.atob(event.data))
    data = JSON.parse(window.atob(event.data))
    res = data.data
    console.log('result', res)
    if (res.content != undefined && res.name == $("input[name=user_name]").val()) {
        // own message 自己发的消息
        var temp = $(".r-row-temp").clone().eq(0)
    } else {
        // other's message 别人发的消息
        var temp = $(".row-temp").clone().eq(0)
    }
    temp.find(".word").html(res.content)
    temp.find(".username").html(res.name)
    temp.css("display", "block")
    // append to chat div 添加到聊天框
    content.append(temp)
    // bullet screen 发弹幕
    bulletScreen(res.content)
}

/**
 * bullet screen 发送弹幕
 * @param $text
 */
function bulletScreen($text) {
    var colors = ["red", "green", "orange", "yellow", "hotpink", "purple", "cyan"]
    var randomColor = parseInt(Math.random() * colors.length)
    var randomY = parseInt(Math.random() * 400)
    $("<span class='dialog'></span>")
        .text($text)                            // content 设置内容
        .css("color", colors[randomColor])      // color 设置字体颜色
        .css("left", 1400)                      // left size 设置left值
        .css("top", randomY)                    // top size 设置top值
        .animate({left: -500}, 10000, "linear", function () {
            // in the end delete that 到了终点，需要删除
            $(this).remove()
        })
        .appendTo(".main")
}