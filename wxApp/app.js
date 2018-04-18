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
        domain:"https://demo.localhost.com/"
    },
    login: isInfo => {
        var _this = this;
        wx.login({
            success: res => {
                if (res.code) {
                    wx.request({
                        url: _this.globalData.domain + "base/app-login",
                        data: {code: res.code},
                        method: "POST",
                        success: data => {
                            if(data.code == 0){
                                _this.setData({userType: data.data.userType});
                                if (isInfo || data.data.needUserInfo)
                                    _this.setUserInfo();
                            }else{
                                wx.showToast({
                                    title: data.msg,
                                    icon: 'none'
                                })
                            }
                        }
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
    setUserInfo: ()=> {
        var _this = this;
        wx.authorize({
            scope: 'scope.userInfo',
            success () {
                wx.getUserInfo({
                    success: res=> {
                        _this.setData({userInfo: res.userInfo})
                        wx.request({
                            url: _this.globalData.domain + "base/app-user",
                            data: res,
                            method: "POST",
                            success: data=> {

                            }
                        })
                        if (this.userInfoReadyCallback)
                            this.userInfoReadyCallback(res)
                    }
                })
            }
        })
    }
})