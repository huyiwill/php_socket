<?php
// Socket举例：简单的TCP/IP服务器
// 改变地址和端口以满足你的设置和执行。
// telnet 192.168.1.53 10000连接到服务器，（这里是你设置的地址和端口）。 //输入任何东西都会在服务器端输出来，然后回显给你。
// 断开连接，请输入'quit'。

error_reporting(E_ALL);

/* 允许脚本挂起等待连接。 */
set_time_limit(0);

//socket_close($sock);die;
/* 打开绝对隐式输出刷新 */
ob_implicit_flush();

$address = '127.0.0.1';
$port    = 10001;

/* 产生一个socket，相当于产生一个socket的数据结构 */
if(($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false){
    echo "socket_create() 失败的原因是: " . socket_strerror(socket_last_error()) . "\n";
}

/* 把socket绑定在一个IP地址和端口上 */
if(socket_bind($sock, $address, $port) === false){
    echo "socket_bind() 失败的原因是: " . socket_strerror(socket_last_error($sock)) . "\n";
}

/* 监听指定socket的所有连接 */
if(socket_listen($sock, 5) === false){
    echo "socket_listen() 失败的原因是: " . socket_strerror(socket_last_error($sock)) . "\n";
}

do{
    /* 接受一个Socket连接 */
    if(($msgsock = socket_accept($sock)) === false){
        echo "socket_accept() 失败的原因是: " . socket_strerror(socket_last_error($sock)) . "\n";
        break;
    }

    $msg = "\nWelcome to the PHP Test Server. " . "To quit, type 'quit'. To shut down the server type 'shutdown'.\n";
    /* 写数据到socket缓存 */
    socket_write($msgsock, $msg, strlen($msg));

    do{
        /* 读取指定长度的数据 */
        if(false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))){
            echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($msgsock)) . "\n";
            break 2;
        }

        if(!$buf = trim($buf)){
            continue;
        }
        if($buf == 'quit'){
            break;
        }

        if($buf == 'shutdown'){
            socket_close($msgsock);
            break 2;
        }
        $talkback = "PHP: You said '$buf'.\n";
        socket_write($msgsock, $talkback, strlen($talkback));
        echo "$buf\n";
    }while(true);

    /* 关闭一个socket资源 */
    socket_close($msgsock);
}while(true);

socket_close($sock);
