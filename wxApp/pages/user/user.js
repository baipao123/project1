//index.js
//获取应用实例
const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {},
        company: {},
        isCompany: false,
        domain: app.globalData.qiNiuDomain
    },
    bindViewTap: function () {

    },
    onLoad: function () {
        let that = this
        that.setData({
            user: app.globalData.user,
            isCompany: app.globalData.user && app.globalData.user.type > 1
        })
        app.getCompanyInfo(function () {
            that.setData({
                company: app.globalData.company,
            })
        })
        if(app.globalData.user && app.globalData.user.type == 0){
            wx.navigateTo({
                url: "/pages/index/index"
            })
        }
    },
    goIndex:function (e) {
        let that = this
        if(that.data.isCompany){
            wx.navigateTo({
                url: "/pages/job/add"
            })
        }else{
            wx.switchTab({
                url: "/pages/index/home"
            })
        }
    }
})
