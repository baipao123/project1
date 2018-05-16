const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {},
        company: {},
        domain: app.globalData.qiNiuDomain,
        jobs: [],
        widowHeight: 500,
        page: 1,
        empty: false,
        loading: false,
        refresh: false
    },
    onLoad: function () {
        let that = this
        that.setData({
            user: app.globalData.user,
            widowHeight: app.getSystemInfo("widowHeight", 500)
        })
        app.getCompanyInfo(function () {
            that.setData({
                company: app.globalData.company
            })
        })
        that.getList(1)
    },
    getList: function (page, refresh) {
        if (refresh) {
            this.data.refresh = true
            page = 1
        }
        this.data.loading = true
        refresh = refresh === undefined ? false : refresh
        page = page === undefined ? 1 : page
        let that = this,
            list = that.data.jobs
        request.get("/part/user/jobs?page=" + page, {}, function (data) {
            console.log(data)
            if (refresh) {
                list = data.list
                that.data.page = 1
            } else {
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
    moreList: function () {
        if (this.data.empty || this.data.loading || this.data.refresh)
            return true;
        let that = this
        that.getList(that.data.page, false)
    },
    refreshList: function () {
        if (this.data.empty || this.data.loading || this.data.refresh)
            return true;
        let that = this
        that.getList(1, true)
    },
    onReachBottom:function () {
        this.moreList()
    }

})