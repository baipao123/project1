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
        lastClock: 0,
        navBar: {
            activeIndex: 0,
            sliderOffset: 0,
            sliderLeft: 0,
            sliderWidth: 96
        }
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
        that.setNavBarWidth()
        that.getNowTime()
        that.getUJob(uJid)
    },
    getUJob: function (uJid) {
        let that = this
        request.get("part/job/my-job?uJid=" + uJid, {}, function (data) {
            console.log(data)
            let clocks = data.clocks,
                todayClock = clocks.length > 0 ? clocks[clocks.length - 1] : {items: []}
            console.log(todayClock)
            if (todayClock.items.length > 0) {
                let tmp = todayClock.items[0]
                console.log(tmp)
                if (!tmp.isToday)
                    todayClock = {items: []}
            }
            that.setData({
                job: data.job,
                uJob: data.uJob,
                isAjax: false,
                clocks: clocks,
                todayClock: todayClock
            })
            let uJob = data.uJob
            that.drawQr(uJob.id + ';' + uJob.jid + ';' + uJob.key + ';' + uJob.uid);
        }, function () {

        }, function () {
            that.setData({
                isAjax: false
            })
        })
    },
    setCanvasSize: function () {
        let that = this
        app.getSystemInfo(function (res) {
            let width = res.hasOwnProperty("windowWidth") ? res.windowWidth : 300
            that.setData({
                canvasWidth: width * 686 / 750
            })
        })
    },
    drawQr: function (text) {
        let that = this
        console.log(that.data.canvasWidth)
        qrCode.api.draw(text, "qrcode", that.data.canvasWidth, that.data.canvasWidth)
    },
    setNavBarWidth: function () {
        let that = this
        app.getSystemInfo(function (res) {
            let width = res.hasOwnProperty("windowWidth") ? res.windowWidth : 300,
                sliderWidth = width / 4
            console.log(width)
            that.setData({
                "navBar.sliderLeft": (width / 3 - sliderWidth) / 2,
                "navBar.sliderOffset": width / 3 * that.data.navBar.activeIndex,
                "navBar.sliderWidth": sliderWidth
            });
        })
    },
    tabClick: function (e) {
        console.log(e)

        this.setData({
            "navBar.sliderOffset": e.currentTarget.offsetLeft,
            "navBar.activeIndex": e.currentTarget.id
        });
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
            },
            tmpClock = that.data.lastClock
        if (tmpClock > 0) {
            app.toast("2次打卡最少间隔5分钟", "none")
            return false
        }
        that.data.lastClock = 5
        app.getLocation(function (res) {
            data.lat = res.latitude
            data.long = res.longitude
            data.acc = res.accuracy
            request.post("part/clock/clock", data, function (data) {
                app.toast("打卡成功")
                let today = that.data.todayClock
                today.items.push(data.info)
                that.setData({
                    todayClock: today
                })
            }, function () {
                that.data.lastClock = tmpClock
            })
        }, function () {
            that.data.lastClock = tmpClock
        })
    },
})