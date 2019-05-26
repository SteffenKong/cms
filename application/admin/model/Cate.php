<?php
namespace app\admin\model;

use think\Model;
use app\common\lib\Tree;
use think\Request;

/**
 * Class Cate
 * @package app\admin\model
 * 栏目模型
 */
class Cate extends Model
{

    /**
     * 获取所有的栏目
     */
    public function getList()
    {
        $rows = $this->order('sort','desc')->select();
        return Tree::getList($rows);
    }


    /**
     * @param $data
     * @return false|int
     * 添加栏目数据
     */
    public function add($data) {
        return $this->save($data);
    }


    /**
     * @param $id
     * @param $data
     * 编辑数据
     */
    public function edit($id,$data) {
        $this::update($data,['id'=>$id]);
    }


    /**
     * @param $id
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 编辑栏目状态
     */
    public function changeStatus($id)
    {
        $row = $this->field('status')->where('id',$id)->find();
        $status = $row['status'];
        $flag = 0;
        if($status == 0){
            $flag = 1;
        }
        return $this->where('id',$id)->setField('status',$flag);
    }

    /**
     * @param $id
     * @return int
     * 删除栏目
     */
    public function deleteCate($id) {
        $data = $this->field('id,parent_id')->select();
        $ids = $this->getChildeIds($data,$id);
        return $this->whereIn('id',$ids)->delete();
    }


    /**
     * @param $ids
     * 批量删除栏目
     */
    public function deleteCates($ids)
    {
        return $this->whereIn('id',$ids)->delete();
    }


    /**
     * @param $id
     * 更改排序数值
     */
    public function changeSort($cateId,$sortValue)
    {
        return $this->where('id',$cateId)->setField('sort',$sortValue);
    }

    /**
     * @param $data
     * @param $id
     * 获取当前栏目下的所有子栏目id
     */
    public function getChildeIds($data,$id)
    {
        $subIds = $this->_getChildeIds($data,$id);
        $subIds[] = $id;
        return $subIds;
    }

    public function _getChildeIds($data,$id)
    {
        static $subIds = [];
        foreach ($data as $k=>$v)
        {
            if($v['parent_id'] == $id)
            {
                $subIds[] = $v['id'];
                $this->getChildeIds($data,$v['id']);
            }
        }
        return $subIds;
    }

    /**
     * @param $id
     * 获取指定栏目id的栏目
     */
    public function getCateById($id)
    {
        return $this->where('id',$id)->find();
    }
}