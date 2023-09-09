// eslint-disable-next-line no-unused-vars
window.apidocFeConfig = {
  // 标题
  TITLE: 'Apidoc',
  // 缓存配置
  CACHE: {
    // 缓存前缀
    PREFIX: 'APIDOC_',
  },
  HTTP: {
    // 接口请求地址
    HOSTS: [],
    // 请求前缀地址
    // API_PREFIX:"/apidoc",
    // 接口响应超时时间
    TIMEOUT: 30000,
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
    ERROR_CODE_FIELD: 'code',
  },
  // 菜单配置
  MENU: {
    SHOWURL: false,
    WIDTH: 300,
  },
  // 请求类型的颜色
  METHOD_COLOR: {
    GET: '#87d068',
    POST: '#2db7f5',
    PUT: '#ff9800',
    DELETE: '#ff4d4f',
    PATCH: '#802feb',
  },
  // 接口详情页的tab顺序
  API_DETAIL_TABS: ['table', 'json', 'ts', 'debug'],
  // 接口详情表格属性
  API_TABLE_PROPS: {
    // 是否默认展开所有行
    defaultExpandAllRows: true,
  },
  // 加载外部js文件
  LOAD_SCRIPTS: ['./utils/md5.js'],
  // （选配）调试时事件，处理参数值的方法
  DEBUG_EVENTS: {
    md5(param) {
      return new Promise((resolve, reject) => {
        const { config, event } = param
        if (event.key) {
          let value = ''
          let paramKey = 'params'
          if (config.params[event.key]) {
            value = config.params[event.key]
          } else if (config.data[event.key]) {
            value = config.data[event.key]
            paramKey = 'data'
          }
          if (value) {
            const password = md5(value)
            param.config[paramKey][event.key] = password
            param.message = '->' + password
          }
          resolve(param)
        } else {
          reject('未指定字段')
        }
      })
    },
  },
  // （选配）自定义mock规则
  MOCK_EXTENDS: {},
  // （选配）自定义方法
  CUSTOM_METHODS: {},
  // 多语言
  LANG: [
    {
      title: '简体中文',
      lang: 'zh-cn',
      messages: {
        'home.title': '首页',
        'home.appCount': '应用数',
        'home.apiCount': 'API数量',
        'home.docsCount': '文档数量',
        'home.methodCount': '类型统计',
        'common.ok': '确认',
        'common.cancel': '取消',
        'common.clear': '清空',
        'common.desc': '说明',
        'common.action': '操作',
        'common.field': '字段',
        'common.method': '请求类型',
        'common.require': '必填',
        'common.notEmpty': '非空',
        'common.defaultValue': '默认值',
        'common.value': '值',
        'common.docs': '文档',
        'common.close': '关闭',
        'common.view': '查看',
        'common.copy': '复制',
        'common.copySuccess': '复制成功',
        'common.copyUrl': '复制链接',
        'common.actionSuccess': '操作成功',
        'common.page.404': '404-未知页面',
        'common.notdata': '暂无数据',
        'common.group': '分组',
        'common.notGroup': '未分组',
        'common.currentApp': '当前应用',
        'common.please.input': '请输入',
        'common.please.select': '请选择',
        'common.file.name': '文件名',
        'common.appOrVersion': '应用/版本',
        'common.allAppOption': '全部应用',
        'common.ms': '毫秒',
        'common.name': '名称',
        'common.controller': '控制器',
        'common.api': '接口',
        'common.author': '作者',
        'common.tag': '标签',
        'common.delete.confirm.title': '标签',

        'side.search.placeholder': '名称 URL',
        'lang.change.confirm.title': '您确认切换语言为 {langTitle} 吗？',
        'lang.change.confirm.content': '确认后将刷新页面，并回到首页',
        'host.change.confirm.title': '您确认切换Host为 {hostTitle} 吗？',
        'auth.title': '授权访问',
        'auth.password.label': '访问密码',
        'auth.input.placeholder': '请输入访问密码',

        'apiPage.reload.button': '刷新',
        'apiPage.tabs.table': '文档',
        'apiPage.tabs.json': 'Json',
        'apiPage.tabs.ts': 'TypeScript',
        'apiPage.tabs.debug': '调试',
        'apiPage.header.title': '请求头Header',
        'apiPage.query.title': '请求参数Query',
        'apiPage.body.title': '请求参数Body',
        'apiPage.routeParam.title': '路由参数Route',
        'apiPage.title.responses': '响应结果',
        'apiPage.responses': '响应结果',
        'apiPage.responses.success': '成功响应',
        'apiPage.responses.error': '错误响应',
        'apiPage.mdDetail.title': '{name} 字段的说明',
        'apiPage.debug.param.reload': '重置参数',
        'apiPage.debug.header': 'Header',
        'apiPage.debug.query': 'Query',
        'apiPage.debug.body': 'Body',
        'apiPage.debug.routeParam': 'Route',
        'apiPage.debug.excute': '执行 Excute',
        'apiPage.debug.notExcute': '未发起请求',
        'apiPage.debug.reloadParamsAndExcute': '重置所有参数并执行',
        'apiPage.debug.selectFile': 'Select File',
        'apiPage.debug.selectFiles': 'Select Files',

        'apiPage.common.field': '字段名',
        'apiPage.common.value': '字段值',
        'apiPage.common.method': '字段类型',
        'apiPage.common.require': '必填',
        'apiPage.common.desc': '描述',
        'apiPage.common.defaultValue': '默认值',
        'apiPage.common.action': '操作',
        'apiPage.common.type': '类型',
        'apiPage.json.formatError': 'json 参数格式化错误',

        'cache.manage': '缓存管理',
        'cache.cancelAll': '清除所有缓存',
        'cache.cancelSuccess': '清除成功',
        'cache.cancelAllConfirm': '您确认清除所有Api缓存吗？',
        'cache.createAllApi': '生成所有Api缓存',
        'cache.createAllConfirm': '您确认生成所有Api缓存吗？',
        'cache.createSuccess': '生成成功',

        'layout.menu.reload': '更新菜单',
        'layout.menu.openAll': '展开全部',
        'layout.menu.hideAll': '收起全部',
        'layout.tabs.leftSide': '左侧',
        'layout.tabs.rightSide': '右侧',
        'layout.tabs.notTab': '没有标签',
        'layout.tabs.closeCurrent': '关闭当前',
        'layout.tabs.closeLeft': '关闭左侧',
        'layout.tabs.closeRight': '关闭右侧',
        'layout.tabs.closeAll': '关闭全部',

        'globalParam.title': '全局参数',
        'globalParam.header': 'Header',
        'globalParam.header.message': '发送请求时，所有接口将自动携带以下Header参数。',
        'globalParam.query': 'Query',
        'globalParam.query.message': '发送请求时，所有接口将自动携带以下Query参数。',
        'globalParam.body': 'Body',
        'globalParam.body.message': '发送请求时，所有接口将自动携带以下Body参数。',
        'globalParam.cancel.confirm': '确认清空所有参数吗?',
        'globalParam.add': '添加参数',

        'debug.event.before': '请求前事件',
        'debug.event.after': '响应后事件',
        'debug.event.setHeader': '设置请求Header参数',
        'debug.event.setQuery': '设置请求Query参数',
        'debug.event.setBody': '设置请求Body参数',
        'debug.event.clearHeader': '清除请求Header参数',
        'debug.event.clearQuery': '清除请求Query参数',
        'debug.event.clearBody': '清除请求Body参数',
        'debug.event.setGlobalHeader': '设置全局Header参数',
        'debug.event.setGlobalQuery': '设置全局Query参数',
        'debug.event.setGlobalBody': '设置全局Body参数',
        'debug.event.clearGlobalHeader': '清除全局Header参数',
        'debug.event.clearGlobalQuery': '清除全局Query参数',
        'debug.event.clearGlobalBody': '清除全局Body参数',
        'debug.event.ajax': '发送请求',
        'debug.event.custom': '自定义事件',
        'debug.request.header': '请求头',
        'debug.responses.header': '响应头',

        'generator.title': '接口生成',
        'generator.apps.title': '应用/版本',
        'generator.group.title': '分组',
        'generator.table.field': '字段名',
        'generator.table.desc': '注释',
        'generator.table.type': '类型',
        'generator.table.length': '长度',
        'generator.table.default': '默认值',
        'generator.table.notNull': '非Null',
        'generator.table.autoAdd': '自增',
        'generator.table.mainKey': '主键',
        'generator.model.name': '模型名',
        'generator.table.name': '表名',
        'generator.table.comment': '表注释',
        'generator.model.name.placeholder': '请输入模型文件名',
        'generator.table.name.placeholder': '请输入表名',
        'generator.table.row.error': '第{rows}行，字段名、类型必填',
        'generator.submitSuccess': '生成成功',
        'tools.title': '工具',
        'codeTemplate.title': '代码模板',
        'codeTemplate.reload': '重新生成',

        'apiShare.title': '接口分享',
        'apiShare.edit': '编辑接口分享',
        'apiShare.add': '新增接口分享',
        'apiShare.type.all': '全部',
        'apiShare.type.app': '应用',
        'apiShare.type.api': '接口',
        'apiShare.button.create': '创建分享',
        'apiShare.button.edit': '编辑',
        'apiShare.button.delete': '删除',
        'apiShare.type.label': '分享接口',
        'apiShare.time.label': '分享时间',
        'apiShare.all.text': '每次都解析或取缓存中所有接口数据',
      },
    },
  ],
}
