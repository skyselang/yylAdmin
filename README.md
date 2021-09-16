# yylAdmin

- [Gitee](https://gitee.com/skyselang/yylAdmin) | [Github](https://github.com/skyselang/yylAdmin)

## 文档

- [yylAdmin文档](http://skyselang.gitee.io/yyladmindoc/)

## 简介

yylAdmin是一个基于ThinkPHP6和Vue2极简后台管理系统，只有登录退出、权限管理、日志管理、接口文档与调试等基础功能；前后台基础框架；你可以在此基础上根据你的业务需求进行开发扩展。前后分离，简单轻量，免费开源，开箱即用。

- [yylAdmin](https://gitee.com/skyselang/yylAdmin) | [yylAdminWeb](https://gitee.com/skyselang/yylAdminWeb)

## 功能

- 登录退出
- 权限管理
- 日志管理
- 实用工具
- 微信管理
- 内容管理
- 接口文档与调试
- 前台基础功能：登录注册、微信登录、Token认证、接口管理、会员管理...  
- 更多功能请安装后体验

## 演示

- 地址：[yylAdmin demo](http://47.98.132.71:9527) 
- 账号：yyladmin、admin、demo、php  
- 密码：123456  
- 提示：演示账号只有部分权限，请安装后体验全部功能
- 前台：[yylAdmin index](http://47.98.132.71:8081) 
## 安装

### 环境要求

- PHP >= 7.2.5  
  安装 fileinfo、redis 扩展  
  开启 putenv、proc_open 函数
- MySQL >= 5.5
- Redis
- node >= 10.15.0
- npm >= 5.6.0

### 安装后端

```bash
# 克隆项目
git clone https://gitee.com/skyselang/yylAdmin.git

# 进入项目目录
cd yylAdmin

# 设置composer
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# 安装依赖
composer install

# 导入数据库
数据库文件：private/yyladmin.sql

# 修改配置
重命名.env.example成.env环境变量文件修改里面配置（或修改config文件夹里面的配置）

# 环境配置（phpStudy）
根据你的实际环境软件配置
域名：localhost
端口：9526
根目录：yylAdmin/public
PHP版本：7.3.9
伪静态Nginx：
location / {
    if (!-e $request_filename){
        rewrite  ^(.*)$  /index.php?s=$1  last;   break;
    }
}
```

### 安装前端

```bash
# 克隆项目
git clone https://gitee.com/skyselang/yylAdminWeb.git

# 进入项目目录
cd yylAdminWeb

# 使用cnpm
npm install -g cnpm --registry=https://registry.npm.taobao.org

# 安装依赖
cnpm install

# 修改配置
在.env.xxx环境变量文件里面修改接口地址（后端环境配置域名端口）
VUE_APP_BASE_API = 'http://localhost:9526'

# 本地开发 启动项目
cnpm run dev

# 开发完打包正式环境
cnpm run build:prod

# 开发完打包测试环境
cnpm run build:stage
```

### 访问后台

地址：[http://localhost:9527](http://localhost:9527)  
账号：yyladmin  
密码：123456  
超管：skyselang  
密码：123456

## 预览

- ![login](./public/static/img/yyladmin_login.jpg)
- ![index](./public/static/img/yyladmin.jpg)

## 支持

- 如果本项目对您有所帮助，请点个Star支持我们  

- [Gitee](https://gitee.com/skyselang/yylAdmin)![Gitee](https://gitee.com/skyselang/yylAdmin/badge/star.svg)
- [Github](https://github.com/skyselang/yylAdmin)![Github](https://img.shields.io/github/stars/skyselang/yylAdmin)

## 反馈

- 有任何疑问或者建议，请提 [Issue](https://gitee.com/skyselang/yylAdmin/issues)

## 协议

- Apache-2.0许可协议，免费开源  
- Copyright skyselang https://gitee.com/skyselang
