<h1>yylAdmin</h1>

[Github](https://github.com/skyselang/yylAdmin) | 码云

## 简介

[yylAdmin](https://gitee.com/skyselang/yylAdmin) 是一个极简后台管理系统，只有登录退出、权限管理、日志管理等基本功能，方便扩展；前后端分离，后端采用ThinkPHP6，前端采用Element2；项目由后端[yylAdmin](https://gitee.com/skyselang/yylAdmin)和前端[yylAdminWeb](https://gitee.com/skyselang/yylAdminWeb)组成。
- [yylAdmin](https://gitee.com/skyselang/yylAdmin) 
- [yylAdminWeb](https://gitee.com/skyselang/yylAdminWeb)

## 要求

- PHP >= 7.1
- MySQL >= 5.6
- Redis

## 准备

- [Git](https://git-scm.com/) 
- [Node](https://nodejs.org/zh-cn/) 
- [Composer](https://www.phpcomposer.com/) 
- [ThinkPHP](https://www.kancloud.cn/manual/thinkphp6_0/1037479) 
- [Element](https://element.eleme.cn/#/zh-CN/component/installation) 
- [PhpStudy](https://www.xp.cn/) 

## 开发

PHP部分
```bash
# 克隆项目
git clone https://gitee.com/skyselang/yylAdmin.git

# 进入项目目录
cd yylAdmin

# 安装依赖
composer install

# 可以通过composer镜像解决速度慢的问题
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# 配置环境（PhpStudy）

# 导入数据库
数据库文件：public/private/yyladmin.sql

```
WEB部分
```bash
# 克隆项目
git clone https://gitee.com/skyselang/yylAdminWeb.git

# 进入项目目录
cd yylAdminWeb

# 安装依赖
npm install

# 可以通过npm镜像解决速度慢的问题
npm install --registry=https://registry.npm.taobao.org

# 启动服务
npm run dev
```
在 .env* 环境变量文件 修改接口地址

浏览器访问 http://localhost:9527

账号：yyladmin，密码：123456

## 发布

```bash
# 构建测试环境
npm run build:stage

# 构建生产环境
npm run build:prod
```

## 其它

```bash
# 预览发布环境效果
npm run preview

# 预览发布环境效果 + 静态资源分析
npm run preview -- --report

# 代码格式检查
npm run lint

# 代码格式检查并自动修复
npm run lint -- --fix
```
<img width="100%" src="./public/static/img/yyladmin_login.jpg">

<img width="100%" src="./public/static/img/yyladmin.jpg">