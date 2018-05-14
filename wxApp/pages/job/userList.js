const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {},
        company: {},
        domain: app.globalData.qiNiuDomain,
        jobs: [],
        page: 1
    },
    onLoad: function () {
        let that = this
        that.setData({
            user: app.globalData.user
        })
        app.getCompanyInfo(function () {
            that.setData({
                company: app.globalData.company
            })
        })
        that.getList(1)
    },
    getList: function (page, refresh) {
        refresh = refresh === undefined ? false : refresh
        page = page === undefined ? 1 : page
        let that = this,
            list = that.data.jobs
        request.get("/part/user/jobs?page=" + page, {}, function (data) {
            console.log(data)
            for (let i = 0; i < data.list.length; i++)
                list.push(data.list[i])
            that.setData({
                jobs: list
            })
        })
    },
})