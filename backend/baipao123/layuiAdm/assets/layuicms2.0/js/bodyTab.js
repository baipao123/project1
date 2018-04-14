/*
	@Author: 驊驊龔頾
	@Time: 2017-10
	@Tittle: bodyTab
	@Description: 点击对应按钮添加新窗口
*/
var liIndex,curNav,delMenu;
layui.define(["element","jquery"],function(exports){
	var element = layui.element,
		$ = layui.$,
        Tab = function () {
            this.tabConfig = {
                openTabNum: 10,  //最大可打开窗口数量
                tabFilter: "bodyTab",  //添加窗口的filter
                changeRefreshBool: false,//点击之后是否刷新页面
            }
        };
    Tab.prototype.getSessionStorage = function (name) {
        return window.sessionStorage.getItem(name);
    }

    Tab.prototype.setSessionStorage = function (name, value) {
        if (typeof value !== "string")
            value = JSON.stringify(value);
        return window.sessionStorage.setItem(name, value);
    }

    Tab.prototype.generateIconIHtml = function(icon){
        //layui-tab-i 用于标记本i是图标
        if (icon === undefined || icon === "")
            return "";
        if (icon.indexOf("icon-") !== -1)
            return '<i class="layui-tab-i seraph ' + icon + '" data-icon="' + icon + '"></i>';
        return '<i class="layui-tab-i layui-icon" data-icon="' + icon + '">' + icon + '</i>';
	}

	Tab.prototype.generateAHeadHtml = function(href,target){
        if (target === "_blank")
            return '<a data-url="' + href + '" target="' + target + '">';
        return '<a data-url="' + href + '">';
	}

    //生成左侧菜单
    Tab.prototype.navBar = function(data){
        var that = this;
        var ulHtml = '';
        $.each(data, function (i, val) {
            var liClass = i === 0 ? "layui-this" : "";
            if (val.spread || val.spread === undefined)
                ulHtml += '<li class="layui-nav-item layui-nav-itemed ' + liClass + '">';
            else
                ulHtml += '<li class="layui-nav-item ' + liClass + '">';
            //三级菜单
            if (val.children !== undefined && val.children.length > 0) {
                ulHtml += '<a>';
                ulHtml += that.generateIconIHtml(val.icon);
                ulHtml += '<cite>' + val.title + '</cite>';
                ulHtml += '<span class="layui-nav-more"></span>';
                ulHtml += '</a>';
                ulHtml += '<dl class="layui-nav-child">';
                $.each(val.children, function (j, item) {
                    ulHtml += '<dd>';
                    ulHtml += that.generateAHeadHtml(item.href, item.target);
                    ulHtml += that.generateIconIHtml(item.icon);
                    ulHtml += '<cite>' + item.title + '</cite></a></dd>';
                });
                ulHtml += "</dl>";
            } else {
                ulHtml += that.generateAHeadHtml(val.href, val.target);
                ulHtml += that.generateIconIHtml(val.icon);
                ulHtml += '<cite>' + val.title + '</cite></a>';
            }
            ulHtml += '</li>';
        });
        return ulHtml;
    }
	//获取二级菜单数据
	Tab.prototype.render = function(dataStr) {
		//显示左侧菜单
		var _this = this;
		$(".navBar ul").html(_this.navBar(dataStr)).height($(window).height()-210);
		element.init();  //初始化页面元素
		$(window).resize(function(){
			$(".navBar").height($(window).height()-210);
		})
	}

	//是否点击窗口切换刷新页面
    Tab.prototype.changeRegresh = function (index) {
        if (this.tabConfig.changeRefreshBool)
            $(".clildFrame .layui-tab-item").eq(index).find("iframe")[0].contentWindow.location.reload();
    }

	//参数设置
	Tab.prototype.set = function(option) {
		var _this = this;
		$.extend(true, _this.tabConfig, option);
		return _this;
	};

    Tab.prototype.getLayId = function (url) {
        var layId = 0;
        $(".layui-tab-title.top_tab li").each(function (i) {
            if ($(this).find("i.layui-tab-close").attr("data-url") == url)
                layId = $(this).attr("lay-id");
        });
        return layId;
    };
    Tab.prototype.hasTab = function (url) {
        return this.getLayId(url) !== 0;
    };
    Tab.prototype.getTabIndex = function (url) {
        var index = 0;
        $(".layui-tab-title.top_tab li").each(function (i) {
            if ($(this).find("i.layui-tab-close").attr("data-url") == url)
                index = i;
        });
        console.log(index);
        return index;
    };

	//右侧内容tab操作
	var tabIdIndex = 0;
	Tab.prototype.tabAdd = function(_this){
		var that = this;
		if(_this.attr("target") === "_blank"){
			window.open(_this.attr("data-url"));
		}else if(_this.attr("data-url") !== undefined){
            var icon = _this.find("i.layui-tab-i").length > 0 ? _this.find("i.layui-tab-i").attr("data-icon") : "";
		    that.tabAddiFrame(_this.find("cite").text(),icon,_this.attr("data-url"));
            // that.changeRegresh(_this.parent('.layui-nav-item').index());
		}
	}

    Tab.prototype.tabAddiFrame = function (title, icon, url) {
        var openTabNum = this.tabConfig.openTabNum,
            curmenu = {
                "icon": icon,
                "title": title,
                "href": url
            },
            that = this,
            tabFilter = that.tabConfig.tabFilter;
        if ($(".layui-tab-title.top_tab li").length >= openTabNum) {
            layer.msg('只能同时打开' + openTabNum + '个选项卡哦。不然系统会卡的！');
            return;
        }
        if (that.hasTab(url)) {
            that.changeRegresh(that.getTabIndex(url));//是否需要刷新页面
            that.setSessionStorage("curmenu", curmenu);//当前的窗口
            element.tabChange(tabFilter, that.getLayId(url));
        } else {
            tabIdIndex++;
            var titleHtml = that.generateIconIHtml();
            titleHtml += '<cite>' + title + '</cite>';
            titleHtml += '<i class="layui-icon layui-unselect layui-tab-close" data-url="' + url.replace('"', '') + '" data-id="' + tabIdIndex + '">&#x1006;</i>';
            element.tabAdd(tabFilter, {
                title: titleHtml,
                content: "<iframe src='" + url + "' data-id='" + tabIdIndex + "'></frame>",
                id: new Date().getTime()
            });
            //当前窗口内容
            curmenu = {
                "icon": icon,
                "title": title,
                "href": url,
                "layId": new Date().getTime()
            };
            var menu = [];
            if (this.getSessionStorage("menu"))
                menu = JSON.parse(this.getSessionStorage("menu"));
            menu.push(curmenu);
            that.setSessionStorage("menu", menu);
            that.setSessionStorage("curmenu", curmenu);//当前的窗口
            element.tabChange(tabFilter, that.getLayId(url));
        }
    }

    //切换后获取当前窗口的内容
	$(document).on("click",".top_tab li",function(){
		var curmenu = '';
		var menu = JSON.parse(bodyTab.getSessionStorage("menu"));
        if(bodyTab.getSessionStorage("menu")) {
            curmenu = menu[$(this).index() - 1];
        }
        if ($(this).index() === 0) {
            bodyTab.setSessionStorage("curmenu", '');
        } else {
            bodyTab.setSessionStorage("curmenu", curmenu);
            if (bodyTab.getSessionStorage("curmenu") === "undefined") {
                //如果删除的不是当前选中的tab,则将curmenu设置成当前选中的tab
                if (curNav != JSON.stringify(delMenu)) {
                    bodyTab.setSessionStorage("curmenu", curNav);
                } else {
                    bodyTab.setSessionStorage("curmenu", menu[liIndex - 1]);
                }
            }
        }
		element.tabChange(bodyTab.tabConfig.tabFilter,$(this).attr("lay-id")).init();
        bodyTab.changeRegresh($(this).index());
		setTimeout(function(){
			bodyTab.tabMove();
		},100);
	})

	//删除tab
	$(document).on("click",".top_tab li i.layui-tab-close",function(){
		//删除tab后重置session中的menu和curmenu
		liIndex = $(this).parent("li").index();
		var menu = JSON.parse(bodyTab.getSessionStorage("menu"));
		if(menu !== null) {
            //获取被删除元素
            delMenu = menu[liIndex - 1];
            var curmenu = bodyTab.getSessionStorage("curmenu") === "undefined" ? undefined : bodyTab.getSessionStorage("curmenu") === "" ? '' : JSON.parse(bodyTab.getSessionStorage("curmenu"));
            if (JSON.stringify(curmenu) !== JSON.stringify(menu[liIndex - 1])) {  //如果删除的不是当前选中的tab
                // window.sessionStorage.setItem("curmenu",JSON.stringify(curmenu));
                curNav = JSON.stringify(curmenu);
            } else {
                if ($(this).parent("li").length > liIndex) {
                    bodyTab.setSessionStorage("curmenu", curmenu);
                    curNav = curmenu;
                } else {
                    bodyTab.setSessionStorage("curmenu", menu[liIndex - 1]);
                    curNav = JSON.stringify(menu[liIndex - 1]);
                }
            }
            menu.splice((liIndex - 1), 1);
            bodyTab.setSessionStorage("menu", menu);
        }
		element.tabDelete("bodyTab",$(this).parent("li").attr("lay-id")).init();
		bodyTab.tabMove();
	})

	//刷新当前
    $(".refresh").on("click", function () {  //此处添加禁止连续点击刷新一是为了降低服务器压力，另外一个就是为了防止超快点击造成chrome本身的一些js文件的报错(不过貌似这个问题还是存在，不过概率小了很多)
        if ($(this).hasClass("refreshThis")) {
            $(this).removeClass("refreshThis");
            $(".clildFrame .layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload();
            setTimeout(function () {
                $(".refresh").addClass("refreshThis");
            }, 2000)
        } else {
            layer.msg("您点击的速度超过了服务器的响应速度，还是等两秒再刷新吧！");
        }
    })

	//关闭其他
	$(".closePageOther").on("click",function(){
		if($("#top_tabs li").length>2 && $("#top_tabs li.layui-this cite").text()!="后台首页"){
			var menu = JSON.parse(bodyTab.getSessionStorage("menu"));
			$("#top_tabs li").each(function(){
				if($(this).attr("lay-id") != '' && !$(this).hasClass("layui-this")){
					element.tabDelete("bodyTab",$(this).attr("lay-id")).init();
					//此处将当前窗口重新获取放入session，避免一个个删除来回循环造成的不必要工作量
					for(var i=0;i<menu.length;i++){
						if($("#top_tabs li.layui-this cite").text() == menu[i].title){
							menu.splice(0,menu.length,menu[i]);
                            bodyTab.setSessionStorage("menu",menu);
						}
					}
				}
			})
		}else if($("#top_tabs li.layui-this cite").text()=="后台首页" && $("#top_tabs li").length>1){
			$("#top_tabs li").each(function(){
				if($(this).attr("lay-id") != '' && !$(this).hasClass("layui-this")){
					element.tabDelete("bodyTab",$(this).attr("lay-id")).init();
					window.sessionStorage.removeItem("menu");
					menu = [];
					window.sessionStorage.removeItem("curmenu");
				}
			})
		}else{
			layer.msg("没有可以关闭的窗口了@_@");
		}
		//渲染顶部窗口
		tab.tabMove();
	})
	//关闭全部
	$(".closePageAll").on("click",function(){
		if($("#top_tabs li").length > 1){
			$("#top_tabs li").each(function(){
				if($(this).attr("lay-id") != ''){
					element.tabDelete("bodyTab",$(this).attr("lay-id")).init();
					window.sessionStorage.removeItem("menu");
					menu = [];
					window.sessionStorage.removeItem("curmenu");
				}
			})
		}else{
			layer.msg("没有可以关闭的窗口了@_@");
		}
		//渲染顶部窗口
		tab.tabMove();
	})

	var bodyTab = new Tab();
	exports("bodyTab",function(option){
		return bodyTab.set(option);
	});
})
