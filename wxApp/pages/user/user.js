//index.js
//获取应用实例
const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {},
        company: {},
        isUser: false,
        domain:app.globalData.qiNiuDomain
    },
    bindViewTap: function () {

    },
    onLoad: function () {
        let that = this
        that.setData({
            user: app.globalData.user,
            isUser: app.globalData.user && app.globalData.user.type <= 1
        })
        app.getCompanyInfo(function () {
            that.setData({
                company: app.globalData.company
            })
        })
    },
})
