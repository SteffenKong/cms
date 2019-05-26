<?php
namespace app\admin\model;
use think\Model as parentModel;

/**
 * Class Model
 * @package app\admin\model
 * cms模型 模型器
 */
class Model extends parentModel
{
    /**
     * @param int $pageSize
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * 分页获取模型列表的数据
     */
    public function getList($pageSize=10)
    {
        return $this->order('id','desc')->paginate($pageSize);
    }

    /**
     * @param $data
     * @return false|int
     * 添加模型数据
     */
    public function add($data)
    {
        return $this->save($data);
    }

    /**
     * @param $id
     * @param $data
     * @return false|int
     * 编辑模型数据
     */
    public function edit($id,$data)
    {
        return $this->save($data,['id'=>$id]);
    }


    /**
     * @param $id
     * 获取指定id模型的数据
     */
    public function getModelById($id)
    {
        return $this->where('id',$id)->find();
    }

    /**
     * @param $id
     * 通过id修改某个数据的状态
     */
    public function changeStatus($id)
    {
        //首先查出来当前数据的状态
        $statusData = $this->field('status')->where('id',$id)->find();

        $status = 0;    //默认修改为0

        //如果当前状态为0那就改为1
        if($statusData['status'] == 0)
        {
            $status = 1;
        }

        return  $this->where('id',$id)->setField('status',$status);
    }


    /**
     * @param $id
     * @return int
     * 删除模型数据
     */
    public function deleteModel($id)
    {
        return $this->where('id',$id)->delete();
    }


    /**
     * @param $tableName
     * @param $prefix
     * 创建附属表
     */
    public function createTable($tableName,$prefix)
    {
        //拼凑附属表的全名
        $tableAllName = $prefix.$tableName;
        $sql = "CREATE TABLE {$tableAllName}(aid int unsigned primary key)CHARSET=utf8,ENGINE=INNODB";
        return $this->query($sql);
    }

    /**
     * @param $tableName
     * @param $prefix
     * 删除附属表
     */
    public function dropTable($tableName,$prefix)
    {
        //拼凑附属表的全名
        $tableAllName = $prefix.$tableName;
        $sql = "DROP TABLE IF EXISTS {$tableAllName}";
        return $this->query($sql);
    }

    /**
     * @param $oldName
     * @param $newName
     * 重命名附属表
     */
    public function renameTable($oldName,$newName)
    {
        $sql = "ALTER TABLE {$oldName} RENAME {$newName}";
        return $this->query($sql);
    }
}