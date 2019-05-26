<?php
namespace app\admin\model;

use think\Model;

/**
 * Class ModelFields
 * @package app\admin\model
 * 模型字段 模型器
 */
class ModelFields extends Model
{

    /**
     * @param $pageSize
     * 分页获取模型字段的数据
     */
//    public function getList($pageSize)
//    {
//        return $this->order('id','desc')->paginate($pageSize);
//    }

    /**
     * @param $data
     * 添加模型字段数据
     */
    public function add($data)
    {
        return $this->save($data);
    }

    /**
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 通过id获取指定数据
     */
    public function getDataById($id)
    {
        return $this->where('id',$id)->find();
    }

    /**
     * @param $id
     * @return int
     * 删除字段数据
     */
    public function deleteField($id)
    {
        return $this->where('id',$id)->delete();
    }

    /**
     * @param $data
     * @return ModelFields
     * 编辑数据
     */
    public function edit($data)
    {
        return $this::update($data,['id'=>$data['id']]);
    }
}