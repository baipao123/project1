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
    },
    preview: function (e) {
        let that = this,
            url = e.currentTarget.dataset.url
        wx.previewImage({
            current: url, // 当前显示图片的http链接
            urls: [url] // 需要预览的图片http链接列表
        })
    }
})