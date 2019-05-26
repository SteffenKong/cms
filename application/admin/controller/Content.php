<?php
namespace app\admin\controller;

use app\admin\controller\Common;
use think\Db;
use app\admin\model\Cate;
use think\Request;

/**
 * Class Content
 * @package app\admin\controller
 * 文档控制器
 */
class Content extends Common
{
    protected $cateModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->cateModel = new Cate;
    }

    /**
     * @return string
     * @throws \think\Exception
     * 展示列表数据
     */
    public function index()
    {
        $modelId = input('model_id','','intval');
        $cateId =  input('cate_id','','intval');
        if(!$modelId || !$cateId) {
            $modelId = 0;
            $cateId = 0;
        }

        $this->assign([
            'modelId'=>$modelId,
            'cateId'=>$cateId,
        ]);

        return $this->view->fetch('list');
    }

    /**
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 添加数据
     */
    public function add()
    {

        $modelId = input('modelId','','intval');
        $cateId = input('cateId','','intval');
        //获取所有的模型
        $modelData = db('model')->field('id,model_name')->select();

        //获取所有栏目数据
        $allCates = $this->cateModel->getList();

        //获取当前模型自定义的字段
        $diyFields = db('model_fields')->where('model_id',$modelId)->select();

        $this->assign([
           'modelData'=>$modelData,
            'allCates'=>$allCates,
            'modelId'=>$modelId,
            'cateId'=>$cateId,
            'diyFields'=>$diyFields,
        ]);
        return $this->view->fetch();
    }


    public function doAdd()
    {

    }
}