可以按照以下步骤来部署和运行程序:
1.请确保机器huangjiachu@meizudeMacBook-Pro.local已经安装了Yaf框架, 并且已经加载入PHP;
2.把yaf_api目录Copy到Webserver的DocumentRoot目录下;
3.需要在php.ini里面启用如下配置，生产的代码才能正确运行：
	yaf.environ="product"
4.重启Webserver;
5.访问http://yourhost/yaf_api/,出现Hellow Word!, 表示运行成功,否则请查看php错误日志;
6.common.php包含加载APP/liblrary类的封装、全局Yaf_Registry的封装;
7.实现了Redis的封装;
8.文件日志;
9.分布式session，可以实现存储接口，替换session保存的介质;
