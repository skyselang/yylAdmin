<div align="center">

## 🎉 正在参加 Gitee 2025 最受欢迎开源软件评选

<a href="https://gitee.com/activity/2025opensource?ident=IZQ0KG" target="_blank">
  <img src="https://img.shields.io/badge/🗳️_立即投票-支持本项目-ff6b35?style=for-the-badge&logo=gitee" alt="投票" height="50"/>
</a>

<p>
  <strong>一票就够，不用每天投 🙏 您的支持是我们持续更新的最大动力！</strong>
</p>

<p>
  <a href="https://gitee.com/activity/2025opensource?ident=IZQ0KG" target="_blank">
    <strong>👉 点击徽章或这里投票 👈</strong>
  </a>
</p>

</div>

# yylAdmin

- [码云](https://gitee.com/skyselang/yylAdmin) | [Github](https://github.com/skyselang/yylAdmin)

## 文档

- [开发文档](https://www.yyladmin.top/)

## 简介

yylAdmin 是一个基于 ThinkPHP 和 Vue 极简后台管理系统，只有登录退出、权限管理、系统管理、接口文档与调试等基础功能；前后台基础框架；你可以在此基础上根据你的业务需求进行开发扩展。前后分离，简单轻量，免费开源，开箱即用。

- 后端：[yylAdmin](https://gitee.com/skyselang/yylAdmin) | 前端：[yylAdminWeb](https://gitee.com/skyselang/yylAdminWeb)

## 功能

- 控制台
- 会员管理
- 内容管理
- 文件管理
- 日志管理
- 设置管理
- 系统管理：权限管理、组织架构...
- 导出导入
- 代码生成器
- 接口文档与调试...
- 前台基础功能：注册登录、微信登录、Token 认证...
- 国际化：中英文
- 暗黑模式
- 更多功能请安装后体验

| 版本 | 分支                                                | 框架                          | 环境          |
| ---- | --------------------------------------------------- | ----------------------------- | ------------- |
| v5   | [master](https://gitee.com/skyselang/yylAdmin)      | thinkphp8.1 vue3 element-plus | php8.2 node20 |
| v4   | [v4](https://gitee.com/skyselang/yylAdmin/tree/v4/) | thinkphp8 vue3 element-plus   | php8.0 node18 |
| v3   | [v3](https://gitee.com/skyselang/yylAdmin/tree/v3/) | thinkphp6 vue2 element-ui     | php7.3 node12 |

## 演示

- 地址：[demo](https://admin.yyladmin.top)
- 账号：yyladmin、admin、demo、test、php
- 密码：123456
- 提示：演示账号只有部分权限，请安装后体验全部功能
- 前台：[index](https://www.yyladmin.top)

## 安装

### 环境要求

- PHP >= 8.2  
  安装 fileinfo、redis 扩展  
  开启 putenv、proc_open 函数
- Composer >= 2.5
- MySQL >= 5.5
- Redis >= 5.0
- Node >= 20.0
- pnpm >= 10.0

### 安装后端

##### 克隆项目

```bash
git clone https://gitee.com/skyselang/yylAdmin.git
```

##### 进入项目目录

```bash
cd yylAdmin
```

##### 安装依赖

```bash
composer install
```

##### 导入数据库

```bash
数据库文件：private/database/yyladmin.sql
数据库字符集：utf8mb4
数据库排序规则：utf8mb4_general_ci
```

##### 修改配置

```bash
复制.env.example后重命名成.env环境变量文件修改里面配置
```

##### 环境配置（phpStudy）

```bash
根据你的实际环境软件配置
域名：localhost
端口：9526
根目录：yylAdmin/public
PHP版本：8.2.9
```

##### 设置伪静态

###### Nginx

```bash
location / {
    if (!-e $request_filename){
        rewrite  ^(.*)$  /index.php?s=$1  last;  break;
    }
}
```

###### Apache

- httpd.conf 配置文件中加载 mod_rewrite.so 模块
- AllowOverride None 将 None 改为 All

```bash
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On

  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
</IfModule>
```

> 不设置伪静态接口文档无法访问

### 安装前端

#### 克隆项目

```bash
git clone https://gitee.com/skyselang/yylAdminWeb.git
```

#### 进入项目目录

```bash
cd yylAdminWeb
```

#### 安装依赖

```bash
pnpm install
```

> 也可以使用 npm、cnpm、yarn

#### 修改配置

```bash
复制.env.xxx后重命名成.env.xxx.local环境变量文件修改里面配置
```

#### 本地开发启动项目

```bash
pnpm run dev
```

#### 开发完打包正式环境

```bash
pnpm run build:prod
```

#### 开发完打包测试环境

```bash
pnpm run build:stage
```

### 访问后台

地址：[http://localhost:9527](http://localhost:9527)  
账号：yyladmin  
密码：123456  
超管：skyselang  
密码：123456

## 支持

- 如果本项目对您有所帮助，请点个 Star 支持我们

- [Gitee](https://gitee.com/skyselang/yylAdmin)![Gitee](https://gitee.com/skyselang/yylAdmin/badge/star.svg)
- [Github](https://github.com/skyselang/yylAdmin)![Github](https://img.shields.io/github/stars/skyselang/yylAdmin)

## 反馈

- 有任何疑问或者建议，请提 [Issue](https://gitee.com/skyselang/yylAdmin/issues)

## 协议

- Apache-2.0 许可协议，免费开源
- Copyright skyselang https://gitee.com/skyselang

## 提示

- 项目不定时更新，前后端最新代码保持一致
- 拉取代码后请更新前后端依赖（composer install、pnpm install）、同步数据库表结构、清除所有缓存

## 交流

- QQ 交流群：679883097
