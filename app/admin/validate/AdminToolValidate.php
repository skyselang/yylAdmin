<?php
/*
 * @Description  : 实用工具验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-15
 * @LastEditTime : 2020-08-15
 */

namespace app\admin\validate;

use think\Validate;

class AdminToolValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'string_str' => ['require'],
        'strran_ids' => ['require'],
        'strran_len' => ['require', 'egt:1'],
        'timestamp_trantype'  => ['require', 'checkTimestampTrantype'],
        'timestamp_datetime'  => ['require', 'date'],
        'timestamp_timestamp' => ['require', 'number', 'integer'],
        'qrcode_str' => ['require'],
    ];

    // 错误信息
    protected $message  =   [
        'string_str.require' => '请输入字符串',
        'strran_ids.require' => '请选择所用字符',
        'strran_len.require' => '请选择字符长度',
        'strran_len.egt'     => '字符长度必须大于0',
        'timestamp_trantype.require'                => '转换类型必须',
        'timestamp_trantype.checkTimestampTrantype' => '转换类型错误',
        'timestamp_datetime.require'                => '请选择时间',
        'timestamp_datetime.date'                   => '请选择有效的时间',
        'timestamp_timestamp.require'               => '请输入时间戳',
        'timestamp_timestamp.number'                => '请输入有效的时间戳',
        'timestamp_timestamp.integer'               => '请输入有效的时间戳',
        'qrcode_str.require' => '请输入文本内容',
    ];

    // 验证场景
    protected $scene = [
        'string' => ['string_str'],
        'strran' => ['strran_ids', 'strran_len'],
        'timestamp_type' => ['timestamp_trantype'],
        'timestamp_time' => ['timestamp_trantype', 'timestamp_datetime'],
        'timestamp_date' => ['timestamp_trantype', 'timestamp_timestamp'],
        'qrcode' => ['qrcode_str'],
    ];

    // 时间戳转换类型验证
    protected function checkTimestampTrantype($value, $rule, $data = [])
    {
        $check = false;
        if ($data['trantype'] != 'time' && $data['trantype'] != 'date') {
            $check = true;
        }

        return $check ? '转换类型错误' : true;
    }
}
