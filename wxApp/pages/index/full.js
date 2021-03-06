const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {},
        isCompany: false,
        domain: app.globalData.qiNiuDomain,
        jobs: [],
        slider: [],
        windowWidth: 375,
        page: 1,
        searchData: {},
        inputShowed: false,
        empty: false,
        loading: false,
        refresh: false
    },
    onLoad: function () {
        let that = this
        app.setNavBarBackColor()
        that.setData({
            user: app.globalData.user,
            isCompany: app.globalData.user && app.globalData.user.type > 1,
            searchData: {
                aid: app.globalData.user.area_id,
                cid: app.globalData.user.city_id
            }
        })
        app.getSystemInfo(function (res) {
            that.setData({
                windowWidth: res.hasOwnProperty("windowWidth") ? res.windowWidth : 375
            })
        })
        that.getSlider()
    },
    onShow: function () {
        if (app.globalData.user) {
            if (app.globalData.user.type == 0)
                app.turnPage("index/index")
            else if (app.globalData.user.type > 1)
                this.setData({
                    isCompany: true
                })
        }
        let region = app.globalData.region,
            that = this
        console.log(region)
        if (region.isSelect) {
            that.setData({
                "user.city_id": region.cid,
                "user.area_id": region.aid,
                "user.cityStr": region.cityStr
            })
            app.resetRegion()
            that.data.searchData.cid = region.cid
            that.data.searchData.aid = region.aid
            that.getList(1, true)
        }
        if (that.data.jobs.length == 0)
            that.getList(1, true)
    },
    getList: function (page, refresh) {
        let that = this,
            list = that.data.jobs,
            searchData = that.data.searchData
        refresh = refresh === undefined ? false : refresh
        page = page === undefined ? that.data.page : page
        that.setData({
            loading:true
        })
        that.data.loading = true
        that.data.refresh = !!refresh
        searchData.page = page
        searchData.type = 1
        request.get("part/job/list", searchData, function (data) {
            console.log(data)
            if (!refresh && that.data.refresh)
                return false
            if (refresh) {
                list = data.list
                that.data.page = 1
            }
            else {
                for (let i = 0; i < data.list.length; i++)
                    list.push(data.list[i])
            }
            that.setData({
                jobs: list,
                empty: data.list.length == 0,
                loading: false,
                refresh: false
            })
            that.data.page++
            if (refresh)
                wx.stopPullDownRefresh()
        })
    },
    selectCity: function (e) {
        let that = this,
            cid = that.data.user.city_id,
            aid = that.data.user.area_id
        wx.navigateTo({
            url: "/pages/districtSelect/districtSelect?aid=" + aid + "&cid=" + cid
        })
    },
    showInput: function () {
        this.setData({
            inputShowed: true
        });
    },
    hideInput: function () {
        this.setData({
            "searchData.text": "",
            inputShowed: false
        });
    },
    clearInput: function () {
        this.setData({
            "searchData.text": ""
        });
    },
    inputTyping: function (e) {
        this.setData({
            "searchData.text": e.detail.value
        });
        this.data.searchData.text = e.detail.value
        this.getList(1, true)
    },
    onReachBottom: function () {
        if (this.data.empty || this.data.loading)
            return true;
        let that = this
        that.getList(that.data.page, false)
    },
    onPullDownRefresh: function () {
        let that = this
        if (that.data.refresh)
            return true
        that.getList(1, true)
        that.getSlider()
    },
    getSlider: function () {
        let that = this
        if (that.data.user.type && that.data.user.type > 1)
            return false
        request.get("part/slider/index", {}, function (res) {
            that.setData({
                slider: res.list
            })
        })
    },
    sliderTap: function (e) {
        let that = this,
            index = e.currentTarget.dataset.index,
            slider = that.data.slider[index]
        switch (slider.type) {
            case 0:
                break;
            case 1:
                wx.navigateTo({
                    url: "/pages/job/info?id=" + slider.tid
                })
                break;
            case 2:
                wx.navigateTo({
                    url: slider.link
                })
                break;
            case 3:
                wx.navigateTo({
                    url: "/pages/common/webView?url=" + slider.link
                })
                break;
            default:
                break;
        }
    },
    turnAddJob:function () {
        app.turnPage("job/add")
    }
})