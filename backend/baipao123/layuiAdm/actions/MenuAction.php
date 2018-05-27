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
			"title": "首页",
			"icon": "&#xe68e;",
			"href": "/site/home",
			"spread": false
		},
        {
			"title": "轮播管理",
			"icon": "icon-icon10",
			"href": "",
			"spread": true,
			"children": [
				{
					"title": "轮播列表",
					"icon": "&#xe612;",
					"href": "/slider/list",
					"spread": false
				}
			]
		},
        {
			"title": "企业管理",
			"icon": "icon-icon10",
			"href": "",
			"spread": true,
			"children": [
                {
					"title": "企业列表",
					"icon": "&#xe612;",
					"href": "/company/list",
					"spread": false
				},
				{
					"title": "待审核列表",
					"icon": "&#xe612;",
					"href": "/company/verify-list",
					"spread": false
				}
			]
		},
		{
			"title": "账户管理",
			"icon": "icon-icon10",
			"href": "",
			"spread": true,
			"children": [
				{
					"title": "账户列表",
					"icon": "&#xe612;",
					"href": "/admin/list",
					"spread": false
				}
			]
		}
	]';
    }
}