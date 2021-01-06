# yylAdmin

Gitee：<a href="https://gitee.com/skyselang/yylAdmin">https://gitee.com/skyselang/yylAdmin</a>  
Github：<a href="https://github.com/skyselang/yylAdmin">https://github.com/skyselang/yylAdmin</a>

## 简介
免费开源、快速、简单、轻量  
yylAdmin是一个极简后台管理系统，只有登录退出、权限管理、日志管理等基础功能；前后台基础框架，只有后台后端、后台前端、前台后端基础功能，你可以在此基础根据你的业务需求进行开发扩展。前后端分离，后端采用ThinkPHP6，前端采用Vue2。
- <a href="https://github.com/skyselang/yylAdmin" target="_blank">yylAdmin</a>
- <a href="https://github.com/skyselang/yylAdminWeb" target="_blank">yylAdminWeb</a>

## 演示

地址：<a href="https://admin.yyladmin.top" target="_blank">yylAdmin demo</a>  
账号：yyladmin、admin、demo、php  
密码：123456  
提示：演示账号只有部分权限，请安装体验全部功能

## 准备

- <a href="https://www.xp.cn" target="_blank">PhpStudy</a>
- <a href="https://git-scm.com" target="_blank">Git</a>
- <a href="https://nodejs.org/zh-cn" target="_blank">Node</a>
- <a href="https://www.phpcomposer.com" target="_blank">Composer</a>
- <a href="https://www.kancloud.cn/manual/thinkphp6_0/1037479" target="_blank">ThinkPHP</a>
- <a href="https://cn.vuejs.org/v2/guide/syntax.html" target="_blank">Vue</a>
- <a href="https://element.eleme.cn/#/zh-CN/component/installation" target="_blank">Element</a>

## 要求

- PHP >= 7.1
- MySQL >= 5.6
- Redis
- node >= 10.15.0
- npm >= 5.6.0

## 安装

### PHP部分
```bash
# 克隆项目
git clone https://github.com/skyselang/yylAdmin.git

# 进入项目目录
cd yylAdmin

# 设置composer
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# 安装依赖
composer install

# 导入数据库
数据库文件：public/private/yyladmin.sql

# 修改配置
修改.env环境变量文件里面配置（或者直接修改config文件夹里面的相应配置）
```

### WEB部分
```bash
# 克隆项目
git clone https://github.com/skyselang/yylAdminWeb.git

# 进入项目目录
cd yylAdminWeb

# 使用cnpm
npm install -g cnpm --registry=https://registry.npm.taobao.org

# 安装依赖
cnpm install

# 修改配置
在.env.xxx环境变量文件里面修改接口地址

# 启动服务
cnpm run dev
```

### 访问
地址：http://localhost:9527  
账号：yyladmin  
密码：123456  
管理员：skyselang  
密码：123456

## 目录

```bash
yylAdmin
├── app                        # 应用（核心目录）
│   │── admin                  # 后台接口
│   │── common                 # 公共（缓存、工具等）
│   └── index                  # 前台接口
│   ...
├── config                     # 配置目录（admin、index，其它为tp配置）
├── extend                     # 扩展类库
├── public                     # 静态资源（上传的目录需要读写权限）
├── route                      # 路由（没有用到路由）
├── runtime                    # 运行时目录（读写权限）
├── vendor                     # Composer类库
├── .env                       # 环境变量文件
...
# 更多请参考thinkphp6目录结构

yylAdminWeb
├── build                      # 构建相关
├── public                     # 静态资源
│   │── favicon.ico            # favicon图标
│   └── index.html             # html模板
├── src                        # 源代码
│   ├── api                    # 所有请求接口
│   ├── assets                 # 主题字体等静态资源
│   ├── components             # 全局公用组件
│   ├── directive              # 全局指令
│   ├── filters                # 全局filter
│   ├── layout                 # 全局layout
│   ├── router                 # 路由
│   ├── store                  # 全局store管理
│   ├── styles                 # 全局样式
│   ├── utils                  # 全局公用方法
│   ├── views                  # 所有页面
│   ├── App.vue                # 入口页面
│   ├── main.js                # 入口文件加载组件初始化等
│   ├── permission.js          # 权限管理
│   └── setting.js             # 基础设置
├── .env.xxx                   # 环境变量配置
├── .eslintrc.js               # eslint配置项
├── package.json               # package.json
├── postcss.config.js          # postcss配置
└── vue.config.js              # vue-cli配置
...
```

## 开发
> 以日志管理为例
### PHP部分（后台接口）
- 编写接口代码：app/admin/controller/AdminLog.php
<img width="100%" src="./public/static/img/devphp1.jpg">
- 添加菜单信息
<img width="100%" src="./public/static/img/devphp2.jpg">
- 分配相应权限
<img width="100%" src="./public/static/img/devphp3.jpg">

### WEB部分（后台页面）
- 新建接口文件：src/api/admin.js
<img width="100%" src="./public/static/img/devweb1.jpg">
- 新建页面文件：src/views/admin/log.vue
<img width="100%" src="./public/static/img/devweb2.jpg">
- 添加路由信息：src/router/index.js
<img width="100%" src="./public/static/img/devweb3.jpg">
- 重新登录刷新权限
<img width="100%" src="./public/static/img/devref.jpg">

### PHP部分（前台接口）
- 前台接口写在app/index/controller


## 发布

```bash
# 构建测试环境
cnpm run build:stage

# 构建生产环境
cnpm run build:prod

# 代码格式检查
cnpm run lint

# 代码格式检查并自动修复
cnpm run lint -- --fix
```

## 预览

<img width="100%" src="./public/static/img/yyladmin_login.jpg">

<img width="100%" src="./public/static/img/yyladmin.jpg">

## FQA

### npm
- 推荐使用cnpm：<a href="https://developer.aliyun.com/mirror/NPM" target="_blank">cnpm</a>

### ui
- 使用的是element-ui：<a href="https://element.eleme.cn/#/zh-CN/component/installation" target="_blank">element-ui</a>

### browser
- 支持Chrome、Firefox、QQ、360、Edge等主流浏览器，不支持IE以及浏览器的兼容模式（IE内核）

### debug
- 调试模式下根据接口返回错误信息排查，或者提<a href="https://github.com/skyselang/yylAdmin/issues" target="_blank">Issue</a> 

## 协议

- Apache2协议，免费开源
- Copyright skyselang https://github.com/skyselang