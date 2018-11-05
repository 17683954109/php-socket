#### PHP - Socket

+ TCP 长连接
+ UDP 音视频流
+ ICMP 原始连接( ping )

###### 查看示例
+ 首先必须编译安装 php 时指定 --enable-sockets 参数
+ 或者进入到 sockets 目录, 使用 phpize 安装
  + make
  + make install

+ 克隆仓库到本地  
```
 git clone https://github.com/17683954109/php-socket.git
```
+ 进入该目录
```
 cd php-socket/
```
+ CLI模式运行
```
 php php_socket.php
```
+ Linux下进行测试
```
 telnet 0.0.0.0 9999
```
