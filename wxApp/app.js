//app.js
const request = require("./utils/request.js");

App({
    onLaunch: function () {
        // 展示本地存储能力
        // var logs = wx.getStorageSync('logs') || []
        // logs.unshift(Date.now())
        // wx.setStorageSync('logs', logs)
        this.login()
    },
    globalData: {
        user:{},
        domain:"https://demo.wx-dk.cn/"
    },
    login: function(){
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
    setUserInfo: function(res) {
        let _this = this;
        request.post("base/app-user", res, function (data) {
            _this.globalData.user = data.user;
            console.log(data.user);
        })
    }
})