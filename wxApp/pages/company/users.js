const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        list: [],
        job: {},
        jid: 0,
        page: 1,
        loading: false,
        empty: false,
        navBar: {
            activeIndex: 0,
            sliderOffset: 0,
            sliderLeft: 0,
            sliderWidth: 96,
            length: 2
        }
    },
    onLoad: function (options) {
        let that = this
        that.setData({
            jid: options && options.hasOwnProperty("jid") ? options.jid : 0
        })
        that.setNavBarWidth()
        that.getList(true)
    },
    setNavBarWidth: function () {
        let that = this,
            index = that.data.navBar.activeIndex,
            length = that.data.navBar.length
        app.getSystemInfo(function (res) {
            let width = res.hasOwnProperty("windowWidth") ? res.windowWidth : 300,
                sliderWidth = width / (length + 1)
            console.log(width)
            that.setData({
                "navBar.sliderLeft": (width / length - sliderWidth) / 2,
                "navBar.sliderOffset": width / length * that.data.navBar.activeIndex,
                "navBar.sliderWidth": sliderWidth
            });
        })
    },
    tabClick: function (e) {
        this.setData({
            "navBar.sliderOffset": e.currentTarget.offsetLeft,
            "navBar.activeIndex": e.currentTarget.id
        });
        this.getList(true)
    },
    getList: function (refresh) {
        let that = this,
            page = refresh ? 1 : that.data.page,
            jid = that.data.jid,
            index = that.data.index,
            barIndex = that.data.navBar.activeIndex
        if (!refresh && that.data.loading)
            return false;
        that.data.loading = true
        if (page == 1)
            that.setData({
                page: 1,
                loading: true
            })
        request.get("part/job/users", {id: jid, page: page, status: barIndex == 0 ? 2 : 1}, function (data) {
            if (index != that.data.index)
                return false
            let list = that.data.list
            if (refresh)
                list = data.users
            else
                for (let i = 0; i < data.users.length; i++)
                    list.push(data.users[i])

            that.data.page++
            that.setData({
                job: data.job,
                list: list,
                loading: false,
                empty: data.users.length == 0
            })
        })
    },
    onReachBottom: function (e) {
        let that = this
        that.getList(false)
    },
    refuse: function (e) {
        let that = this,
            index = e.detail.value.index,
            uJid = that.data.list[index].info.id,
            name = that.data.list[index].user.name
        wx.showModal({
            title: "拒绝招聘？",
            content: "确定拒绝招聘用户 " + name + " ？拒绝之后无法恢复，用户也无法重新报名！",
            success: function (res) {
                if (res.confirm) {
                    let data = {
                        uJid: uJid,
                        formId: e.detail.formId,
                    }
                    request.post("part/company/refuse-job", data, function (response) {
                        let list = that.data.list
                        list[index].info.status = 9
                        that.setData({
                            list: list
                        })
                    })
                }
            }
        })
    }
})