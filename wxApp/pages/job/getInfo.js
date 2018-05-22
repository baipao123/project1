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
        },
        showIndex: -1,
        isPrompt: false,
        prompt: {
            date: '',
            type: 0,
            num: '',
            msg: '',
            index: 0
        },
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
            that.setData({
                job: data.job,
                uJob: data.uJob,
                isAjax: false,
                todayClock: data.clocks
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
    getDaily: function (uJid) {
        let that = this
        request.get("part/clock/job-daily?uJid=" + uJid, {}, function (data) {
            that.setData({
                clocks: data.clocks
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
        this.setData({
            "navBar.sliderOffset": e.currentTarget.offsetLeft,
            "navBar.activeIndex": e.currentTarget.id
        });
        if (e.currentTarget.id == 1)
            this.getDaily(this.data.uJob.id)
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
        if (this.data.uJob.status != 2)
            return false;
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
            request.post("part/clock/clock", data, function (res) {
                app.toast("打卡成功")
                let today = that.data.todayClock,
                    clocks = that.data.clocks
                today.items.push(res.info)
                clocks[clocks.length - 1].items.push(res.info)
                that.setData({
                    todayClock: today
                })
                if (res.info.type == 3 && clocks[clocks.length - 1].status == 0) {
                    wx.showModal({
                        title: '上报工时',
                        content: '每天只统计一次工时，审核通过后无法修改，请确认今天的工作已经完成！',
                        cancelText: '还有工作',
                        confirmText: '上报工时',
                        success: function () {
                            that.goTimeUp(null, that.data.clocks.length - 1)
                        }
                    })
                }
            }, function () {
                that.data.lastClock = tmpClock
            })
        }, function () {
            that.data.lastClock = tmpClock
        })
    },
    changeTab: function (e) {
        let that = this,
            id = e.currentTarget.dataset.id
        that.setData({
            showIndex: id == that.data.showIndex ? -1 : id
        })
    },
    goTimeUp: function (e, index) {
        if (e)
            index = e.currentTarget.dataset.index
        let that = this,
            day = that.data.clocks[index]
        that.setData({
            isPrompt: true,
            prompt: {
                date: day.date,
                type: day.type ? day.type : 0,
                num: day.num,
                msg: '',
                index: index
            }
        })
    },
    promptTypeChange: function (e) {
        let that = this
        console.log(e)
        that.setData({
            "prompt.type": e.detail.value
        })
    },
    cancelPrompt: function () {
        let that = this
        that.setData({
            isPrompt: false
        })
    },
    submitPrompt: function (e) {
        let that = this,
            data = e.detail.value,
            clocks = that.data.clocks,
            index = that.data.prompt.index
        data.formId = e.detail.formId
        data.uJid = that.data.uJob.id
        request.post("part/job/time-up", data, function (res) {
            app.toast("上报成功")
            clocks[index].type = res.info.type
            clocks[index].num = res.info.num
            clocks[index].status = res.info.status
            that.setData({
                clocks: clocks,
                isPrompt: false
            })
        })
    }
})