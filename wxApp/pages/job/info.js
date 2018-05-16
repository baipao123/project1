const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        jid: 0,
        user: {},
        // job: {},
        company: {},
        isOwner: false,
        scrollHeight: 500,
        domain:app.globalData.qiNiuDomain
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
        })
        wx.getSystemInfo({
            success: function (res) {
                console.log(res)
                that.setData({
                    scrollHeight: res.windowHeight
                })
            }
        });
    },
    toggleLike: function (e) {
        let that = this
        request.post("part/job/follow", {jid: that.data.jid}, function (e) {
            that.setData({
                "job.isLike": !that.data.job.isLike
            })
        })
    },
    toggleJobStatus: function (e) {
        let that = this
        console.log(e)
        request.post("part/company/toggle-job", {
            jid: that.data.jid,
            status: e.currentTarget.dataset.status
        }, function (e) {
            that.setData({
                "job.status": that.data.job.status == 1 ? 2 : 1
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
            that.goUJobInfo(res.id)
        })
    },
    phoneCall: function () {
        let that = this
        wx.makePhoneCall({
            phoneNumber: that.data.job.phone //仅为示例，并非真实的电话号码
        })
    },
    showQuizPosition:function (e) {
        let that = this
        wx.openLocation({
            latitude: parseFloat(that.data.job.quiz.latitude),
            longitude: parseFloat(that.data.job.quiz.longitude),
            scale: 28
        })
    },
    showWorkPosition:function (e) {
        let that = this
        wx.openLocation({
            latitude: parseFloat(that.data.job.work.latitude),
            longitude: parseFloat(that.data.job.work.longitude),
            scale: 28
        })
    },
    goUJobInfo:function(id){
        id= typeof id== "object"?this.data.job.uJid:id
        wx.navigateTo({
            url:"getInfo?id="+id
        })
    }
})