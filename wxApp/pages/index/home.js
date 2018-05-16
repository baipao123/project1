const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {},
        domain: app.globalData.qiNiuDomain,
        jobs: [],
        page: 1,
        searchData: {},
        inputShowed: false,
        widowHeight: 500,
        empty: false,
        loading: false,
        refresh: false,
        scrollTop:0
    },
    onLoad: function () {
        let that = this
        that.setData({
            user: app.globalData.user,
            widowHeight: app.getSystemInfo("widowHeight", 500),
            searchData: {
                aid: app.globalData.user.area_id,
                cid: app.globalData.user.city_id
            }
        })
        that.getList(1, true)
    },
    onShow: function () {
        if (app.globalData.user != {} && app.globalData.user.type == 0)
            wx.navigateTo({
                url: "/pages/index/index"
            })
        let region = app.globalData.region,
            that = this
        console.log(region)
        if (region.isSelect) {
            this.setData({
                "user.city_id": region.cid,
                "user.area_id": region.aid,
                "user.cityStr": region.cityStr
            })
            app.resetRegion()
            that.data.searchData.cid = region.cid
            that.data.searchData.aid = region.aid
            that.getList(1, true)
        }
    },
    getList: function (page, refresh) {
        if(refresh){
            this.data.refresh = true
            page = 1
        }
        this.data.loading = true
        let that = this,
            list = that.data.jobs,
            searchData = that.data.searchData
        refresh = refresh === undefined ? false : refresh
        page = page === undefined ? that.data.page : page
        searchData.page = page
        request.get("/part/job/list", searchData, function (data) {
            console.log(data)
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
    moreList: function () {
        if (this.data.empty || this.data.loading || this.data.refresh)
            return true;
        let that = this
        that.getList(that.data.page, false)
    },
    refreshList: function () {
        if (this.data.loading || this.data.refresh)
            return true;
        let that = this
        that.goTop()
        that.getList(1, true)
    },
    goTop: function(){
        this.setData({
            scrollTop:0
        })
    },
})