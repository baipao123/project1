const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        status: 0,
        user: {},
        company: {},
        domain: app.globalData.qiNiuDomain,
        jobs: [],
        windowHeight: 500,
        page: 1,
        empty: false,
        loading: false
    },
    onLoad: function (options) {
        let that = this
        that.setData({
            user: app.globalData.user,
            status: options && options.hasOwnProperty("status") ? options.status : 0
        })
        app.setTitle(app.globalData.user.type && app.globalData.user.type > 1 ? "我的招聘" : "我的兼职")
        app.getCompanyInfo(function () {
            that.setData({
                company: app.globalData.company
            })
        })
        app.getSystemInfo(function (res) {
            that.setData({
                windowHeight: res.hasOwnProperty("windowHeight") ? res.windowHeight : 500
            })
        })
        that.getList(1)
    },
    getList: function (page, refresh) {
        this.data.loading = true
        refresh = refresh === undefined ? false : refresh
        page = page === undefined ? 1 : page
        let that = this,
            list = that.data.jobs
        request.get("part/user/jobs?page=" + page + "&status=" + that.data.status, {}, function (data) {
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
                loading: false
            })
            that.data.page++
        })
    },
    moreList: function () {
        if (this.data.empty || this.data.loading)
            return true;
        let that = this
        that.getList(that.data.page, false)
    }

})