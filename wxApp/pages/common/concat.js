const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        domain:''
    },
    onLoad: function () {
        this.setData({
            domain:app.globalData.qiNiuDomain
        })
        if (app.globalData.user && app.globalData.user.type > 1)
            app.setCompanyStyle()
        wx.hideShareMenu()
    },
    call: function (e) {
        let phone = e.currentTarget.dataset.phone
        wx.makePhoneCall({
            phoneNumber: phone
        })
    }
})