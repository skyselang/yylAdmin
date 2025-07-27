// eslint-disable-next-line no-unused-vars
window.apidocFeConfig = {
  // 标题
  TITLE: "Apidoc",
  // 缓存配置
  CACHE: {
    // 缓存前缀
    PREFIX: "APIDOC_",
  },
  HTTP: {
    // 接口请求地址
    HOSTS: [],
    // 请求前缀地址
    // API_PREFIX:"/apidoc",
    // 接口响应超时时间
    TIMEOUT: 60000,
    // 跨域请求时是否使用凭证
    WITHCREDENTIALS: false,
    // 启用转码
    ENCODEURICOMPONENT: false,
  },
  // 授权访问
  AUTH: {
    // 异常状态码
    ERROR_STATUS: 401,
    // 异常code字段
    ERROR_CODE_FIELD: "code",
  },
  // 菜单配置
  MENU: {
    SHOWURL: false,
    WIDTH: 300,
  },
  // 请求类型的颜色
  METHOD_COLOR: {
    GET: "#87d068",
    POST: "#2db7f5",
    PUT: "#ff9800",
    DELETE: "#ff4d4f",
    PATCH: "#802feb",
  },
  // 接口详情页的tab顺序
  API_DETAIL_TABS: ["table", "json", "ts", "debug"],
  // 接口详情表格属性
  API_TABLE_PROPS: {
    // 是否默认展开所有行
    defaultExpandAllRows: true,
  },
  // 加载外部js文件
  LOAD_SCRIPTS: ["./utils/md5.js"],
  // （选配）调试时事件，处理参数值的方法
  DEBUG_EVENTS: {
    md5(param) {
      return new Promise((resolve, reject) => {
        const { config, event } = param;
        if (event.key) {
          let value = "";
          let paramKey = "params";
          if (config.params[event.key]) {
            value = config.params[event.key];
          } else if (config.data[event.key]) {
            value = config.data[event.key];
            paramKey = "data";
          }
          if (value) {
            const password = md5(value);
            param.config[paramKey][event.key] = password;
            param.message = "->" + password;
          }
          resolve(param);
        } else {
          reject("未指定字段");
        }
      });
    },
    // 自定义设置token到全局请求参数
    setToken(param) {
      return new Promise((resolve, reject) => {
        const token = `Bearer ${param.value}`;
        this.setGlobalHeader({
          ...param,
          value: token,
        })
          .then(() => resolve(param))
          .catch((err) => reject(err));
      });
    },
  },
  // （选配）自定义mock规则
  MOCK_EXTENDS: {},
  // （选配）自定义方法
  CUSTOM_METHODS: {},
  // 多语言
  LANG: [
    {
      title: "简体中文",
      lang: "zh-cn",
      messages: {
        "home.title": "首页",
        "home.appCount": "应用数",
        "home.apiCount": "API数量",
        "home.docsCount": "文档数量",
        "home.methodCount": "类型统计",
        "common.ok": "确认",
        "common.cancel": "取消",
        "common.clear": "清空",
        "common.desc": "说明",
        "common.action": "操作",
        "common.field": "字段",
        "common.method": "请求类型",
        "common.require": "必填",
        "common.notEmpty": "非空",
        "common.defaultValue": "默认值",
        "common.value": "值",
        "common.docs": "文档",
        "common.close": "关闭",
        "common.view": "查看",
        "common.copy": "复制",
        "common.copySuccess": "复制成功",
        "common.copyUrl": "复制链接",
        "common.exportSwaggerJson": "导出Swagger.json",
        "common.exportSuccess": "导出成功",
        "common.exportError": "导出失败：{message}",
        "common.actionSuccess": "操作成功",
        "common.page.404": "404-未知页面",
        "common.notdata": "暂无数据",
        "common.group": "分组",
        "common.notGroup": "未分组",
        "common.currentApp": "当前应用",
        "common.please.input": "请输入",
        "common.please.select": "请选择",
        "common.file.name": "文件名",
        "common.appOrVersion": "应用/版本",
        "common.allAppOption": "全部应用",
        "common.ms": "毫秒",
        "common.name": "名称",
        "common.controller": "控制器",
        "common.api": "接口",
        "common.author": "作者",
        "common.tag": "标签",
        "common.delete.confirm.title": "标签",

        "side.search.placeholder": "名称 URL",
        "lang.change.confirm.title": "您确认切换语言为 {langTitle} 吗？",
        "lang.change.confirm.content": "确认后将刷新页面，并回到首页",
        "host.change.confirm.title": "您确认切换Host为 {hostTitle} 吗？",
        "auth.title": "授权访问",
        "auth.password.label": "访问密码",
        "auth.input.placeholder": "请输入访问密码",

        "apiPage.reload.button": "刷新",
        "apiPage.tabs.table": "文档",
        "apiPage.tabs.json": "Json",
        "apiPage.tabs.ts": "TypeScript",
        "apiPage.tabs.debug": "调试",
        "apiPage.header.title": "请求头Header",
        "apiPage.query.title": "请求参数Query",
        "apiPage.body.title": "请求参数Body",
        "apiPage.routeParam.title": "路由参数Route",
        "apiPage.title.responses": "响应结果",
        "apiPage.responses": "响应结果",
        "apiPage.responses.success": "成功响应体",
        "apiPage.responses.error": "错误响应体",
        "apiPage.responsesStatus": "响应状态码",
        "apiPage.responsesStatus.name": "状态码",
        "apiPage.responsesStatus.contentType": "Content-Type",

        "apiPage.mdDetail.title": "{name} 字段的说明",
        "apiPage.debug.param.reload": "重置参数",
        "apiPage.debug.header": "Header",
        "apiPage.debug.query": "Query",
        "apiPage.debug.body": "Body",
        "apiPage.debug.routeParam": "Route",
        "apiPage.debug.excute": "执行 Excute",
        "apiPage.debug.notExcute": "未发起请求",
        "apiPage.debug.reloadParamsAndExcute": "重置所有参数并执行",
        "apiPage.debug.selectFile": "选择文件",
        "apiPage.debug.selectFiles": "选择文件",
        "apiPage.debug.useGlobalParams": "使用全局参数",

        "apiPage.common.field": "字段名",
        "apiPage.common.value": "字段值",
        "apiPage.common.method": "字段类型",
        "apiPage.common.require": "必填",
        "apiPage.common.desc": "描述",
        "apiPage.common.defaultValue": "默认值",
        "apiPage.common.action": "操作",
        "apiPage.common.type": "类型",
        "apiPage.json.formatError": "json 参数格式化错误",

        "cache.manage": "缓存管理",
        "cache.cancelAll": "清除所有缓存",
        "cache.cancelSuccess": "清除成功",
        "cache.cancelAllConfirm": "您确认清除所有Api缓存吗？",
        "cache.createAllApi": "生成所有Api缓存",
        "cache.createAllConfirm": "您确认生成所有Api缓存吗？",
        "cache.createSuccess": "生成成功",

        "layout.menu.reload": "更新菜单",
        "layout.menu.openAll": "展开全部",
        "layout.menu.hideAll": "收起全部",
        "layout.tabs.leftSide": "左侧",
        "layout.tabs.rightSide": "右侧",
        "layout.tabs.notTab": "没有标签",
        "layout.tabs.closeCurrent": "关闭当前",
        "layout.tabs.closeLeft": "关闭左侧",
        "layout.tabs.closeRight": "关闭右侧",
        "layout.tabs.closeAll": "关闭全部",

        "globalParam.title": "全局参数",
        "globalParam.header": "Header",
        "globalParam.header.message":
          "发送请求时，所有接口将自动携带以下Header参数。",
        "globalParam.query": "Query",
        "globalParam.query.message":
          "发送请求时，所有接口将自动携带以下Query参数。",
        "globalParam.body": "Body",
        "globalParam.body.message":
          "发送请求时，所有接口将自动携带以下Body参数。",
        "globalParam.cancel.confirm": "确认清空所有参数吗?",
        "globalParam.add": "添加参数",

        "debug.event.before": "请求前事件",
        "debug.event.after": "响应后事件",
        "debug.event.setHeader": "设置请求Header参数",
        "debug.event.setQuery": "设置请求Query参数",
        "debug.event.setBody": "设置请求Body参数",
        "debug.event.clearHeader": "清除请求Header参数",
        "debug.event.clearQuery": "清除请求Query参数",
        "debug.event.clearBody": "清除请求Body参数",
        "debug.event.setGlobalHeader": "设置全局Header参数",
        "debug.event.setGlobalQuery": "设置全局Query参数",
        "debug.event.setGlobalBody": "设置全局Body参数",
        "debug.event.clearGlobalHeader": "清除全局Header参数",
        "debug.event.clearGlobalQuery": "清除全局Query参数",
        "debug.event.clearGlobalBody": "清除全局Body参数",
        "debug.event.ajax": "发送请求",
        "debug.event.custom": "自定义事件",
        "debug.request.header": "请求头",
        "debug.responses.header": "响应头",

        "generator.title": "接口生成",
        "generator.apps.title": "应用/版本",
        "generator.group.title": "分组",
        "generator.table.field": "字段名",
        "generator.table.desc": "注释",
        "generator.table.type": "类型",
        "generator.table.length": "长度",
        "generator.table.default": "默认值",
        "generator.table.notNull": "非Null",
        "generator.table.autoAdd": "自增",
        "generator.table.mainKey": "主键",
        "generator.model.name": "模型名",
        "generator.table.name": "表名",
        "generator.table.comment": "表注释",
        "generator.model.name.placeholder": "请输入模型文件名",
        "generator.table.name.placeholder": "请输入表名",
        "generator.table.row.error": "第{rows}行，字段名、类型必填",
        "generator.submitSuccess": "生成成功",
        "tools.title": "工具",
        "codeTemplate.title": "代码模板",
        "codeTemplate.reload": "重新生成",

        "apiShare.title": "接口分享",
        "apiShare.edit": "编辑接口分享",
        "apiShare.add": "新增接口分享",
        "apiShare.type.all": "全部",
        "apiShare.type.app": "应用",
        "apiShare.type.api": "接口",
        "apiShare.button.create": "创建分享",
        "apiShare.button.edit": "编辑",
        "apiShare.button.delete": "删除",
        "apiShare.type.label": "分享接口",
        "apiShare.time.label": "分享时间",
        "apiShare.all.text": "每次都解析或取缓存中所有接口数据",
      },
    },
    {
      title: "English",
      lang: "en",
      messages: {
        "home.title": "Home",
        "home.appCount": "App count",
        "home.apiCount": "Api count",
        "home.docsCount": "Docs count",
        "home.methodCount": "Method count",
        "common.ok": "OK",
        "common.cancel": "Cancel",
        "common.clear": "Clear",
        "common.desc": "Desc",
        "common.action": "Action",
        "common.field": "Field",
        "common.method": "Method",
        "common.require": "Require",
        "common.notEmpty": "Not empty",
        "common.defaultValue": "Default value",
        "common.value": "Value",
        "common.docs": "Document",
        "common.close": "Close",
        "common.view": "View",
        "common.copy": "Copy",
        "common.copySuccess": "Copy success",
        "common.copyUrl": "Copy url",
        "common.exportSwaggerJson": "Export SwaggerJson",
        "common.exportSuccess": "Export success",
        "common.exportError": "Export error：{message}",
        "common.actionSuccess": "Action success",
        "common.page.404": "404-page",
        "common.notdata": "Not data",
        "common.group": "Group",
        "common.notGroup": "Not group",
        "common.currentApp": "Current app",
        "common.please.input": "Please input",
        "common.please.select": "Please select",
        "common.file.name": "File name",
        "common.appOrVersion": "App or version",
        "common.allAppOption": "All app",
        "common.ms": "Millisecond",
        "common.name": "Name",
        "common.controller": "Controller",
        "common.api": "Api",
        "common.author": "Author",
        "common.tag": "Label",
        "common.delete.confirm.title": "Label",

        "side.search.placeholder": "Name URL",
        "lang.change.confirm.title":
          "Are you sure you want to switch the language to {langTitle}?",
        "lang.change.confirm.content":
          "After confirmation, the page will refresh and return to the homepage",
        "host.change.confirm.title":
          "Are you sure you want to switch the Host to {hostTitle}?",
        "auth.title": "Authorized Access",
        "auth.password.label": "Access password",
        "auth.input.placeholder": "Please enter the access password",

        "apiPage.reload.button": "Refresh",
        "apiPage.tabs.table": "Document",
        "apiPage.tabs.json": "Json",
        "apiPage.tabs.ts": "TypeScript",
        "apiPage.tabs.debug": "Dbugging",
        "apiPage.header.title": "Request Header",
        "apiPage.query.title": "Request parameter Query",
        "apiPage.body.title": "Request parameter Body",
        "apiPage.routeParam.title": "Route parameter",
        "apiPage.title.responses": "Response results",
        "apiPage.responses": "Response results",
        "apiPage.responses.success": "Successful response body",
        "apiPage.responses.error": "Error response body",
        "apiPage.responsesStatus": "Response status code",
        "apiPage.responsesStatus.name": "Status",
        "apiPage.responsesStatus.contentType": "Content-Type",

        "apiPage.mdDetail.title": "Description of the {name} field",
        "apiPage.debug.param.reload": "Reset parameters",
        "apiPage.debug.header": "Header",
        "apiPage.debug.query": "Query",
        "apiPage.debug.body": "Body",
        "apiPage.debug.routeParam": "Route",
        "apiPage.debug.excute": "Execute Excute",
        "apiPage.debug.notExcute": "No request initiated",
        "apiPage.debug.reloadParamsAndExcute":
          "Reset all parameters and execute",
        "apiPage.debug.selectFile": "Select File",
        "apiPage.debug.selectFiles": "Select Files",
        "apiPage.debug.useGlobalParams": "Use global parameters",

        "apiPage.common.field": "Field name",
        "apiPage.common.value": "Field value",
        "apiPage.common.method": "Field type",
        "apiPage.common.require": "Require",
        "apiPage.common.desc": "Describe",
        "apiPage.common.defaultValue": "Default value",
        "apiPage.common.action": "Operate",
        "apiPage.common.type": "Type",
        "apiPage.json.formatError": "JSON parameter formatting error",

        "cache.manage": "Cache Management",
        "cache.cancelAll": "Clear All Caches",
        "cache.cancelSuccess": "Clear successfully",
        "cache.cancelAllConfirm": "Are you sure to clear all API caches?",
        "cache.createAllApi": "Generate all API caches",
        "cache.createAllConfirm": "Are you sure to generate all API caches?",
        "cache.createSuccess": "Successfully generated",

        "layout.menu.reload": "Update menu",
        "layout.menu.openAll": "Expand all",
        "layout.menu.hideAll": "Put away everything",
        "layout.tabs.leftSide": "Left side",
        "layout.tabs.rightSide": "Right side",
        "layout.tabs.notTab": "No label",
        "layout.tabs.closeCurrent": "Close current",
        "layout.tabs.closeLeft": "Close the left side",
        "layout.tabs.closeRight": "Close the right side",
        "layout.tabs.closeAll": "Close all",

        "globalParam.title": "Global parameters",
        "globalParam.header": "Header",
        "globalParam.header.message":
          "When sending a request, all interfaces will automatically carry the following header parameters.",
        "globalParam.query": "Query",
        "globalParam.query.message":
          "When sending a request, all interfaces will automatically carry the following Query parameters.",
        "globalParam.body": "Body",
        "globalParam.body.message":
          "When sending a request, all interfaces will automatically carry the following Body parameters.",
        "globalParam.cancel.confirm": "Are you sure to clear all parameters?",
        "globalParam.add": "Add parameters",

        "debug.event.before": "Pre request event",
        "debug.event.after": "Post response events",
        "debug.event.setHeader": "Set request header parameters",
        "debug.event.setQuery": "Set request query parameters",
        "debug.event.setBody": "Set request Body parameters",
        "debug.event.clearHeader": "Clear request header parameters",
        "debug.event.clearQuery": "Clear Request Query Parameters",
        "debug.event.clearBody": "Clear request Body parameter",
        "debug.event.setGlobalHeader": "Set global header parameters",
        "debug.event.setGlobalQuery": "Set global Query parameters",
        "debug.event.setGlobalBody": "Set global Body parameters",
        "debug.event.clearGlobalHeader": "Clear global header parameters",
        "debug.event.clearGlobalQuery": "Clear global query parameters",
        "debug.event.clearGlobalBody": "Clear global Body parameters",
        "debug.event.ajax": "Send request",
        "debug.event.custom": "Custom Events",
        "debug.request.header": "Request header",
        "debug.responses.header": "Response header",

        "generator.title": "Interface generation",
        "generator.apps.title": "App/Ver",
        "generator.group.title": "Group",
        "generator.table.field": "Field name",
        "generator.table.desc": "Explanatory note",
        "generator.table.type": "Type",
        "generator.table.length": "Length",
        "generator.table.default": "Default value",
        "generator.table.notNull": "Non null",
        "generator.table.autoAdd": "Auto-increment",
        "generator.table.mainKey": "Primary key",
        "generator.model.name": "Model Name",
        "generator.table.name": "Table name",
        "generator.table.comment": "Table Annotations",
        "generator.model.name.placeholder": "Please enter the model file name",
        "generator.table.name.placeholder": "Please enter the table name",
        "generator.table.row.error":
          "Line {rows}, field name and type are required",
        "generator.submitSuccess": "Submit success",
        "tools.title": "Tools",
        "codeTemplate.title": "Code template",
        "codeTemplate.reload": "Regenerate",

        "apiShare.title": "Api Share",
        "apiShare.edit": "Api share edit",
        "apiShare.add": "Api share add",
        "apiShare.type.all": "All",
        "apiShare.type.app": "App",
        "apiShare.type.api": "Api",
        "apiShare.button.create": "Create",
        "apiShare.button.edit": "Edit",
        "apiShare.button.delete": "Delete",
        "apiShare.type.label": "Share api",
        "apiShare.time.label": "Share time",
        "apiShare.all.text":
          "Parse or retrieve all interface data from the cache every time",
      },
    },
  ],
};
