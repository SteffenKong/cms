<?php
namespace app\admin\controller;

use app\admin\controller\Common;
use think\Request;

use app\admin\model\Model as ModelObj;

/**
 * Class Model
 * @package app\admin\controller
 * cms模型控制器
 */
class Model extends Common
{
    //模型对象
    protected $modelModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->modelModel = new ModelObj;
    }


    public function index()
    {
        $pageSize = 10;
        $rows = $this->modelModel->getList($pageSize);
        $this->assign('rows',$rows);
        return $this->view->fetch('list');
    }

    /**
     * @return string
     * @throws \think\Exception
     * 添加页面的展示
     */
    public function add()
    {
        return $this->view->fetch();
    }


    /**
     * @param Request $request
     * 添加模型
     */
    public function doAdd(Request $request)
    {
        $data = $request->post();
        $res = $this->modelModel->add($data);
        if(!$res) {
            $this->error('添加失败',url('/admin/Model/add'),'',1);
        }
        $prefix = config('database.prefix');
        //创建附属表
        $this->modelModel->createTable($data['table_name'],$prefix);
        $this->success('添加成功',url('/admin/Model/index'),'',1);
    }

    public function edit()
    {
        $id = input('id','','intval');
        if(empty($id))
        {
            $this->error('非法id操作');
        }

        $row = $this->modelModel->getModelById($id);
        $this->assign('row',$row);
        return $this->view->fetch();
    }

    public function doEdit(Request $request)
    {
        $data = $request->post();

        //判断是否需要改附属表名,如果需要:首先通过id获取旧的附属表名称，然后再进行附属表的重命名
        $id = $data['id'];
        $oldData = $this->modelModel->getModelById($id);
        $oldTableName = $oldData['table_name'];

        //判断附属表是否需要重命名
        if($oldTableName != $data['table_name']) {
            //获取表的前缀
            $prefix = config('database.prefix');
            $oldTableName = $prefix.$oldTableName;
            $newTableName = $prefix.$data['table_name'];
            //如果旧表不等与新表，就需要进行附属表的重命名
            $newTable =
            $this->modelModel->renameTable($oldTableName,$newTableName);
        }

        //修改操作
        $res = $this->modelModel->edit($id,$data);

        if($res === false)
        {
            $this->error('编辑失败',url('/admin/Model/edit',['id'=>$id]),'',1);
        }

        $this->success('编辑成功',url('/admin/Model/index'),'',1);
    }

    /**
     * 删除模型数据,并将对应的模型的附加数据表也一起删除掉
     */
    public function delete()
    {
        $id = input('id','','intval');
        if(empty($id))
        {
            $this->error('非法id操作',url('/admin/Model/index'),'',1);
        }

        //首先平凑出附属表的名称
        $prefix = config('database.prefix');
        $tableName = input('tableName');

        //先删除模型数据
        $res = $this->modelModel->deleteModel($id);

        //然后再删除附属表
        $this->modelModel->dropTable($tableName,$prefix);

        if(!$res) {
            $this->error('删除失败',url('/admin/Model/index'),'',1);
        }

        $this->success('删除成功',url('/admin/Model/index'),'',1);
    }

    /**
     * @param Request $request
     * @return bool|false|float|string
     * 修改数据的状态
     */
    public function changeStatus(Request $request)
    {
        $id = $request->post('id','','intval');
        if(empty($id))
        {
            return json_encode([
                'status'=>'001',
                'message'=>'非法操作'
            ]);
        }

        $res = $this->modelModel->changeStatus($id);

        if(!$res) {
            return json_encode([
                'status'=>'001',
                'message'=>'修改失败'
            ]);
        }

        return json_encode([
            'status'=>'000',
            'message'=>'修改成功'
        ]);
    }
}