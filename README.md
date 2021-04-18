# yylAdmin 快速、简单、轻量
- <a href="https://gitee.com/skyselang/yylAdmin">Gitee</a> | <a href="https://github.com/skyselang/yylAdmin">Github</a> | <a href="https://gitee.com/skyselang/yylAdmin/wikis">文档</a>

## 简介
yylAdmin是一个基于ThinkPHP6和Vue2极简后台管理系统，只有登录退出、权限管理、日志管理等基础功能；前后台基础框架；你可以在此基础上根据你的业务需求进行开发扩展。简单轻量，开箱即用，前后分离，免费开源。
- <a href="https://github.com/skyselang/yylAdmin">yylAdmin</a> | <a href="https://github.com/skyselang/yylAdminWeb">yylAdminWeb</a>

## 功能
- 登录退出
- 权限管理
- 日志管理
- 接口文档：自动生成接口文档，在线调试
- 表单构建：可视化表单操作，自动生成代码   
更多功能请体验演示

## 演示
- 地址：<a href="https://admin.yyladmin.top" target="_blank">yylAdmin demo</a>  
- 账号：yyladmin、admin、demo、php  
- 密码：123456  
- 提示：演示账号只有部分权限，请安装体验全部功能

## 准备
- <a href="https://www.xp.cn" target="_blank">PhpStudy</a> |
<a href="https://www.phpcomposer.com" target="_blank">Composer</a> |
<a href="https://nodejs.org/zh-cn" target="_blank">Node</a> |
<a href="https://git-scm.com" target="_blank">Git</a> |
<a href="https://www.kancloud.cn/manual/thinkphp6_0/1037479" target="_blank">ThinkPHP</a> |
<a href="https://cn.vuejs.org/v2/guide/syntax.html" target="_blank">Vue</a> |
<a href="https://element.eleme.cn/#/zh-CN/component/installation" target="_blank">Element</a>

## 要求
- PHP >= 7.2.5  
  安装 fileinfo 扩展  
  开启 putenv、proc_open 函数
- MySQL >= 5.5
- Node >= 10.15.0
- npm >= 5.6.0

## 安装
- 前后分离，需分别安装

### 后端
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
重命名.env.example成.env环境变量文件修改里面配置（或修改config文件夹里面的配置）

# 环境配置（phpStudy）
根据你的实际情况配置
域名：localhost
端口：9526
根目录：yylAdmin/public
PHP版本：7.3
伪静态Nginx：
location / {
    if (!-e $request_filename){
        rewrite  ^(.*)$  /index.php?s=$1  last;   break;
    }
}
```
### 前端
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
在.env.xxx环境变量文件里面修改接口地址（后端环境配置域名端口）
VUE_APP_BASE_API = 'http://localhost:9526'

# 本地开发 启动服务
cnpm run dev

# 开发完打包正式环境
cnpm run build:prod

# 开发完打包测试环境
cnpm run build:stage
```
### 访问
- 地址：http://localhost:9527  
- 账号：yyladmin  
- 密码：123456  
- 管理员：skyselang  
- 密码：123456

## 目录
```bash
yylAdmin
├── app                        # 应用（核心目录）
│   │── admin                  # 后台接口
│   │   │── controller         # 控制器（请求参数）
│   │   │── middleware         # 中间件（拦截或过滤请求）
│   │   │── service            # 业务逻辑
│   │   │── validate           # 验证器（验证参数）
│   │── common                 # 公共（缓存、工具等）
│   └── index                  # 前台接口
│   ...
├── config                     # 配置目录（admin、index，其它为tp配置）
├── extend                     # 扩展类库
├── public                     # 对外访问目录
│   │── private                # 数据库文件在里面
│   │── static                 # 静态资源目录
│   │── storage                # 上传目录（读写权限）
│   │── .htaccess              # apache重写文件
│   │── index.php              # 入口文件
│   └── nginx.htaccess         # nginx重写文件
│   ...                 
├── route                      # 路由（没有用到路由）
├── runtime                    # 运行时目录（读写权限）
├── vendor                     # Composer类库目录
├── .env.example               # 环境变量示例文件，重命名.env后使用
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

## 预览
- <img width="100%" src="./public/static/img/yyladmin_login.jpg">
- <img width="100%" src="./public/static/img/yyladmin.jpg">

## 反馈
- 有任何疑问或者建议，请提 <a href="https://github.com/skyselang/yylAdmin/issues">Issue</a> 

## 协议
- Apache-2.0许可协议，免费开源  
- Copyright skyselang https://github.com/skyselang