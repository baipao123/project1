const app = getApp()
const request = require("./../../utils/request.js")
const qrCode = require("./../../utils/qrCode.js")

Page({
    data: {
        user: {},
        job: {},
        uJob: {},
        domain: app.globalData.qiNiuDomain,
        canvasWidth: 300,
    },
    onLoad: function (options) {
        let uJid = options && options.hasOwnProperty("id") ? options.id : 0,
            that = this
        that.setData({
            user: app.globalData.user
        })
        app.getCompanyInfo(function () {
            that.setData({
                company: app.globalData.company
            })
        })
        that.setCanvasSize()
        that.getUJob(uJid)
    },
    getUJob: function (uJid) {
        let that = this
        request.get("/part/job/my-job?uJid=" + uJid, {}, function (data) {
            console.log(data)
            that.setData({
                job: data.job,
                uJob: data.uJob
            })
            let uJob = data.uJob
            that.drawQr(uJob.id + ';' + uJob.jid + ';' + uJob.key + ';' + uJob.id);
        })
    },
    setCanvasSize: function () {
        try {
            let res = wx.getSystemInfoSync(),
                scale = 750 / 686, //不同屏幕下canvas的适配比例；设计稿是750宽
                width = res.windowWidth / scale
            this.setData({
                canvasWidth: width
            })
        } catch (e) {
            // Do something when catch error
            console.log("获取设备信息失败" + e);
        }
    },
    drawQr: function (text) {
        let that = this
        console.log(that.data.canvasWidth)
        qrCode.api.draw(text,"qrcode",that.data.canvasWidth,that.data.canvasWidth)
    },
})