<?php
namespace app\admin\model;

use think\Model;

/**
 * Class Conf
 * @package app\admin\model
 * 配置管理模型
 */
class Conf extends Model {

    /**
     * @param $data
     * @return false|int
     * 添加配置信息
     */
    public function addData($data) {
        return $this->save($data);
    }

    /**
     * @param $pageSize
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * 分页获取配置列表
     */
    public function getList($pageSize)
    {
        return $this->paginate($pageSize);
    }

    /**
     * @param $ename
     * @return bool
     * @throws \think\Exception
     * 判断配置英文名是否存在
     */
    public function getENameIsExists($ename) {
        $count = $this->where('ename',$ename)->count();
        return (bool)$count;
    }

    /**
     * @param $cname
     * @return bool
     * @throws \think\Exception
     * 判断配置中文名是否存在
     */
    public function getCNameIsExists($cname) {
        $count = $this->where('cname',$cname)->count();
        return (bool)$count;
    }

    /**
     * @param $id
     * @param $ename
     * @return bool
     * @throws \think\Exception
     * 判断除了自身，是否和其他的配置英文名冲突
     */
    public function getENameIsExistsById($id,$ename) {
        $count = $this->where([
            'id'=>['neq',$id],
            'cname'=>['eq',$ename]
        ])->count();
        return (bool)$count;
    }

    /**
     * @param $id
     * @param $cname
     * @return bool
     * @throws \think\Exception
     * 判断除了自身，是否和其他的配置中文名冲突
     */
    public function getCNameIsExistsById($id,$cname) {
        $count = $this->where([
            'id'=>['neq',$id],
            'cname'=>['eq',$cname]
        ])->count();
        return (bool)$count;
    }

    /**
     * @param $id
     * @param $data
     * @return Conf
     * 编辑配置数据
     */
    public function edit($id,$data) {
        return $this::update($data,['id'=>['eq',$id]]);
    }

    /**
     * @param $id
     * 通过id获取单条配置数据
     */
    public function getDataById($id) {
        return $this->where('id',$id)->find();
    }

    /**
     * @param $id
     * @return int
     * 删除配置数据
     */
    public function deleteConf($id) {
        return $this->where('id',$id)->delete();
    }
}