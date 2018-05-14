const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {}
    },
    onLoad: function () {
        let that = this
        that.setData({
            user: app.globalData.user
        })
        app.getCompanyInfo(function () {
            that.setData({
                company: app.globalData.company
            })
        })
    }
})