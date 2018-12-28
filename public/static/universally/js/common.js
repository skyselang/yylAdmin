/*
*@Name: layuiUniversalCompany - 通用企业公司网站模板
*@Author: xuzhiwen
*@Copyright:layui.com
*/


layui.define(['jquery','element','util','laytpl','layer'],function(exports){

  var $ = layui.jquery
          ,element = layui.element
          ,layer = layui.layer
          ,util = layui.util
          ,laytpl = layui.laytpl;
          // var off = false;
  var gather = {
    // waterfall: function(){
    //   // off = true;
    //   console.log(1)
    //   // console.log(2)
    //   var box = $('.imgtext-flow .layui-col-md6');
    //   // console.log(box.eq(0).outerHeight())
    //   var boxWidth = box.eq(0).width();
    //   // console.log(boxWidth)
    //   var num = Math.ceil($('#item-list-box').width() / boxWidth);
    //   // console.log(boxWidth,num)
    //   var boxArr = [];
    //   box.each(function(index,value){
    //     var boxHeight = box.eq(index).height();
    //     if(index < num){
    //       // console.log(index,num)
    //       // console.log(index)
    //       $(value).css({
    //         "position":"absolute",
    //         'top':0,
    //         "left":index*box.width(),
    //       });
    //       boxArr[index] = boxHeight;
    //       // box.eq(num-1).css('float','right')
    //       // console.log(value)
    //     } else {
    //       var minboxHeight = Math.min.apply(null,boxArr),minboxIndex = $.inArray(minboxHeight,boxArr)
    //       // console.log(value)
    //       $(value).css({
    //         "position":"absolute",
    //         "top":minboxHeight + 15,
    //         "left":box.eq(minboxIndex).position().left,
    //       });
   
    //       boxArr[minboxIndex] += box.eq(index).height() + 15;
        
    //       // if(boxHeight + minboxHeight > $('#item-list-box').height()){
    //         $('#item-list-box').height(50 + boxArr[minboxIndex]);
    //       // }
    //     }

    //   });
    // },
    json:function(url,data,func,options){
      var that = this, type = typeof data === 'function';
      if(type){
        options = func
        func =  data;
        data = {};
      }
      options = options || {};
      // console.log(off)
      // if(off){
        return $.ajax({
          type: options.type || 'get',
          dataType: options.dataType || 'json',
          data : data,
          url: url,
          success: function(res){
            func && func(res);
          },error:function(e){
            layer.msg('请求异常,请重试',{shift:6});
            options.error && options.error(e);
          }
        });
      // }
    },
  };
  exports('common',gather)
});