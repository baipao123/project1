const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
    },
    onLoad: function () {
        if(app.globalData.user && app.globalData.user.type > 1)
            app.setCompanyStyle()
        wx.hideShareMenu()
    }
})