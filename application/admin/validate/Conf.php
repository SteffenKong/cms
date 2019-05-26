<?php
namespace app\admin\validate;

use think\Validate;

class Conf extends Validate {

    //配置项的校验规则
    protected $rule = [
        'id'=>'require|number',
        'cname'=>'require',
        'ename'=>'require',
        'dt_type'=>'notIn:0|require|number',
        'cf_type'=>'notIn:0|require|number'
    ];

    //配置项的校验规则错误提示信息
    protected $message = [
        'id.require'=>'id不能为空',
        'id.number'=>'id类型必须为整型',
        'cname.require'=>'中文名不能为空',
        'ename.require'=>'英文名不能为空',
        'dt_type.notIn'=>'配置类型取值异常',
        'dt_type.number'=>'配置类型异常',
        'dt_type.require'=>'请选择配置类型',
        'cf_type.notIn'=>'配置数据类型取值异常',
        'cf_type.number'=>'配置数据类型异常',
        'cf_type.require'=>'请选择配置数据类型'
    ];

    //配置项的校验规则的校验场景
    protected $scene = [
        'add'=>['cname','ename','dt_type','cf_type'],
        'edit'=>['id','cname','ename','dt_type','cf_type'],
        'delete'=>['id']
    ];
}