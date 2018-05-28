//index.js
//获取应用实例
const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {},
        company: {},
        isCompany: false,
        domain: app.globalData.qiNiuDomain,
        verifyNum: 0,
        uJid: -1,
        lastJid: -1,
    },
    onShow: function () {
        let that = this
        request.get("part/user/user-status", {}, function (res) {
            that.setData({
                verifyNum: res.verifyNum,
                uJid: res.uJid,
                lastJid: res.lastJid
            })
        })
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
        if (app.globalData.user && app.globalData.user.type == 0) {
            wx.navigateTo({
                url: "/pages/index/index"
            })
        }
        wx.hideShareMenu()
    },
    goIndex: function (e) {
        let that = this
        if (that.data.isCompany) {
            wx.navigateTo({
                url: "/pages/job/add"
            })
        } else {
            wx.switchTab({
                url: "/pages/index/home"
            })
        }
    },
    goClock: function (e) {
        let that = this,
            uJid = that.data.uJid
        if (uJid == -1) {
            app.toast("您还没有工作，无需打卡", "none")
            return false
        } else {
            wx.navigateTo({
                url: uJid == 0 ? "/pages/job/userList?status=1" : "/pages/job/getInfo?id=" + uJid
            })
        }
    },
    goUsers: function (e) {
        let that = this,
            jid = that.data.lastJid
        if (jid == -1) {
            app.toast("您还没有发布招聘岗位", "none")
            return false
        } else {
            wx.navigateTo({
                url: jid == 0 ? "/pages/company/jobs" : "/pages/company/users?jid=" + jid
            })
        }
    }
})
