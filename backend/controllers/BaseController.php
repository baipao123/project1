<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/10
 * Time: 下午11:05
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;

class BaseController extends Controller {
    
    public $header = [
//        [
//            "class"  => "user",
//            "title"  => "用户管理",
//            "access" => "",
//            "href"   => "",
//        ],
    ];
    
    public $sideMenu = [
        [
            "class"  => "user",
            "title"  => "用户管理",
            "access" => "",//使用eval执行来判断是否显示
            "show"   => false,//是否展开菜单、默认展开
            "href"   => "",
            "items"  => [
                [
                    "class" => "list",
                    "title" => "用户列表",
                    "href"  => "/user/list",
                ],
                [
                    "class" => "info",
                    "title" => "用户详情",
                    "href"  => "/user/info",
                ]
            ],
        ],
        [
            "class" => "leader",
            "title" => "辅导员管理",
            "access" => ">=99",
            "href"=>"",
            "items"=>[
                [
                    "class"=>"list",
                    "title"=>"辅导员列表",
                    "href"=>"/leader/list"
                ]
            ]
        ],
        [
            "class"=>"help",
            "title"=>"说明文档",
            "href"=>"/user/help",
        ]
    ];
    
    public $headerMenuTitle;
    public $sideMenuTitle;
    public $sideMenuItem;
    
    public function beforeAction($action) {
        Yii::$app->layout= "@backend/views/layouts/layui.php";
        return parent::beforeAction($action);
    }
    
    public function sideMenu() {
        $access = Yii::$app->user ? Yii::$app->user->access : 1;
        if(empty($this->sideMenuTitle))
            $this->sideMenuTitle = Yii::$app->controller->id;
        if(empty($this->sideMenuItem))
            $this->sideMenuItem = Yii::$app->controller->action->id;
        $str = '<ul class="layui-nav layui-nav-tree"  lay-filter="side">';
        foreach (Yii::$app->controller->sideMenu as $menu) {
            if (isset($menu['access']) && !empty($menu['access']) && !eval("return ".$access . $menu['access'].";"))
                continue;
            $show = $this->sideMenuTitle != $menu['class'] && (!isset($menu['items']) || empty($menu['items']) || (isset($menu['show']) && !$menu['show'])) ? false : true;
            $str .= '<li class="layui-nav-item ' . ($show ? 'layui-nav-itemed' : '') . '">';
            if (!isset($menu['items']) || empty($menu['items'])) {
                $is_light = $this->sideMenuTitle == $menu['class'] && $this->sideMenuItem == Yii::$app->controller->action->id ? true : false;
                if (!isset($menu['href']) || empty($menu['href']))
                    $menu['href'] = 'javascript:;';
                $str .= '<a href="' . $menu['href'] . '"' . ($is_light ? ' class="layui-this" ' : '') . '>' . $menu['title'] . '</a>';
            } else {
                $str .= '<a href="javascript:;">' . $menu['title'] . '</a>';
                $str .= '<dl class="layui-nav-child">';
                foreach ($menu['items'] as $sub){
                    if (isset($sub['access']) && !empty($sub['access']) && !eval("return ".$access . $sub['access'].";"))
                        continue;
                    $is_light = $this->sideMenuTitle == $menu['class'] && $this->sideMenuItem == $sub['class'] ? true : false;
                    if(!isset($sub['href']) || empty($sub['href']))
                        $sub['href'] = "javascript:;";
                    $str .= '<dd><a href="' . $sub['href'] . '"' . ($is_light ? ' class="layui-this"' : '') . '>' . $sub['title'] . '</a></dd>';
                }
                $str .= '</dl>';
            }
            $str .= '</li>';
        
        }
        $str .= '</ul>';
        
        return $str;
    }
    
    public function headerMenu(){
        if(empty($this->header))
            return "";
        $access = Yii::$app->user ? Yii::$app->user->access : 1;
        if(empty($this->headerMenuTitle))
            $this->headerMenuTitle = Yii::$app->module;
        
        $str = '<ul class="layui-nav layui-layout-left">';
        $tmp = '';
        foreach ($this->header as $menu){
            if (isset($menu['access']) && !empty($menu['access']) && !eval("return ".$access . $menu['access'].";"))
                continue;
            $is_light = $this->headerMenuTitle == $menu['class'] ? true : false;
            if(!isset($menu['href']) || empty($menu['href']))
                $menu['href'] = "javascript:;";
            $tmp .= '<li class="layui-nav-item' . ($is_light ? ' layui-this' : '') . '"><a href="' . $menu['href'] . '">' . $menu['title'] . '</a></li>';
        }
        if(empty($tmp))
            return "";
        $str .= $tmp;
        $str .= '</ul>';
        return $str;
    }
    
}