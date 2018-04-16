<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/14
 * Time: 下午5:27
 */

namespace layuiAdm\actions;

use Yii;


class MenuAction extends ApiAction
{
    public $module;

    public function run() {
        echo '[
		{
			"title": "文章列表",
			"icon": "icon-text",
			"href": "/site/error-jj",
			"spread": false
		},
		{
			"title": "图片管理",
			"icon": "&#xe634;",
			"href": "#2",
			"spread": false
		},
		{
			"title": "账户管理",
			"icon": "&#xe630;",
			"href": "",
			"spread": true,
			"children": [
				{
					"title": "账户列表",
					"icon": "&#xe61c;",
					"href": "/admin/list",
					"spread": false
				}
			]
		}
	]';
    }
}