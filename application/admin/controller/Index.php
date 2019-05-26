<?php
namespace app\admin\controller;

use app\admin\controller\Common;

/**
 * Class Index
 * @package app\admin\controller
 * 网站首页控制器
 */
class Index extends Common
{

    /**
     * 网站后台首页
     * @return string
     * @throws \think\Exception
     */
    public function index() {
        return $this->view->fetch();
    }
}
