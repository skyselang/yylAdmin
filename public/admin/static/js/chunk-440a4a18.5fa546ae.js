(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-440a4a18"],{"26d1":function(e,t,n){"use strict";n.d(t,"f",(function(){return a})),n.d(t,"e",(function(){return r})),n.d(t,"a",(function(){return o})),n.d(t,"d",(function(){return l})),n.d(t,"b",(function(){return s})),n.d(t,"c",(function(){return u})),n.d(t,"i",(function(){return c})),n.d(t,"j",(function(){return d})),n.d(t,"g",(function(){return m})),n.d(t,"h",(function(){return f})),n.d(t,"k",(function(){return h})),n.d(t,"l",(function(){return p}));var i=n("b775");function a(e){return Object(i["a"])({url:"/admin/AdminMenu/list",method:"get",params:e})}function r(e){return Object(i["a"])({url:"/admin/AdminMenu/info",method:"get",params:e})}function o(e){return Object(i["a"])({url:"/admin/AdminMenu/add",method:"post",data:e})}function l(e){return Object(i["a"])({url:"/admin/AdminMenu/edit",method:"post",data:e})}function s(e){return Object(i["a"])({url:"/admin/AdminMenu/dele",method:"post",data:e})}function u(e){return Object(i["a"])({url:"/admin/AdminMenu/disable",method:"post",data:e})}function c(e){return Object(i["a"])({url:"/admin/AdminMenu/unauth",method:"post",data:e})}function d(e){return Object(i["a"])({url:"/admin/AdminMenu/unlogin",method:"post",data:e})}function m(e){return Object(i["a"])({url:"/admin/AdminMenu/role",method:"get",params:e})}function f(e){return Object(i["a"])({url:"/admin/AdminMenu/roleRemove",method:"post",data:e})}function h(e){return Object(i["a"])({url:"/admin/AdminMenu/user",method:"get",params:e})}function p(e){return Object(i["a"])({url:"/admin/AdminMenu/userRemove",method:"post",data:e})}},"333d":function(e,t,n){"use strict";var i=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"pagination-container",class:{hidden:e.hidden}},[n("el-pagination",e._b({attrs:{background:e.background,"current-page":e.currentPage,"page-size":e.pageSize,layout:e.layout,"page-sizes":e.pageSizes,total:e.total},on:{"update:currentPage":function(t){e.currentPage=t},"update:current-page":function(t){e.currentPage=t},"update:pageSize":function(t){e.pageSize=t},"update:page-size":function(t){e.pageSize=t},"size-change":e.handleSizeChange,"current-change":e.handleCurrentChange}},"el-pagination",e.$attrs,!1))],1)},a=[];n("65ba");Math.easeInOutQuad=function(e,t,n,i){return e/=i/2,e<1?n/2*e*e+t:(e--,-n/2*(e*(e-2)-1)+t)};var r=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(e){window.setTimeout(e,1e3/60)}}();function o(e){document.documentElement.scrollTop=e,document.body.parentNode.scrollTop=e,document.body.scrollTop=e}function l(){return document.documentElement.scrollTop||document.body.parentNode.scrollTop||document.body.scrollTop}function s(e,t,n){var i=l(),a=e-i,s=20,u=0;t="undefined"===typeof t?500:t;var c=function e(){u+=s;var l=Math.easeInOutQuad(u,i,a,t);o(l),u<t?r(e):n&&"function"===typeof n&&n()};c()}var u={name:"Pagination",props:{total:{required:!0,type:Number},page:{type:Number,default:1},limit:{type:Number,default:20},pageSizes:{type:Array,default:function(){return[10,12,20,30,50,80,100,150,200,300,500,800,1e3]}},layout:{type:String,default:"total, sizes, prev, pager, next, jumper"},background:{type:Boolean,default:!0},autoScroll:{type:Boolean,default:!0},hidden:{type:Boolean,default:!1}},computed:{currentPage:{get:function(){return this.page},set:function(e){this.$emit("update:page",e)}},pageSize:{get:function(){return this.limit},set:function(e){this.$emit("update:limit",e)}}},methods:{handleSizeChange:function(e){this.$emit("pagination",{page:this.currentPage,limit:e}),this.autoScroll&&s(0,800)},handleCurrentChange:function(e){this.$emit("pagination",{page:e,limit:this.pageSize}),this.autoScroll&&s(0,800)}}},c=u,d=(n("e154"),n("4ac2")),m=Object(d["a"])(c,i,a,!1,null,"3f5d1c05",null);t["a"]=m.exports},7107:function(e,t,n){"use strict";n("c71d")},"98b1":function(e,t,n){"use strict";function i(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:230,t=880,n=document.documentElement.clientHeight||document.body.clientHeight;return n?n-e:t-e}n.d(t,"a",(function(){return i}))},a7ad:function(e,t,n){"use strict";n.r(t);var i=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"app-container"},[n("div",{staticClass:"filter-container"},[n("el-row",{attrs:{gutter:0}},[n("el-col",{attrs:{xs:24,sm:22}},[n("el-input",{staticClass:"filter-item",staticStyle:{width:"200px"},attrs:{placeholder:"名称",clearable:""},model:{value:e.query.role_name,callback:function(t){e.$set(e.query,"role_name",t)},expression:"query.role_name"}}),n("el-input",{staticClass:"filter-item",staticStyle:{width:"200px"},attrs:{placeholder:"描述",clearable:""},model:{value:e.query.role_desc,callback:function(t){e.$set(e.query,"role_desc",t)},expression:"query.role_desc"}}),n("el-button",{staticClass:"filter-item",attrs:{type:"primary"},on:{click:function(t){return e.search()}}},[e._v("查询")]),n("el-button",{staticClass:"filter-item",on:{click:function(t){return e.refresh()}}},[e._v("刷新")])],1),n("el-col",{staticStyle:{"text-align":"right"},attrs:{xs:24,sm:2}},[n("el-button",{staticClass:"filter-item",attrs:{type:"primary"},on:{click:function(t){return e.add()}}},[e._v("添加")])],1)],1)],1),n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.loading,expression:"loading"}],staticStyle:{width:"100%"},attrs:{data:e.datas,height:e.height,border:""},on:{"sort-change":e.sort}},[n("el-table-column",{attrs:{prop:"admin_role_id",label:"角色ID","min-width":"100",sortable:"custom",fixed:"left"}}),n("el-table-column",{attrs:{prop:"role_name",label:"名称","min-width":"120","show-overflow-tooltip":""}}),n("el-table-column",{attrs:{prop:"role_desc",label:"描述","min-width":"130","show-overflow-tooltip":""}}),n("el-table-column",{attrs:{prop:"role_sort",label:"排序","min-width":"100",sortable:"custom"}}),n("el-table-column",{attrs:{prop:"create_time",label:"添加时间","min-width":"160",sortable:"custom"}}),n("el-table-column",{attrs:{prop:"update_time",label:"修改时间","min-width":"160",sortable:"custom"}}),n("el-table-column",{attrs:{prop:"is_disable",label:"是否禁用","min-width":"110",align:"center",sortable:"custom"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("el-switch",{attrs:{"active-value":1,"inactive-value":0},on:{change:function(n){return e.isDisable(t.row)}},model:{value:t.row.is_disable,callback:function(n){e.$set(t.row,"is_disable",n)},expression:"scope.row.is_disable"}})]}}])}),n("el-table-column",{attrs:{label:"操作","min-width":"210",align:"right",fixed:"right"},scopedSlots:e._u([{key:"default",fn:function(t){var i=t.row;return[n("el-button",{attrs:{size:"mini",type:"primary"},on:{click:function(t){return e.userShow(i)}}},[e._v("用户")]),n("el-button",{attrs:{size:"mini",type:"success"},on:{click:function(t){return e.edit(i)}}},[e._v("修改")]),n("el-button",{attrs:{size:"mini",type:"danger"},on:{click:function(t){return e.dele(i)}}},[e._v("删除")])]}}])})],1),n("pagination",{directives:[{name:"show",rawName:"v-show",value:e.count>0,expression:"count > 0"}],attrs:{total:e.count,page:e.query.page,limit:e.query.limit},on:{"update:page":function(t){return e.$set(e.query,"page",t)},"update:limit":function(t){return e.$set(e.query,"limit",t)},pagination:e.list}}),n("el-dialog",{attrs:{title:e.dialogTitle,visible:e.dialog,top:"1vh",width:"50%","before-close":e.cancel},on:{"update:visible":function(t){e.dialog=t}}},[n("el-form",{ref:"ref",staticClass:"dialog-body",style:{height:e.height+"px"},attrs:{rules:e.rules,model:e.model,"label-width":"100px"}},[n("el-form-item",{attrs:{label:"名称",prop:"role_name"}},[n("el-input",{attrs:{placeholder:"请输入角色名称",clearable:""},model:{value:e.model.role_name,callback:function(t){e.$set(e.model,"role_name",t)},expression:"model.role_name"}})],1),n("el-form-item",{attrs:{label:"描述",prop:"role_desc"}},[n("el-input",{attrs:{clearable:""},model:{value:e.model.role_desc,callback:function(t){e.$set(e.model,"role_desc",t)},expression:"model.role_desc"}})],1),n("el-form-item",{attrs:{label:"排序",prop:"role_sort"}},[n("el-input",{attrs:{type:"number"},model:{value:e.model.role_sort,callback:function(t){e.$set(e.model,"role_sort",t)},expression:"model.role_sort"}})],1),n("el-form-item",{attrs:{label:"菜单"}},[n("el-tree",{ref:"menuRef",attrs:{data:e.menuTree,"default-checked-keys":e.model.admin_menu_ids,props:{children:"children",label:"menu_name"},"expand-on-click-node":!1,"node-key":"admin_menu_id","default-expand-all":"","show-checkbox":"","check-strictly":"","highlight-current":""},on:{check:e.menuCheck},scopedSlots:e._u([{key:"default",fn:function(t){var i=t.node,a=t.data;return n("span",{staticClass:"custom-tree-node"},[n("span",[e._v(e._s(i.label))]),n("span",[a.children[0]?n("el-button",{attrs:{type:"text",size:"mini"},on:{click:function(){return e.menuChildrenAllCheck(a)}}},[e._v("全选")]):e._e(),a.children[0]?n("el-button",{attrs:{type:"text",size:"mini"},on:{click:function(){return e.menuChildrenAllCheck(a,!0)}}},[e._v("反选")]):e._e(),a.menu_url?n("i",{staticClass:"el-icon-link",staticStyle:{"margin-left":"10px"},attrs:{title:a.menu_url}}):n("i",{staticClass:"el-icon-link",staticStyle:{"margin-left":"10px",color:"#fff"}}),a.is_unauth?n("i",{staticClass:"el-icon-unlock",staticStyle:{"margin-left":"10px"},attrs:{title:"无需权限"}}):n("i",{staticClass:"el-icon-unlock",staticStyle:{"margin-left":"10px",color:"#fff"}}),a.is_unlogin?n("i",{staticClass:"el-icon-user",staticStyle:{"margin-left":"10px"},attrs:{title:"无需登录"}}):n("i",{staticClass:"el-icon-user",staticStyle:{"margin-left":"10px",color:"#fff"}})],1)])}}])})],1)],1),n("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[n("el-button",{on:{click:e.cancel}},[e._v("取消")]),n("el-button",{attrs:{type:"primary"},on:{click:e.submit}},[e._v("提交")])],1)],1),n("el-dialog",{attrs:{title:e.userDialogTitle,visible:e.userDialog,width:"65%",top:"1vh"},on:{"update:visible":function(t){e.userDialog=t}}},[n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.userLoad,expression:"userLoad"}],ref:"userRef",staticStyle:{width:"100%"},attrs:{data:e.userData,height:e.height+30,border:""},on:{"sort-change":e.userSort}},[n("el-table-column",{attrs:{prop:"admin_user_id",label:"用户ID","min-width":"100",sortable:"custom",fixed:"left"}}),n("el-table-column",{attrs:{prop:"username",label:"账号","min-width":"120",sortable:"custom"}}),n("el-table-column",{attrs:{prop:"nickname",label:"昵称","min-width":"120"}}),n("el-table-column",{attrs:{prop:"remark",label:"备注",width:"100"}}),n("el-table-column",{attrs:{prop:"is_admin",label:"超管","min-width":"80",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("el-switch",{attrs:{"active-value":1,"inactive-value":0,disabled:""},model:{value:t.row.is_admin,callback:function(n){e.$set(t.row,"is_admin",n)},expression:"scope.row.is_admin"}})]}}])}),n("el-table-column",{attrs:{prop:"is_disable",label:"禁用","min-width":"80",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("el-switch",{attrs:{"active-value":1,"inactive-value":0,disabled:""},model:{value:t.row.is_disable,callback:function(n){e.$set(t.row,"is_disable",n)},expression:"scope.row.is_disable"}})]}}])}),n("el-table-column",{attrs:{label:"操作","min-width":"80",align:"right",fixed:"right"},scopedSlots:e._u([{key:"default",fn:function(t){var i=t.row;return[n("el-button",{attrs:{size:"mini",type:"danger"},on:{click:function(t){return e.userRemove(i)}}},[e._v("解除")])]}}])})],1),n("pagination",{directives:[{name:"show",rawName:"v-show",value:e.userCount>0,expression:"userCount > 0"}],attrs:{total:e.userCount,page:e.userQuery.page,limit:e.userQuery.limit},on:{"update:page":function(t){return e.$set(e.userQuery,"page",t)},"update:limit":function(t){return e.$set(e.userQuery,"limit",t)},pagination:e.user}})],1)],1)},a=[],r=(n("b4e6"),n("51d7"),n("98b1")),o=n("333d"),l=n("26d1"),s=n("b775");function u(e){return Object(s["a"])({url:"/admin/AdminRole/list",method:"get",params:e})}function c(e){return Object(s["a"])({url:"/admin/AdminRole/info",method:"get",params:e})}function d(e){return Object(s["a"])({url:"/admin/AdminRole/add",method:"post",data:e})}function m(e){return Object(s["a"])({url:"/admin/AdminRole/edit",method:"post",data:e})}function f(e){return Object(s["a"])({url:"/admin/AdminRole/dele",method:"post",data:e})}function h(e){return Object(s["a"])({url:"/admin/AdminRole/disable",method:"post",data:e})}function p(e){return Object(s["a"])({url:"/admin/AdminRole/user",method:"get",params:e})}function g(e){return Object(s["a"])({url:"/admin/AdminRole/userRemove",method:"post",data:e})}var b={name:"Role",components:{Pagination:o["a"]},data:function(){return{height:680,loading:!1,datas:[],count:0,query:{page:1,limit:12},dialog:!1,dialogTitle:"",model:{admin_role_id:"",admin_menu_ids:[],role_name:"",role_desc:"",role_sort:200},rules:{role_name:[{required:!0,message:"请输入角色名称",trigger:"blur"}]},menuTree:[],userDialog:!1,userDialogTitle:"",userLoad:!1,userData:[],userCount:0,userQuery:{}}},created:function(){this.height=Object(r["a"])(),this.list()},methods:{list:function(){var e=this;this.loading=!0,u(this.query).then((function(t){e.datas=t.data.list,e.count=t.data.count,e.loading=!1})).catch((function(){e.loading=!1}))},add:function(){var e=this;this.dialog=!0,this.dialogTitle="角色添加",Object(l["f"])().then((function(t){e.menuTree=t.data.list})),this.reset()},edit:function(e){var t=this;this.dialog=!0,this.dialogTitle="角色修改："+e.admin_role_id,Object(l["f"])().then((function(e){t.menuTree=e.data.list})),c({admin_role_id:e.admin_role_id}).then((function(e){t.reset(e.data)}))},dele:function(e){var t=this;this.$confirm('确定要删除角色 <span style="color:red">'+e.role_name+" </span>吗？","删除角色："+e.admin_role_id,{type:"warning",dangerouslyUseHTMLString:!0}).then((function(){t.loading=!0,f({admin_role_id:e.admin_role_id}).then((function(e){t.reset(),t.list(),t.$message.success(e.msg)})).catch((function(){t.loading=!1}))})).catch((function(){}))},cancel:function(){this.dialog=!1,this.reset()},submit:function(){var e=this;this.$refs["ref"].validate((function(t){t&&(e.loading=!0,e.model.admin_role_id?m(e.model).then((function(t){e.list(),e.reset(),e.dialog=!1,e.$message.success(t.msg)})).catch((function(){e.loading=!1})):d(e.model).then((function(t){e.list(),e.reset(),e.dialog=!1,e.$message.success(t.msg)})).catch((function(){e.loading=!1})))}))},reset:function(e){this.model=e||this.$options.data().model,void 0!==this.$refs["ref"]&&this.$refs["ref"].resetFields()},search:function(){this.query.page=1,this.list()},refresh:function(){this.query=this.$options.data().query,this.list()},sort:function(e){this.query.sort_field=e.prop,this.query.sort_type="","ascending"===e.order&&(this.query.sort_type="asc",this.list()),"descending"===e.order&&(this.query.sort_type="desc",this.list())},isDisable:function(e){var t=this;this.loading=!0,h({admin_role_id:e.admin_role_id,is_disable:e.is_disable}).then((function(e){t.list(),t.$message.success(e.msg)})).catch((function(){t.list(),t.loading=!1}))},menuCheck:function(e,t){this.model.admin_menu_ids=t.checkedKeys},menuChildrenAllCheck:function(e){var t=arguments.length>1&&void 0!==arguments[1]&&arguments[1],n=this.model.admin_menu_ids,i=[];i.push(e.admin_menu_id);for(var a=0;a<e.children.length;a++)i.push(e.children[a].admin_menu_id);if(t){for(var r=0;r<n.length;r++)for(var o=0;o<i.length;o++)n[r]===i[o]&&n.splice(r,1);this.model.admin_menu_ids=n,this.$refs.menuRef.setCheckedKeys(n)}else{for(var l=[],s=n.concat(i),u=0;u<s.length;u++)-1===l.indexOf(s[u])&&l.push(s[u]);this.model.admin_menu_ids=l,this.$refs.menuRef.setCheckedKeys(l)}},userShow:function(e){this.userDialog=!0,this.userDialogTitle="角色用户："+e.role_name,this.userQuery.admin_role_id=e.admin_role_id,this.user()},user:function(){var e=this;this.userLoad=!0,p(this.userQuery).then((function(t){e.userData=t.data.list,e.userCount=t.data.count,e.userLoad=!1,e.$nextTick((function(){e.$refs["userRef"].doLayout()}))})).catch((function(){e.userLoad=!1}))},userSort:function(e){this.userQuery.sort_field=e.prop,this.userQuery.sort_type="","ascending"===e.order&&(this.userQuery.sort_type="asc",this.user()),"descending"===e.order&&(this.userQuery.sort_type="desc",this.user())},userRemove:function(e){var t=this;this.$confirm('确定要解除该角色与用户 <span style="color:red">'+e.username+" </span>的关联吗？","解除用户："+e.admin_user_id,{type:"warning",dangerouslyUseHTMLString:!0}).then((function(){t.userLoad=!0,g({admin_role_id:t.userQuery.admin_role_id,admin_user_id:e.admin_user_id}).then((function(e){t.user(),t.$message.success(e.msg)})).catch((function(){t.userLoad=!1}))})).catch((function(){}))}}},_=b,y=(n("7107"),n("4ac2")),v=Object(y["a"])(_,i,a,!1,null,"3c3f6e41",null);t["default"]=v.exports},c71d:function(e,t,n){},d803:function(e,t,n){},e154:function(e,t,n){"use strict";n("d803")}}]);