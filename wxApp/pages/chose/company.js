const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {}
    },
    onLoad: function () {
        this.setData({
            user: app.globalData.userInfo
        });
    }
})