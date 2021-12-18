## BASE_API

### 本地BASE_API

[http://localhost:9526](http://localhost:9526)

### 测试BASE_API

[https://testapi.yyladmin.top](https://testapi.yyladmin.top)

### 正式BASE_API

[https://api.yyladmin.top](https://api.yyladmin.top)

## 请求头部

### admin

请求头部Headers需带上参数：AdminToken

### index

请求头部Headers需带上参数：MemberToken 

## 全局参数

接口调试的时候请在全局参数中设置对应的全局 Headers、Params  

## 响应参数

{
  "code": 200,
  "msg": "操作成功",
  "data": {}
}

调试模式异常返回

{
  "code": 500,
  "message": "服务器错误",
  "data": {}
}

## code说明
|code|说明|
|-|-|
|200| 操作成功|
|400| 操作失败，参数错误|
|401| 登录已失效，请重新登录|
|403| 你没有权限操作|
|404| 接口地址错误|
|429| 你的操作过于频繁|
|500| 服务器错误|