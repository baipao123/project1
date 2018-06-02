const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        jid: 0,
        user: {},
        // job: {},
        company: {},
        isOwner: false,
        domain: app.globalData.qiNiuDomain,
        errMsg: ""
    },
    onLoad: function (options) {
        let jid = options && options.hasOwnProperty("id") ? options.id : 0,
            that = this
        that.setData({
            user: app.globalData.user,
            jid: jid
        })
        request.get("part/job/info", {id: jid}, function (data) {
            that.setData({
                job: data.job,
                company: data.company
            })
            if (data.job.uid == that.data.user.uid)
                that.setData({
                    isOwner: true
                })
        }, function (response) {
            if (response.hasOwnProperty("msg"))
                that.setData({
                    errMsg: response.msg
                })
        })
    },
    toggleLike: function (e) {
        let that = this
        request.post("part/job/follow", {jid: that.data.jid}, function (e) {
            that.setData({
                "job.isLike": !that.data.job.isLike
            })
        })
    },
    toggleJobStatus: function (status) {
        let that = this
        status = status == undefined ? (that.data.job.status == 1 ? 2 : 1) : status
        request.post("part/company/toggle-job", {
            jid: that.data.jid,
            status: status
        }, function (e) {
            that.setData({
                "job.status": status
            })
        })
    },
    sign: function (e) {
        console.log(e)
        let that = this,
            data = e.detail.value
        data.formId = e.detail.formId
        request.post("part/job/apply", data, function (res) {
            app.toast("报名成功")
            that.setData({
                "job.userStatus": 1
            })
            that.data.job.uJid = res.id
            that.goUJobInfo(res.id)
        })
    },
    phoneCall: function () {
        let that = this
        wx.makePhoneCall({
            phoneNumber: that.data.job.phone //仅为示例，并非真实的电话号码
        })
    },
    showQuizPosition: function (e) {
        let that = this
        wx.openLocation({
            latitude: parseFloat(that.data.job.quiz.latitude),
            longitude: parseFloat(that.data.job.quiz.longitude),
            scale: 28
        })
    },
    showWorkPosition: function (e) {
        let that = this
        wx.openLocation({
            latitude: parseFloat(that.data.job.work.latitude),
            longitude: parseFloat(that.data.job.work.longitude),
            scale: 28
        })
    },
    goUJobInfo: function (id) {
        id = typeof id == "object" ? this.data.job.uJid : id
        app.turnPage("job/getInfo?id="+id)
    },
    companyAction: function (e) {
        let that = this,
            jid = that.data.jid,
            status = that.data.job.status,
            itemList = [
                status == 1 ? "设为未发布" : "发布岗位",
                "复制岗位",
                "删除岗位",
                "编辑岗位",
                "查看员工列表"
            ]
        if (status != 4)
            itemList.push(status == 5 ? "岗位工作已结束" : (status == 2 ? "发布并设为已招满" : "设为已招满"))
        wx.showActionSheet({
            itemList: itemList,
            success: function (res) {
                switch (res.tapIndex) {
                    case 0:
                        that.toggleJobStatus(that.data.job.status == 1 ? 2 : 1)
                        break
                    case 1:
                        request.post("part/company/copy-job", {jid: jid}, function (res) {
                            app.toast("复制招聘信息成功")
                            let jid = res.jid
                            wx.navigateTo({
                                url: "/pages/job/add?id=" + jid
                            })
                        })
                        break
                    case 2:
                        app.confirm("确定删除岗位", function () {
                            that.toggleJobStatus(3)
                        })
                        break
                    case 3:
                        wx.navigateTo({
                            url: "/pages/job/add?id=" + jid
                        })
                        break
                    case 4:
                        wx.navigateTo({
                            url: "/pages/company/users?jid=" + jid
                        })
                        break
                    case 5:
                        if (status == 5) {
                            app.confirm("确定设置为已结束？结束后所有在职者将不能打卡", function () {
                                request.post("part/job/expire-job", {jid: jid}, function (res) {
                                    that.setData({
                                        "job.status": 4
                                    })
                                })
                            })
                        } else
                            app.confirm("确定设置为已招满？设置后将统一拒绝所有的报名者，并且岗位将不可报名", function () {
                                that.toggleJobStatus(5)
                            })
                        break
                    default:
                        break
                }
            }
        })
    },
    onShareAppMessage: function () {
        return {
            title: this.data.job.name,
            path: "/pages/job/info?id=" + this.data.jid
        }
    }
})