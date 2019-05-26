create database if not exists cms charset=utf8;

use cms;

-- 配置表
create table if not exists cms_conf(
    id mediumint unsigned not null auto_increment comment '配置id唯一标识',
    cname varchar(32) not null comment '配置中文名',
    ename varchar(32) not null comment '配置英文名',
    value varchar(255) not null comment '配置的选项名',
    `values` varchar(255)  comment '配置的值,可不填',
    dt_type tinyint unsigned not null default 1 comment '配置的类型,1 - 文本框 2 - 单选框 3 - 复选框 4 - 下拉菜单 5 - 文本域 6 - 附件（文件上传）',
    cf_type tinyint unsigned not null comment '配置的设置类型,1 - 基本信息 2 - 联系方式 3 - seo设置',
    primary key(id),
    unique key(cname),
    unique key(ename)
)charset=utf8,engine=innodb;


-- 栏目表
create table if not exists cms_cate(
    id mediumint unsigned auto_increment comment '栏目唯一标识',
    title varchar(64) not null comment '栏目名称',
    keyword varchar(64) not null comment '栏目关键字',
    des text comment '栏目描述',
    content text comment '栏目内容',
    status tinyint default 1 comment '栏目状态 0 - 隐藏 1 - 显示',
    image varchar(255) comment '栏目图片',
    cate_attr varchar(32) not null comment '栏目属性 1 - 列表栏目  2 - 封面栏目  3 - 外链栏目' ,
    list_tmp varchar(64) not null comment '列表页模板',
    index_tmp varchar(64) not null comment '频道页模板',
    article_tmp varchar(64) not null comment '内容页模板',
    sort mediumint default 50 comment '排序值',
    pid mediumint unsigned not null default 0 comment '父级栏目id 0代表是顶级id',
    link varchar(100)  comment '外链地址',
    primary key pkid (id),
    unique key uktitle (title),
    index idx_status (status),
    index idx_pid (pid)
)charset=utf8,engine=innodb;


-- 模型表
create table if not exists cms_model(
    id mediumint unsigned auto_increment comment '模型id',
    model_name varchar(32) not null comment '模型名称',
    table_name varchar(32) not null comment '附加表名',
    status tinyint not null default 0 comment '状态 1 - 开启 0 - 禁用',
    primary key pkid (id),
    unique key ukmodel_name(model_name)
)charset=utf8,engine=innodb;


-- 模型字段表(附属表)
create table if not exists cms_model_field(
    id mediumint unsigned auto_increment comment 'id',
    field_cname varchar(191) not null comment '字段中文名',
    field_ename varchar(191) not null comment '字段英文名',
    field_type tinyint unsigned not null comment '字段类型: 1 - 单行文本 2 - 单选按钮 3 - 复选按钮 4 - 下拉菜单 5 - 文本域 6 - 附件',
    field_values varchar(191) not null comment '可选值',
    model_id mediumint unsigned not null comment '所属模型id',
    primary key(id),
    unique key(field_cname),
    unique key(field_ename)
)charset=utf8,engine=innodb;


-- 文档主表
create table if not exists cms_archives(
    id mediumint unsigned auto_increment comment 'id',
    title varchar(191) not null comment '文档标题',
    keywords varchar(191) not null comment '文档关键字',
    description text not null comment '文档简介',
    author varchar(64) not null comment '文档作者',
    source varchar(191) not null comment '文档来源',
    thumb varchar(191) not null comment '缩略图位置',
    clicks int not null comment '点击量',
    attr varchar(64) not null comment '文档属性',
    content text comment '文档内容',
    cate_id mediumint unsigned comment '栏目id',
    model_id mediumint unsigned comment '模型id',
    add_time int unsigned not null default 0 comment '发布时间',
    primary key (id),
    unique key(title)
)charset=utf8,engine=innodb;