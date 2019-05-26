<?php
namespace app\admin\controller;

use app\admin\controller\Common;
use think\Request;
use app\admin\model\Cate as CateModel;
use app\common\lib\Tree;

/**
 * Class Cate
 * @package app\admin\controller
 * 栏目控制器
 */
class Cate extends Common {

    protected $cateModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->cateModel = new CateModel();
    }


    /**
     * @return string
     * @throws \think\Exception
     * 列表页
     */
    public function index() {
        $allCates = db('cate')->alias('a')->field("a.*,b.model_name")->join('cms_model b','a.model_id = b.id')->select();
        $rows = Tree::getList($allCates);
        $this->assign('rows',$rows);
        return $this->view->fetch('list');
    }


    /**
     * @return string
     * @throws \think\Exception
     * 展示添加界面的模板
     */
    public function add() {

        $id = input('pid','','intval');
        if(isset($id))
        {
            $this->assign('pid',$id);
        }

        //取出所有的模型
        $allModels = db('model')->select();
        //取出所有的栏目
        $rows = $this->cateModel->getList();
        $this->assign([
            'rows'=>$rows,
            'models'=>$allModels
        ]);
        return $this->view->fetch();
    }


    /**
     * @param Request $request
     * 编辑界面
     */
    public function edit()
    {
        $id = input('id','','intval');
        if(!empty($id))
        {
            //获取当前栏目
            $row = $this->cateModel->getCateById($id);

            //获取所有栏目
            $allCates = $this->cateModel->getList();

            //取出所有的模型
            $allModels = db('model')->select();

            $this->assign([
                'row'=>$row,
                'models'=>$allModels,
                'rows'=>$allCates
            ]);

            return $this->view->fetch();
        }else{
            $this->error('非法id');
        }
    }


    /**
     * @param Request $request
     * 编辑列表数据
     */
    public function doEdit(Request $request)
    {
        $data = $request->post();
        $_data = [];    //用于存放选中的字段
        //判断是否选中status复选框，如果没有选中就默认赋予status一个值为1
        $_data = array_keys($data);

        if(!in_array('status',$_data))
        {
            $data['status'] = 1;
        }

        $res = $this->cateModel->edit($data['id'],$data);
        if($res === false)
        {
            $this->error('编辑失败',url('/admin/Cate/edit',['id'=>$data['id']]),'',1);
        }
        $this->success('编辑成功',url('/admin/Cate/index'),'',1);
    }

    /**
     * @param Request $request
     * 添加栏目数据
     */
    public function doAdd(Request $request) {
        $data = $request->post();
        $res = $this->cateModel->add($data);
        if($res!==false)
        {
            $this->success('录入成功',url('admin/Cate/index'),'',1);
        }

        $this->error('录入失败',url('admin/Cate/add'),'',1);
    }

    /**
     * @param Request $request
     * 图片上传
     */
    public function uploadImg(Request $request) {
        //是否有文件上传
        if(isset($_FILES['file']) && $_FILES['file']['error'] === 0 )
        {
            $Path = str_replace('\\','/',ROOT_PATH.'/public/static/admin/CateUploads');
            if(!file_exists($Path))
            {
                mkdir($Path);
            }

            $fileInfo = $request->file('file');
            $uploadInfo = $fileInfo->move($Path);

            $allPath = $uploadInfo->getSaveName();


            return json_encode([
                'status'=>'000',
                'path'=>$allPath
            ]);
        }
    }

    /**
     * @param Request $request
     * 修改栏目状态
     */
    public function changeStatus(Request $request)
    {
        if($request->isAjax())
        {
            $id = $request->post('id','','intval');

            $res = $this->cateModel->changeStatus($id);

            if($res!==false){
                return json_encode([
                    'status'=>'000',
                    'message'=>'编辑成功'
                ]);
            }else{
                return json_encode([
                    'status'=>'001',
                    'message'=>'编辑失败'
                ]);
            }

        }else{
            $this->error('非法访问');
        }
    }


    /**
     * @param Request $request
     * 排序功能以及批量删除功能
     */
    public function sortData(Request $request)
    {
        $data = $request->post();

        $sortItem = $data['sort'];

        foreach ($sortItem as $k=>$v)
        {
            $this->cateModel->changeSort($k,$v);
        }

        $this->success('保存数据成功',url('admin/Cate/index'),'',1);
    }

    /**
     * @param Request $request
     * 删除栏目
     */
    public function delete(Request $request)
    {
        //获取栏目id
        $id = input('id','','intval');

        //执行栏目的删除以及判断该栏目下是否含有子栏目,如果有也一起删除掉
        $res = $this->cateModel->deleteCate($id);
        if ($res)
        {
            $this->success('删除成功',url('admin/Cate/index'),'',1);
        }else{
            $this->error('删除失败',url('admin/Cate/index'),'',1);
        }
    }

    /**
     * @param Request $request
     * 删除栏目的图片(撤销功能)
     */
    public function delCatePic(Request $request)
    {
        $filePath = $request->post('filePath');

        $res = unlink(ROOT_PATH.'/public/static/admin/CateUploads/'.$filePath);

        if(!$res)
        {
            return json_encode(
                [
                    'status'=>'001',
                    'message'=>'删除失败'
                ]
            );
        }

        return json_encode(
            [
                'status'=>'000',
                'message'=>'删除成功'
            ]
        );
    }

    /**
     * @param Request $request
     * 利用ajax收缩子栏目
     */
    public function ajaxList(Request $request)
    {
        $id = $request->post('id');

        //查出所有的$data的数据
        $data = db('cate')->select();

        //获取当前id下的所有子节点
        $ids = $this->cateModel->_getChildeIds($data,$id);

        if(empty($ids))
        {
            return json_encode([
                'status'=>'001',
                'data'=>[],
            ]);
        }

        return json_encode([
            'status'=>'000',
            'data'=>$ids
        ]);
    }

    public function ajaxCateInfo(Request $request)
    {
        $cateId = $request->post('catId');
        if(empty($cateId))
        {
            return json_encode([
                'status'=>'000',
                'message'=>'非法操作'
            ]);
        }

        //查找出该栏目的数据
        $data = db('cate')->find($cateId);

        return json_encode([
            'status'=>'000',
            'message'=>'非法操作',
            'data'=>$data
        ]);
    }
}