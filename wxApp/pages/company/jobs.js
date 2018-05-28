const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        list: [],
        page: 1,
        loading: false,
        empty: false,
    },
    onLoad: function () {
        let that = this
        that.getList(true)
        wx.hideShareMenu()
    },
    getList: function () {
        let that = this,
            page = that.data.page
        if (that.data.loading)
            return false;
        that.data.loading = true
        request.get("part/company/jobs-sample", {page: page}, function (data) {
            let list = that.data.list
            for (let i = 0; i < data.list.length; i++)
                list.push(data.list[i])
            that.data.page++
            that.setData({
                list: list,
                loading: false,
                empty: data.list.length == 0
            })
        })
    },
    onReachBottom: function (e) {
        let that = this
        that.getList(false)
    },
})