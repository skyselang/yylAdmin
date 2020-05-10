<h1 align="center">yylAdmin</h1>

[Github](./README.md) | 码云

## 简介

[yylAdmin](https://gitee.com/skyselang/yyl-admin) 是一个极简后台管理系统，前后端分离，后端采用ThinkPHP，前端采用Element；项目由[yyl-admin-php](https://gitee.com/skyselang/yyl-admin-php)和[yyl-admin-vue](https://gitee.com/skyselang/yyl-admin-vue)组成。

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

php
```bash
# 克隆项目
git clone https://gitee.com/skyselang/yyl-admin-php.git

# 进入项目目录
cd yyl-admin-php

# 安装依赖
composer install

# 可以通过如下操作解决 composer 下载速度慢的问题
composer config repo.packagist composer https://packagist.phpcomposer.com

# 配置环境（PhpStudy）
```
vue
```bash
# 克隆项目
git clone https://gitee.com/skyselang/yyl-admin-vue.git

# 进入项目目录
cd yyl-admin-vue

# 安装依赖
npm install

# 可以通过如下操作解决 npm 下载速度慢的问题
npm install --registry=https://registry.npm.taobao.org

# 启动服务
npm run dev
```
在 .env* 环境变量文件 修改接口地址

浏览器访问 http://localhost:7969

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

<img width="100%" src="./public/static/img/yyladmin.jpg">