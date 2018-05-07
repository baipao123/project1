const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {},
    },
    onLoad: function () {
        this.setData({
            user: app.globalData.user
        });
    },
    getUserInfo: function (e) {
        let that = this
        app.setUserInfo(e.detail,function () {
            that.setData({
                user: app.globalData.user
            })
        });
    }
})