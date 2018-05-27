const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {},
        domain: app.globalData.qiNiuDomain,
        sec: 555,
    },
    onLoad: function () {
        this.setData({
            user: app.globalData.user
        })
        this.desc()
        wx.hideShareMenu()
    },
    getUserInfo: function (e) {
        let that = this
        app.setUserInfo(e.detail, function () {
            that.setData({
                user: app.globalData.user
            })
        });
    },
    desc: function () {
        let that = this,
            sec = that.data.sec
        if (sec <= 0 && that.data.user.type > 0) {
            wx.switchTab({
                url: "/pages/index/home"
            })
            return false;
        }
        that.setData({
            sec: --sec
        })
        setTimeout(that.desc, 1000)
    },
    go: function (e) {
        let that = this,
            type = e ? e.currentTarget.dataset.type : 0
        if (that.data.user.type && that.data.user.type > 0) {
            wx.switchTab({
                url: "/pages/index/home"
            })
        } else if (type == 2) {
            wx.navigateTo({
                url: "/pages/chose/company"
            })
        } else
            wx.navigateTo({
                url: "/pages/chose/user"
            })
    },
    preventDefault:function (e) {
        e.preventDefault()
    }
})