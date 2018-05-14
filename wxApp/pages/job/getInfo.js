const app = getApp()
const request = require("./../../utils/request.js")
const qrCode = require("./../../utils/qrCode.js")

Page({
    data: {
        user: {},
        job: {},
        clocks: {},
        todayClock: [],
        uJob: null,
        domain: app.globalData.qiNiuDomain,
        canvasWidth: 300,
        isAjax: true,
        nowTime: "",
        lastClock: 0
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
        that.getNowTime()
        that.getUJob(uJid)
    },
    getUJob: function (uJid) {
        let that = this
        request.get("/part/job/my-job?uJid=" + uJid, {}, function (data) {
            console.log(data)
            let clocks = data.clocks,
                todayClock = clocks.length > 0 ? clocks[clocks.length - 1] : []
            console.log(todayClock)
            if (todayClock.length > 0) {
                let tmp = todayClock[0]
                console.log(tmp)
                if (!tmp.isToday)
                    todayClock = []
            }
            that.setData({
                job: data.job,
                uJob: data.uJob,
                isAjax: false,
                clocks: clocks,
                todayClock: todayClock
            })
            let uJob = data.uJob
            that.drawQr(uJob.id + ';' + uJob.jid + ';' + uJob.key + ';' + uJob.id);
        }, function () {

        }, function () {
            that.setData({
                isAjax: false
            })
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
        qrCode.api.draw(text, "qrcode", that.data.canvasWidth, that.data.canvasWidth)
    },
    getNowTime: function () {
        let that = this,
            date = new Date(),
            hour = "00" + date.getHours(),
            minute = "00" + date.getMinutes(),
            second = date.getSeconds(),
            lastClock = that.data.lastClock
        if (lastClock > 0)
            lastClock--;
        that.setData({
            nowTime: hour.substr(-2) + ":" + minute.substr(-2),
            lastClock: lastClock
        })
        setTimeout(this.getNowTime, (60 - second) * 1000)
    },
    clock: function () {
        let that = this,
            data = {
                uJid: that.data.uJob.id
            }
        if (that.data.lastClock > 0) {
            wx.showToast({
                icon: "none",
                title: "2次打卡最少间隔5分钟"
            })
            return false
        }
        that.data.lastClock = 5
        wx.getLocation({
            type: "gcj02",
            success: function (res) {
                data.lat = res.latitude
                data.long = res.longitude
                data.acc = res.accuracy
                request.post("part/clock/clock", data, function (data) {
                    wx.showToast({
                        icon: "success",
                        title: "打卡成功"
                    })
                    let today = that.data.todayClock
                    today.push(data.info)
                    that.setData({
                        todayClock: today
                    })
                },function () {
                    that.data.lastClock = 0
                })
            },
            fail: function (res) {
                that.data.lastClock = 0
                wx.showToast({
                    icon: "none",
                    title: "请开启定位设置"
                })
            }
        })

    },
})