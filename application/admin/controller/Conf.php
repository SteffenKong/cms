<?php
namespace app\admin\controller;

use app\admin\controller\Common;
use app\admin\model\Conf as ConfModel;
use think\Request;
use think\Loader;

/**
 * Class Conf
 * @package app\admin\controller
 * 网站配置控制器
 */
class Conf extends Common {

    /**
     * @var ConfModel|null
     * 配置模型对象
     */
    protected $confModel = null;

    public function __construct()
    {
        parent::__construct();
        $this->confModel = new ConfModel;
    }


    /**
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * 配置列表下显示
     */
    public function index() {
        $pageSize = 10;
        $data = $this->confModel->getList($pageSize);
        $this->assign('rows',$data);
        return $this->view->fetch('list');
    }

    /**
     * @param Request $request
     * @return string
     * @throws \think\Exception
     * 加载添加配置的模板界面
     */
    public function add() {
        return $this->view->fetch();
    }


    /**
     * @param Request $request
     * @throws \think\Exception
     * 添加配置数据
     */
    public function doAdd(Request $request) {
        $data = $request->post();
        $validate = Loader::validate('conf');
        if(!$validate->scene('add')->check($data)) {
            $this->error($validate->getError(),url('admin/Conf/add'),'',1);
        }
        if($this->confModel->getCNameIsExists($data['cname'])) {
            $this->error('配置中文名已存在',url('admin/Conf/add'),'',1);
        }
        if($this->confModel->getENameIsExists($data['ename'])) {
            $this->error('配置英文名已存在',url('admin/Conf/add'),'',1);
        }

        if(!empty($data['values'])) {
            $data['values'] = str_replace('，',',',$data['values']);
        }

        $res = $this->confModel->addData($data);
        if($res!=false)
        {
            $this->success('添加成功',url('admin/Conf/index'),'',1);
        }
        $this->error('添加失败',url('admin/Conf/add'),'',1);
    }

    /**
     * @param Request $request
     * @throws \think\Exception
     * 展示配置编辑页面
     */
    public function edit() {
        $id = input('id','','intval');
        if(empty($id))
        {
            $this->error('非法访问',url('admin/Conf/add'),'',1);
        }
        $row = $this->confModel->getDataById($id);
        $this->assign('row',$row);
        return $this->view->fetch();
    }

    /**
     * @param Request $request
     * @throws \think\Exception
     * 编辑配置数据
     */
    public function doEdit(Request $request) {
        $data = $request->post();
        $validate = Loader::validate('Conf');
        if(!$validate->scene('edit')->check($data)) {
            $this->error($validate->getError(),url('admin/Conf/edit',['id'=>$data['id']]),'',1);
        }

        if($this->confModel->getCNameIsExistsById($data['id'],$data['cname']))
        {
            $this->error('配置中文名已存在',url('admin/Conf/edit',['id'=>$data['id']]),'',1);
        }

        if($this->confModel->getENameIsExistsById($data['id'],$data['ename']))
        {
            $this->error('配置英文名已存在',url('admin/Conf/edit',['id'=>$data['id']]),'',1);
        }

        $res = $this->confModel->edit($data['id'],$data);
        if($res!==false) {
            $this->success('编辑成功',url('admin/Conf/index'),'',1);
        }
        $this->error('编辑失败',url('admin/Conf/edit',['id'=>$data['id']]),'',1);
    }

    /**
     * 删除配置数据
     */
    public function delete() {
        $id = input('id','','intval');
        $data['id'] = $id;
        $validate = Loader::validate('Conf');
        if(!$validate->scene('delete')->check($data)) {
            $this->error($validate->getError(),url('admin/Conf/index'),'',1);
        }

        $res = $this->confModel->deleteConf($id);
        if($res!==false) {
            $this->success('删除成功',url('admin/Conf/index'),'',1);
        }
        $this->error('删除失败',url('admin/Conf/index'),'',1);

    }


    /**
     * @return string
     * @throws \think\Exception
     * 网站配置列表界面
     */
    public function conf() {
        //取出所有的配置信息
        $confRes = db('conf')->select();
        $this->assign('confRes',$confRes);
        return $this->view->fetch();
    }


    /**
     * @param Request $request
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * 网站配置值的处理
     */
    public function doConf(Request $request) {
        $data = $request->post();

        //取出所有配置的英文名称
        $enames = db('conf')->field('ename')->select();

        /******* 处理附件类型的数据（文件上传）*******/

        $uploadFile = ROOT_PATH.'/public/static/admin/uploads/';

        //首先取出附件类型的数据
        $file = db('conf')->where('dt_type',6)->field('ename')->select();
        foreach ($file as $k=>$v) {
            //判断一下是否有附件需要上传
            if(isset($_FILES[$v['ename']]) && $_FILES[$v['ename']]['error'] === 0) {

                //判断一下是否有旧附件，如果有就进行删除，然后重新进行上传
                $oldFilePath = db('conf')->where([
                    'ename'=>['eq',$v['ename']],
                    'dt_type'=>['eq',6]
                ])->column('value');

                //删除文件
                unlink($uploadFile.$oldFilePath[0]);

                //上传附件
                $fileObj = $request->file($v['ename']);
                $info = $fileObj->validate([
                    'size'=>1024*1024*1024
                ])->move($uploadFile);


                //获取附件上传的路径
                $filePath = $info->getSaveName();

                //然后写入数据库
                db('conf')->where('ename',$v['ename'])->setField('value',$filePath);
            }
        }




        //比较一下前台是否有复选框没有选择，如果没有选择就讲该配置项的数据清空掉
        $dataKey = array_keys($data);
        foreach ($enames as $k=>$v) {
            if(!in_array($v['ename'],$dataKey)) {
                db('conf')->where([
                    'ename'=>['eq',$v['ename']],
                    'dt_type'=>['neq',6]
                ])->update(['value'=>'']);
            }
        }

        //批量修改配置项的值value
        foreach ($data as $k=>$v) {
            if(is_array($v)) {
                db('conf')->where('ename',$k)->update(['value'=>implode(',',$v)]);
            }else{
                db('conf')->where('ename',$k)->update(['value'=>$v]);
            }
        }

        $this->success('配置成功',url('/admin/Conf/conf'),'',1);
    }
}