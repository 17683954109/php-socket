<?php
error_reporting(E_ALL);  //  报告所有错误

set_time_limit(0);  //  去除最大执行时间限制

ob_implicit_flush();    //  关闭缓冲，有输出就即刻传输到客户端

$address = '0.0.0.0';   //  ip 地址
$port = 9999;       //  端口

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    /*
     *  socket_create() 创建套接字资源
     *
     *  参数1( domain ) :
     *         AF_INET  -> 使用ipv4 网络协议,
     *         AF_INET6 -> 使用ipv6 网络协议,
     *         AF_UNIX  -> 本地通讯协议
     *
     *  参数2( type ) :
     *         SOCK_STREAM    -> TCP 的连接协议, 一个顺序化的、可靠的、全双工的、基于连接的字节流;
     *         SOCK_DGRAM     -> UDP 的连接协议, 数据报文的支持(无连接，不可靠、固定最大长度);
     *         SOCK_SEQPACKET -> 一个顺序化的、可靠的、全双工的、面向连接的、固定最大长度的数据通信, 通过接收数据段获取整个数据包;
     *         SOCK_RAW       -> 读取原始的网络协议, ICMP 请求(ping);
     *         SOCK_RDM       -> 一个可靠的数据层, 但不保证到达顺序;
     *
     *  参数3( protocol ) :
     *         icmp -> 主要用于网关和主机报告错误的数据通信, 如: ping 命令;
     *         udp  -> 一个无连接的、不可靠的、具有固定最大长度的报文协议, 也可直接使用常量 SOL_UDP;
     *         tcp  -> 一个可靠的、基于连接的、面向数据流的全双工协议, 也可直接使用常量 SOL_TCP;
     * */
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
}

if (socket_bind($sock, $address, $port) === false) {
    /*
     * socket_bind() 给套接字绑定 ip 和 端口
     *
     * 参数1( socket ): socket_create() 创建的有效的 socket 资源
     *
     * 参数2( address ): ip 地址, socket_create() 时指定 AF_INET 则该参数为ipv4 的地址, AF_INET6 则为ipv6 地址
     *
     * 参数3( port ): 端口
     *
     **/
    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}

if (socket_listen($sock, 5) === false) {
    /*
     * socket_listen() 监听该套接字上的连接
     *
     * 参数1( socket ): 一个有效的 socket 资源
     *
     * 参数2( backlog ): 最多的排队等待连接, 连接人数已满后, 将允许该个数的连接进入等待队列, 其余的则连接失败或收到错误信息
     *
     * */
    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}

do {   //  循环接受新的连接加入

    if (($msgsock = socket_accept($sock)) === false) {
        /*
         * socket_accept() : 接受 socket 上的连接, 成功后返回新的 socket 资源, 该资源可用于通信,
         *                   新的 socket 资源不可再次socket_accept, 但旧的socket 资源可重复使用,
         *                   新的 socket 资源也就是客户端实例
         *
         * 参数( socket ): socket_listen 后的有效的套接字资源
         *
         **/
        echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
        break;
    }
    /* 给新加入的客户端发送欢迎信息 */
    $msg = "\nWelcome to the PHP Socket Server. \n" .
        "To quit, type 'quit'. To shut down the server type 'shutdown'.\n";
    socket_write($msgsock, $msg, strlen($msg));
    /*
     * socket_write() 写入套接字
     *
     * 参数1( socket ): 一个有效的可通信的 socket 资源;
     *
     * 参数2( buffer ): 要写入的数据;
     *
     * 参数3( length ): 写入的数据备用字节长度, 如果写入数据长度大于缓冲区, 则默认截断为缓冲区的大小,
     *                 指定该参数后则按照该长度进行截断( 最好指定为写入数据的实际长度, 防止截断后的数据不完整 )
     *
     * */

    do {   //  循环接受每一个已经连接的客户端消息, 并回应 -> 该客户端

        if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {  //  读取客户端的信息, 读取失败则打印错误信息
            /*
             * socket_read()  读取套接字
             *
             * 参数1( socket ): 一个有效的可用于通信的 socket 资源
             *
             * 参数2( length ): 读取的最大长度, 超过该长度则不会被读取, 导致数据不完整
             *
             * 参数3( type ): 读取方式( 可选 ):
             *                PHP_BINARY_READ( 默认 )  -> 使用 php 内置函数 recv() , 可以安全的读取二进制数据;
             *                PHP_NORMAL_READ         -> 在读取到 \r 或 \n 时停止读取;
             *
             * */
            echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($msgsock)) . "\n";
            break 2;
        }

        if (!$buf = trim($buf)) {  //  如果读取不到信息则进入下一次的读取, 成功则去除掉一些冗余字符
            /*
             * trim()  剔除首尾空白字符
             *
             * 参数1( str ): 待处理的字符串;
             *
             * 参数2( character_mask ): 要过滤的字符, 可使用 ".." 来表示范围,
             *                         如不指定, 则会默认剔除:
             *
             *                          " " (ASCII 32 (0x20))    -> 普通空格符;
             *                          "\t" (ASCII 9 (0x09))    -> 制表符;
             *                          "\n" (ASCII 10 (0x0A))   -> 换行符;
             *                          "\r" (ASCII 13 (0x0D))   -> 回车符;
             *                          "\0" (ASCII 0 (0x00))    -> 空字节符;
             *                          "\x0B" (ASCII 11 (0x0B)) -> 垂直制表符;
             *
             * */
            continue;
        }

        if ($buf == 'quit') {  //  定义quit 指令为端口与该客户端的连接
            //  跳出第二层循环, 也就是与客户端断开连接
            break;
        }

        if ($buf == 'shutdown') {  //  定义 shutdown 指令为关闭套接字服务
            //  跳出第一层循环, 也就是停止接收和发送数据, 关闭整个连接
            socket_close($msgsock);
            break 2;
        }

        if ($buf == 'hello'){  //  自定义的指令
            $msgs = "hello!\n";
            socket_write($msgsock,$msgs,strlen($msgs));
            break;
        }

        $talkback = "PHP: You said '$buf'.\n";   //  没有定义的指令, 原样返回
        socket_write($msgsock, $talkback, strlen($talkback));
        echo "$buf\n";  //  控制台打印客户端发送给服务器的信息

    } while (true);

    socket_close($msgsock);  //  关闭一个客户端的连接

} while (true);

socket_close($sock);  //  关闭整个套接字资源
