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
            request.get("/part/company/info", {}, function (data) {
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
    getSystemInfo: function (key, defaultValue) {
        let that = this,
            info
        if (that.globalData.systemInfo)
            info = that.globalData.systemInfo
        else {
            info = wx.getSystemInfoSync()
            that.globalData.systemInfo = info
            console.log(info)
        }
        console.log(info)
        if (key == undefined)
            return info
        if (info.hasOwnProperty(key))
            return info[key]
        return defaultValue == undefined ? "" : defaultValue
    },
    toast: function (text, icon) {
        icon = icon == undefined ? "success" : icon
        wx.showToast({
            title: text,
            icon: icon
        })
    }
})