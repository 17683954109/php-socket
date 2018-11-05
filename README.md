#### PHP - Socket

+ TCP 长连接
+ UDP 音视频流
+ ICMP 原始连接( ping )

###### 查看示例
+ 首先必须编译安装 php 时指定 --enable-sockets 参数
+ 或者下载并解压 php 的源码包, 并进入到 ext/sockets/ 目录, 使用 phpize 安装
  + ```
     cd ext/sockets/
    ```
  + ```
     (your php install directory)/bin/phpize
     
      // 不知道安装目录的话可以使用以下命令来查找
      
      whereis phpize
    ```
  + ```
      make
    ```
  + ```
      make install
    ```
  + ```
     echo 'extension=php_sockets.dll' >> (your php.ini directory)/php.ini
     
     //  不知道安装目录可以使用以下命令来查找
    
     whereis php.ini
    ``` 

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
