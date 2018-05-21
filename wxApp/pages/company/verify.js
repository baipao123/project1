const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {},
        company: {},
        list: {},
        page: 1,
        loading: false,
        refresh: false,
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
        that.getList(true)
    },
    getList: function (refresh) {
        let that = this,
            page = refresh ? 1 : that.data.page
        if (that.data.loading || that.data.refresh)
            return false;
        that.data.loading = true
        if (refresh)
            that.data.refresh = true
        request.get("part/company/verify", {page: page}, function (data) {
            let list = that.data.list
            if (refresh) {
                list = data.list
                that.data.page = 1
            } else {
                for (let i = 0; i < data.list.length; i++)
                    list.push(data.list[i])
                that.data.page++
            }
            that.setData({
                list: list,
                loading: false,
                refresh: false
            })
        })
    },
})