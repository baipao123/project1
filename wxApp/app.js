//app.js
const request = require("./utils/request.js");

App({
    onLaunch: function () {
        // 展示本地存储能力
        // var logs = wx.getStorageSync('logs') || []
        // logs.unshift(Date.now())
        // wx.setStorageSync('logs', logs)
        this.login(false)
    },
    globalData: {
        userInfo: null,
        userType: -1,
        domain:"https://demo.wx-dk.cn/"
    },
    login: function(isInfo){
        let _this = this;
        wx.login({
            success: res => {
                if (res.code) {
                    request.post("base/app-login", {code: res.code}, data => {
                        _this.globalData.userType = data.userType;
                        if (isInfo || data.needUserInfo)
                            _this.setUserInfo();
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
    setUserInfo: function() {
        let _this = this;
        wx.authorize({
            scope: 'scope.userInfo',
            success () {
                wx.getUserInfo({
                    withCredentials: true,
                    lang: "zh_CN",
                    success: function (res) {
                        request.post("base/app-user", res, function (data) {
                            _this.globalData.userInfo = res.userInfo;
                        })
                        if (this.userInfoReadyCallback)
                            this.userInfoReadyCallback(res)
                    }
                })
            }
        })
    }
})