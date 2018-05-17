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
            if (typeof callBack == "function") {
                callBack();
            }
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
                    if (typeof callBack == "function") {
                        callBack(res);
                    }
                }
            })
        } else if (typeof callBack == "function") {
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
    getLocation: function (success, fail, type) {
        let that = this
        wx.getSetting({
            success: function (res) {
                let setting = res.authSetting
                if (setting.hasOwnProperty("scope.userLocation") && setting["scope.userLocation"])
                    that.getLocationAction(success, fail, type)
                else {
                    wx.authorize({
                        scope: 'scope.userLocation',
                        success: function (res) {
                            console.log(res)
                            that.getLocationAction(success, fail, type)
                        },
                        fail: function (res) {
                            that.toast("请允许使用地理位置", "none")
                            console.log(res)
                            if (typeof fail == "function")
                                fail()
                        }
                    })
                }
            }
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
    }
})