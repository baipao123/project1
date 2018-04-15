<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/14
 * Time: 下午5:27
 */

namespace backend\baipao123\layuiAdm\layouts\actions;

use Yii;


class MenuAction extends ApiAction
{
    public $module;

    public function run() {
        echo '[
		{
			"title": "文章列表",
			"icon": "icon-text",
			"href": "/site/error",
			"spread": false
		},
		{
			"title": "图片管理",
			"icon": "&#xe634;",
			"href": "#2",
			"spread": false
		},
		{
			"title": "其他页面",
			"icon": "&#xe630;",
			"href": "",
			"spread": true,
			"children": [
				{
					"title": "404页面",
					"icon": "&#xe61c;",
					"href": "/site/error",
					"spread": false
				},
				{
					"title": "登录",
					"icon": "&#xe609;",
					"href": "#3",
					"spread": false,
					"target": "_blank"
				}
			]
		}
	]';
    }
}