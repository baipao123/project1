const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        status: 0,
        isFollow: false,
        user: {},
        company: {},
        domain: app.globalData.qiNiuDomain,
        jobs: [],
        page: 1,
        empty: false,
        loading: false,
        refresh: false
    },
    onLoad: function (options) {
        let that = this
        that.setData({
            user: app.globalData.user,
            status: options && options.hasOwnProperty("status") ? options.status : 0,
            isFollow: !!(options && options.hasOwnProperty("isFollow") && options.isFollow == 1)
        })
        app.setTitle(app.globalData.user.type && app.globalData.user.type > 1 ? "我的招聘" : "我的兼职")
        app.getCompanyInfo(function () {
            that.setData({
                company: app.globalData.company
            })
        })
        that.getList(1, true)
    },
    getList: function (page, refresh) {
        let that = this, url,
            list = that.data.jobs
        refresh = refresh === undefined ? false : refresh
        page = page === undefined ? 1 : page
        that.data.loading = true
        that.data.refresh = !!refresh
        url = that.data.isFollow ? "part/user/follow-jobs" : "part/user/jobs"
        request.get(url, {page: page, status: that.data.status}, function (data) {
            console.log(data)
            if (!refresh && that.data.refresh)
                return false
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
                refresh: false,
            })
            that.data.page++
            if (refresh)
                wx.stopPullDownRefresh()

        })
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
    }
})