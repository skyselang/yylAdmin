import{ab as e,c as a,l,w as t,a3 as o,a2 as s,e as d,Q as m,af as p,as as u}from"./index.APvhMgDH.js";import{E as i}from"./el-row.CPP9k-Sk.js";import{a as r,E as c}from"./el-form-item.CRP52N48.js";import{a as n,E as h}from"./el-select.DcB9YoAC.js";import"./el-scrollbar.BRsHK0Jh.js";import"./el-popper.BGF08XMW.js";import{E as f}from"./el-col.CfjMTi8p.js";import{E as _}from"./el-switch.Cb3IQwTC.js";import{s as b}from"./screen-height.C1I3e_dh.js";import{e as v,f as g}from"./setting.CUzOJh5f.js";import{_ as y}from"./_plugin-vue_export-helper.BCo6x5W8.js";import"./castArray.BGOR1owe.js";import"./_Uint8Array.Dyjbn1zu.js";import"./debounce.DkHeVxem.js";import"./index.DWtrYHhd.js";import"./isUndefined.DgmxjSXK.js";import"./tagsView.CbCSAniL.js";const j=y({name:"SystemSettingCaptcha",data:()=>({name:"验证码设置",height:680,loading:!1,model:{captcha_switch:0,captcha_mode:1,captcha_type:1},rules:{},modes:[{value:1,label:"字符"},{value:2,label:"行为"}],typestr:[{value:1,label:"数字"},{value:2,label:"字母"},{value:3,label:"数字字母"},{value:4,label:"算术"},{value:5,label:"中文"}],typeaj:[{value:1,label:"滑动拼图"},{value:2,label:"点选文字"}]}),created(){this.height=b(210),this.info()},methods:{info(){v().then((e=>{this.model=e.data}))},refresh(){this.loading=!0,v().then((a=>{this.model=a.data,this.loading=!1,e.success(a.msg)})).catch((()=>{this.loading=!1}))},submit(){this.$refs.ref.validate((a=>{a&&(this.loading=!0,g(this.model).then((a=>{this.loading=!1,e.success(a.msg)})).catch((()=>{this.loading=!1})))}))},modeChange(e){this.model.captcha_type=e}}},[["render",function(e,b,v,g,y,j){const V=_,w=f,k=r,C=n,U=h,x=u,E=c,A=i;return a(),l(A,null,{default:t((()=>[o(w,{span:16},{default:t((()=>[o(E,{ref:"ref",model:y.model,rules:y.rules,"label-width":"120px"},{default:t((()=>[o(k,{label:"验证码开关",prop:"captcha_switch"},{default:t((()=>[o(w,{span:8},{default:t((()=>[o(V,{modelValue:y.model.captcha_switch,"onUpdate:modelValue":b[0]||(b[0]=e=>y.model.captcha_switch=e),"active-value":1,"inactive-value":0},null,8,["modelValue"])])),_:1}),o(w,{span:16},{default:t((()=>[s(" 开启后，后台登录需要输入验证码。 ")])),_:1})])),_:1}),o(k,{label:"验证码方式",prop:"captcha_mode"},{default:t((()=>[o(w,{span:8},{default:t((()=>[o(U,{modelValue:y.model.captcha_mode,"onUpdate:modelValue":b[1]||(b[1]=e=>y.model.captcha_mode=e),placeholder:"",onChange:j.modeChange},{default:t((()=>[(a(!0),d(m,null,p(y.modes,(e=>(a(),l(C,{key:e.value,label:e.label,value:e.value},null,8,["label","value"])))),128))])),_:1},8,["modelValue","onChange"])])),_:1}),o(w,{span:16},{default:t((()=>[s(" 字符：输入字符；行为：滑动或点选。 ")])),_:1})])),_:1}),o(k,{label:"验证码类型",prop:"captcha_type"},{default:t((()=>[o(w,{span:8},{default:t((()=>[1==y.model.captcha_mode?(a(),l(U,{key:0,modelValue:y.model.captcha_type,"onUpdate:modelValue":b[2]||(b[2]=e=>y.model.captcha_type=e),placeholder:""},{default:t((()=>[(a(!0),d(m,null,p(y.typestr,(e=>(a(),l(C,{key:e.value,label:e.label,value:e.value},null,8,["label","value"])))),128))])),_:1},8,["modelValue"])):(a(),l(U,{key:1,modelValue:y.model.captcha_type,"onUpdate:modelValue":b[3]||(b[3]=e=>y.model.captcha_type=e),placeholder:""},{default:t((()=>[(a(!0),d(m,null,p(y.typeaj,(e=>(a(),l(C,{key:e.value,label:e.label,value:e.value},null,8,["label","value"])))),128))])),_:1},8,["modelValue"]))])),_:1})])),_:1}),o(k,null,{default:t((()=>[o(x,{loading:y.loading,onClick:b[4]||(b[4]=e=>j.refresh())},{default:t((()=>[s("刷新")])),_:1},8,["loading"]),o(x,{loading:y.loading,type:"primary",onClick:b[5]||(b[5]=e=>j.submit())},{default:t((()=>[s("提交")])),_:1},8,["loading"])])),_:1})])),_:1},8,["model","rules"])])),_:1})])),_:1})}]]);export{j as default};