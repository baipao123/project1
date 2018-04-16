<?php

namespace layuiAdm\widgets;

use yii\data\Pagination;

class PagesWidget extends Widget
{
    /**
     * @var Pagination the pagination object that this pager is associated with.
     * You must set this property in order to make LinkPager work.
     */
    public $pagination;

    /**
     * Renders the widget.
     */
    public function run()
    {
        $pageCount = $this->pagination->pageCount;
        $currPage = $this->pagination->page + 1;
        $js = <<<JS
layui.laypage({
    cont: 'page',
    pages: {$pageCount},
    skip: true,
    curr: {$currPage},
    jump: function(obj, first){
        var queryParams = {};
        var queryString = location.search.replace('?','');
        queryString = decodeURI(queryString);
        if(queryString){
            queryString.split('&').forEach(function(i){
                var j = i.split('=');
                queryParams[j[0]]=j[1];
            });
        }
        queryParams['page'] = obj.curr;
        if(!first){
            location.href = location.pathname + '?' + $.param(queryParams) + location.hash;
        }
    }
});
JS;
        $this->getView()->registerJs($js);
        return '<div id="page"></div>';
    }
}
