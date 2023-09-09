$.ajax({
    type: "{$data.method}",
    url: "{$data.url}",
    contentType : "application/json; charset=utf-8",
    dataType: "json",
    data: JSON.stringify({
{foreach $data.param as $k=>$item}
        {$item.name}: "",
{/foreach}
    }),
    success: function (res) {
        // 处理成功响应
    },
    error: function (res) {
        // 处理成功响应
    },
});