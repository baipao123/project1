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
        app.setUserInfo(e.detail);
        this.setData({
            user: app.globalData.user
        })
    }
})