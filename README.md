2017年6月11日，51zwd.com的主站项目代号正式更改为：warriors（勇士）。

# 本地开发 #

warriors代码与原先的svn版本库完全一致，包括历史提交记录也一同迁移了过来。唯一的区别是将项目根目录下的data目录从版本控制中移除了。因为data目录主要是环境相关的配置以及运行过程中动态生成的配置文件，不适合提交至git。

## 开发环境搭建手册 ##

### 基础软件准备工作 ###

1. 安装MySQL 5.5～5.6
2. 安装memcached ~1.4
3. 安装Apache Httpd ~2.4
4. 安装PHP ~5.3 (包含插件：mysql、memcache(注意不是memcached，没有d))
5. 配置Apache Httpd的httpd.conf，开启rewrite和php5 module，设置DocumentRoot的AllowOverride为All，确保目录级别的.htaccess可以起作用
6. 配置php.ini的`error_reporting = E_ALL & ~E_NOTICE`

### 数据库准备工作 ###

1. 在本地的数据库中创建2个database，分别是ecmall51\_2和ucenter51：`create database ecmall51_2; create database ucenter51;`
2. 从[百度云](http://pan.baidu.com)下载数据库初始化脚本，用户名：51网设计，密码：51wangsheji！，路径：/数据库初始化脚本/ecmall51_2.sql和ucenter51.sql
3. 通过mysql的source命令，将ecmall51\_2.sql导入本地数据库ecmall51\_2，将ucenter51.sql导入本地数据库ucenter51

### 代码准备工作 ###

在Apache Httpd的DocumentRoot目录（本地网站访问根路径）下，执行以下步骤：

1. `git clone ssh://git@121.199.182.35:30002/wengxj/warriors.git warriors`
2. `git clone ssh://git@121.199.182.35:30002/wengxj/cavaliers.git cavaliers`
3. 下载warriors的初始化data包，放在warriors根目录下解压
4. 解压之后，修改data目录下的config.inc.php: `SITE_URL`、`DB_CONFIG`、`UC_DBHOST`、`UC_DBUSER`、`UC_DBPW`、`UC_API`、`UC_IP`都修改成本地开发环境对应的值
5. 将warriors根目录下的`.htaccess.example`重命名为`.htaccess`
6. 下载cavaliers的初始化data包，放在cavaliers根目录下解压
7. 解压之后，修改data目录下的config.inc.php: `UC_DBUSER`、`DB_DBPW`都修改成本地开发环境对应的值

### 运行整套系统 ###

1. 启动mysql
2. 启动memcached，监听端口为12000
3. 启动Apache Httpd
4. 访问warriors首页验证

### 本地不能使用的功能 ###

1. 搜索：需要搭建sphinx，普通开发人员不需要开发搜索相关功能
2. 淘宝接口相关功能：淘宝接口绑定了生产ip白名单，其他地方无法使用
3. 支付宝接口相关功能：支付宝接口绑定了生产ip白名单，其他地方无法使用
4. 数据采集与更新：本地不需要

# 测试环境部署 #

只要把git提交push到gitlab中的warriors库中，就会触发jenkins中的[warriors-deploy-test任务](http://116.62.31.61:8080/job/warriors-deploy-test/)，该任务会在测试环境上拉取最新代码并进行部署，从而达到了自动化部署的目的。

# 生产环境部署 #

jenkins中有一个[warriors-deploy-prod任务](http://116.62.31.61:8080/job/warriors-deploy-prod/)，该任务需要手工触发，会在生产环境上拉取最新代码并进行部署。

# 代码规范 #

关于warriors新增代码几点建议：

## 代码格式 ##

```php
/**
 * 参考下面方法，便于生成doc、test
 */
class My_moneyApp extends MemberbaseApp{

    /**
     * @name 名字（说明干什么用的）
     * @param 定义函数或者方法的参数信息
     * @author zhangshan 2017-07-11
     * @todo 指明应该改进或没有实现的地方
     *  还可以添加更多
     */
    function ajax_edit(param1,param2) {
        // 业务逻辑，不一定要求有详细设计文档，建议有概要设计，负责人把把关
        // 万一造成损失，怎么办？
    }
    ....
}
```

## 数据库设计 ##

一般不要在别人设计的表中，修改和删除字段信息，应该新建关联表（在brown项目中新建一个sql文件，可参照ecm_order.sql中的格式做好文档工作 ），便于系统升级维护和尊重他人成果。

## 欢迎大家补充... ##
