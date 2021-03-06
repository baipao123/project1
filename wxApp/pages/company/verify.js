const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        list: [],
        jid:0,
        page: 1,
        loading: false,
        refresh: false,
        empty: false,
        clockIndex: -1,
        map: {
            index: -1,
            lat: '',
            long: ''
        },
        isPrompt: false,
        prompt: {}
    },
    onLoad: function (options) {
        let that = this
        if(options && options.hasOwnProperty("jid"))
            that.data.jid = options.jid
        that.getList(true)
        wx.hideShareMenu()
        app.setCompanyStyle()
    },
    getList: function (refresh) {
        let that = this,
            page = refresh ? 1 : that.data.page,
            jid = that.data.jid
        that.data.loading = true
        that.setData({
            loading:true
        })
        if (refresh)
            that.data.refresh = true
        request.get("part/company/time-verify-list", {page: page, jid: jid}, function (data) {
            let list = that.data.list
            if (refresh) {
                list = data.list
                that.data.page = 1
            } else {
                for (let i = 0; i < data.list.length; i++)
                    list.push(data.list[i])
            }
            that.data.page++
            that.setData({
                list: list,
                loading: false,
                refresh: false,
                empty: data.list.length == 0
            })
            if (refresh)
                wx.stopPullDownRefresh()
        })
    },
    seeClocks: function (e) {
        let that = this,
            index = e.currentTarget.dataset.id
        that.setData({
            clockIndex: that.data.clockIndex == index ? -1 : index
        })
    },
    showMap: function (e) {
        let that = this,
            index = e.currentTarget.dataset.index,
            cIndex = e.currentTarget.dataset.cIndex,
            clock = that.data.list[index].clocks[cIndex]
        that.setData({
            map: {
                index: cIndex,
                lat: clock.latitude,
                long: clock.longitude
            }
        })
    },
    fullMap: function (e) {
        let that = this,
            lat,
            long
        wx.openLocation({
            latitude: lat,
            longitude: long,
        })
    },
    pass: function (e) {
        let that = this,
            index = e.detail.value.index,
            data = {
                did: that.data.list[index].info.id,
                formId: e.detail.formId
            }
        wx.showModal({
            title: '确认通过?',
            content: '一经通过无法修改，请确认当天工时是否正确',
            cancelText: '我再想想',
            success: function (res) {
                if (res.confirm) {
                    request.post("part/job/time-pass", data, function (res) {
                        let list = that.data.list
                        list[index].info.status = 3
                        that.setData({
                            list: list
                        })
                    })
                }
            }
        })
    },
    submitPrompt: function (e) {
        let that = this,
            data = e.detail.value,
            index = that.data.prompt.index
        data.formId = e.detail.formId
        request.post("part/job/time-refuse", data, function (res) {
            let list = that.data.list
            list[index].info.status = 2
            that.setData({
                list: list,
                isPrompt: false
            })
        })
    },
    goPrompt: function (e) {
        let that = this,
            index = e.detail.value.index,
            item = that.data.list[index]
        that.setData({
            isPrompt: true,
            prompt: {
                index: index,
                did: item.info.id,
                msg: '',
                date: item.info.date,
                name: item.user.name
            }
        })
    },
    cancelPrompt: function () {
        this.setData({
            isPrompt: false
        })
    },
    onReachBottom: function (e) {
        if (this.data.empty || this.data.loading)
            return true;
        let that = this
        that.getList(false)
    },
    onPullDownRefresh: function (e) {
        if (this.data.refresh)
            return true;
        let that = this
        that.getList(true)
    }
})