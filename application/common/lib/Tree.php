<?php
namespace app\common\lib;

/**
 * Class Tree
 * @package app\common\lib
 */
class Tree {

    /**
     * @param $items
     * @param int $pid
     * @param int $level
     * @return array
     * 无限极分类
     */
    public static function getList($items,$pid=0,$level=0)
    {
        static $tree = [];
        foreach ($items ?? [] as $k=>$v)
        {
            if ($v['parent_id'] == $pid)
            {
                $v['level'] = $level;
                $tree[] = $v;
                self::getList($items,$v['id'],$level+1);
            }
        }
        return $tree;
    }
}