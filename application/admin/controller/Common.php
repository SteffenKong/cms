<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;

class Common extends Controller {

    public function _initialize()
    {
        parent::_initialize();
        $this->getControllerAndAction();
    }

    /**
     * 获取控制器的名称和方法的名称
     */
    public function getControllerAndAction() {
        $request = Request::instance();
        $controllerName = $request->controller();
        $actionName = $request->action();
        $this->assign('controller',$controllerName);
        $this->assign('action',$actionName);
    }
}