//app.js
const request = require("./utils/request.js");

App({
    onLaunch: function () {
        this.login()
    },
    globalData: {
        user: {},
        qiNiuDomain: 'http://img.jiuyeli.org/',
        company: {},
        region: {
            cid: 0,
            aid: 0,
            cityStr: "",
            isSelect: false
        },
        systemInfo: null
    },
    login: function () {
        let _this = this;
        wx.login({
            success: res => {
                if (res.code) {
                    request.post("base/app-login", {code: res.code}, data => {
                        _this.globalData.user = data.user;
                        if(data.user.type>1)
                            _this.setCompanyStyle()
                        if (typeof(getCurrentPages) == "function" && getCurrentPages().length > 0)
                            getCurrentPages()[getCurrentPages().length - 1].onLoad();
                    })
                } else {
                    console.log('登录失败：' + res.errMsg)
                }
            },
            fail: res => {
                console.log(res);
            }
        })
    },
    setUserInfo: function (res, callBack) {
        let _this = this;
        request.post("base/app-user", res, function (data) {
            _this.globalData.user = data.user;
            if(data.user.type>1)
                _this.setCompanyStyle()
            if (typeof callBack == "function") {
                callBack();
            }
        })
    },
    setNavBarBackColor: function () {
        if (this.globalData.user && this.globalData.user.type > 1)
            this.setCompanyStyle()
    },
    setCompanyStyle: function () {
        wx.setNavigationBarColor({
            frontColor:"#ffffff",
            backgroundColor:"#4395FF"
        })
    },
    resetRegion: function () {
        this.globalData.region = {
            cid: 0,
            aid: 0,
            cityStr: "",
            isSelect: false
        }
    },
    isEmptyObj: function (obj) {
        // 其他空值
        if (!obj && obj !== 0 && obj !== '')
            return true;
        // 空Array
        if (Array.prototype.isPrototypeOf(obj) && obj.length === 0)
            return true;
        // 空Obj
        return Object.prototype.isPrototypeOf(obj) && Object.keys(obj).length === 0;
    },
    getCompanyInfo: function (callBack) {
        let that = this
        if (that.isEmptyObj(that.globalData.company)) {
            request.get("part/company/info", {}, function (data) {
                data.company.positionStr = ""
                that.globalData.company = data.company
                if (typeof callBack == "function") {
                    callBack();
                }
            })
        } else if (typeof callBack == "function") {
            callBack();
        }
    },
    getSystemInfo: function (callBack) {
        let that = this
        if (!that.globalData.systemInfo) {
            wx.getSystemInfo({
                success: function (res) {
                    that.globalData.systemInfo = res
                    console.log(res)
                    if (typeof callBack == "function") {
                        callBack(res);
                    }
                }
            })
        } else if (typeof callBack == "function") {
            console.log(that.globalData.systemInfo)
            callBack(that.globalData.systemInfo);
        }
    },
    toast: function (text, icon, callback) {
        icon = icon == undefined ? "success" : icon
        wx.showToast({
            title: text,
            icon: icon,
            complete: callback
        })
    },
    confirm: function (content, success, fail, title, confirmText, cancelText) {
        wx.showModal({
            title: title == undefined ? "提示" : title,
            content: content,
            success: res => {
                if (res.confirm) {
                    if (typeof success == "function")
                        success()
                } else if (res.cancel) {
                    if (typeof fail == "function")
                        fail()
                }
            },
            confirmText: confirmText == undefined ? "确定" : confirmText,
            cancelText: cancelText == undefined ? "取消" : cancelText,
        })
    },
    getLocation: function (success, fail, type) {
        let that = this
        that.authorize("scope.userLocation", function () {
            that.getLocationAction(success, fail, type)
        }, function () {
            that.toast("请允许使用地理位置", "none")
            if (typeof fail == "function")
                fail()
        })
    },
    getLocationAction: function (success, fail, type) {
        let that = this
        type = type == undefined ? "gcj02" : type
        wx.getLocation({
            type: type,
            success: function (res) {
                if (typeof success == "function")
                    success(res)
            },
            fail: function (res) {
                that.toast("请开启定位设置", "none")
                if (typeof fail == "function")
                    fail(res)
            }
        })
    },
    authorize: function (scopeName, success, fail) {
        wx.getSetting({
            success: (res) => {
                let setting = res.authSetting
                if (setting.hasOwnProperty(scopeName) && setting[scopeName]) {
                    if (typeof success == "function")
                        success()
                } else {
                    wx.authorize({
                        scope: scopeName,
                        success: success,
                        fail: fail
                    })
                }
            }
        })
    },
    setTitle: (title) => {
        wx.setNavigationBarTitle({
            title: title
        })
    },
    turnPage: function (url) {
        if (!url)
            return false
        if (url == "index/home" || url == "user/user") {
            wx.switchTab({
                url: "/pages/" + url
            })
        } else
            wx.navigateTo({
                url: "/pages/" + url
            })
    }
})