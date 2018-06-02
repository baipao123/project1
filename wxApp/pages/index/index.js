const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {},
        domain: app.globalData.qiNiuDomain,
        sec: 6,
    },
    onLoad: function () {
        this.setData({
            user: app.globalData.user
        })
        wx.hideShareMenu()
    },
    onShow: function () {
        this.desc()
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
            app.turnPage("index/home")
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
        if (that.data.user.type && that.data.user.type > 0)
            app.turnPage("index/home")
        else if (type == 2)
            app.turnPage("chose/company")
        else
            app.turnPage("chose/user")
    }
})