<?php
//  报告所有错误
error_reporting(E_ALL);

//  去除 php 最大执行时间限制
set_time_limit(0);

//  关闭缓冲
ob_implicit_flush();

$address = '0.0.0.0'; // ip
$port = 9999;  // 端口

//  创建套接字资源
if(($socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP)) === false){
    // 创建套接字资源失败, 打印错误信息
    echo 'Create Socket Resource Error : '.socket_strerror(socket_last_error())."\n";
}

//  绑定套接字ip 和端口
if((socket_bind($socket,$address,$port)) === false){
    //  绑定ip 地址和端口失败, 打印错误信息
    echo 'Bind Address And Port Failed : '.socket_strerror(socket_last_error())."\n";
};

//  创建套接字侦听
if ((socket_listen($socket,5)) === false){
    echo 'Listen The Socket Resource Failed : '.socket_strerror(socket_last_error())."\n";
}

$is_send_shutdown = false;

//  开始循环接受新的连接加入
do{
    //  接受新的连接
    if (($msg_sock = socket_accept($socket)) === false){
        echo 'Socket Accept Failed : '.socket_strerror(socket_last_error())."\n";
    }

    //  发送欢迎信息
    socket_write($msg_sock,"\n");
    $msg =
        <<<STR
    Welcome To PHP Socket Server : 
    Type 'quit' To Disconnect This Server,
    Type 'shutdown' To Close This Server.
STR;

    socket_write($msg_sock,$msg,strlen($msg));
    socket_write($msg_sock,"\n");
    echo 'A New Client Connect : '.$msg_sock."\n";

    do{   //  监听客户端发送的消息, 并回应该客户端

        //  读取客户端信息
        if (false === ($req = socket_read($msg_sock,2048,PHP_NORMAL_READ))){
            echo 'Read The Client Msg Error : '.socket_strerror(socket_last_error())."\n";
        }

        //  剔除冗余字符
        if (!($req = trim($req))){
            //  没有读取到数据
            continue;
        }

        //  获取 quit 指令
        if ($req == 'quit'){
            echo 'A Client Disconnect : '.$msg_sock."\n";
            break;
        }

        //  获取 shutdown 指令
        if ($req == 'shutdown'){
            $msg = 'Please Confirm Your Admin Password : '."\n";
            socket_write($msg_sock,$msg,strlen($msg));
            $is_send_shutdown = true;
            continue;
        }

        //  获取用户输入的关闭密码
        if ($is_send_shutdown===true){
            if ($req == 'exit'){
                $msg = 'Exited The Shutdown .'."\n";
                $is_send_shutdown = false;
                socket_write($msg_sock,$msg,strlen($msg));
                continue;
            }
            if ($req == '990222'){
                echo 'The Socket Server Close'."\n";
                break 2;
            }else{
                $msg = 'Password Error , Please Try Again ...'."\n";
                $msg .= "Type 'exit' To Exit Shutdown \n";
                socket_write($msg_sock,$msg,strlen($msg));
                continue;
            }
        }

        //  没有特殊指令, 输出原始数据
        $msg = 'Server : '.$req."\n";
        echo 'Client -> '.$msg_sock.' : '.$req."\n";
        socket_write($msg_sock,$msg,strlen($msg));
    }while(true);

    //  用户输入quit 指令, 关闭该用户的连接
    socket_close($msg_sock);

}while(true);

//  用户输入shutdown 指令, 关闭整个socket 资源连接
socket_close($socket);
