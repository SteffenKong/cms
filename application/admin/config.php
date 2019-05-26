<?php
return [
    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'view_suffix'  => 'htm',

    ],
    'url_html_suffix'=>'',
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__ADMIN__'=>'/static/admin'
    ],
];