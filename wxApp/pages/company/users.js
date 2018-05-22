const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        list: [],
        jid: 0,
        page: 1,
        loading: false,
        empty: false,
    },
    onLoad: function (options) {
        let that = this
        that.setData({
            jid: options && options.hasOwnProperty("jid") ? options.jid : 0
        })
        that.getList(true)
    },
    getList: function () {
        let that = this,
            page = that.data.page,
            jid = that.data.jid
        if (that.data.loading)
            return false;
        that.data.loading = true
        request.get("part/job/users", {id: jid, page: page}, function (data) {
            let list = that.data.list
            for (let i = 0; i < data.list.length; i++)
                list.push(data.list[i])
            that.data.page++
            that.setData({
                job: data.job,
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