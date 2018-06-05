const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        user: {},
        company: {},
        job: {
            gender: 3,
            work: {
                useQuiz: true
            },
            useCompanyContact: true
        },
        tmpQuizPosition: '',
        tmpWorkPosition: '',
        day: {},
        isEdit: false,
    },
    onLoad: function (options) {
        let that = this,
            date = new Date(),
            month = '0' + (date.getMonth() + 1),
            day = '0' + date.getDate(),
            today = date.getFullYear() + '-' + month.substr(-2) + "-" + day.substr(-2)
        this.setData({
            user: app.globalData.user,
            day: {
                start: today,
                end: (date.getFullYear() + 1) + '-12-31'
            },
        });
        app.getCompanyInfo(function () {
            that.setData({
                company: app.globalData.company
            })
        })
        if (options && options.hasOwnProperty("id")) {
            let jid = options.id
            request.get("part/job/info?id=" + jid, {}, function (data) {
                that.setData({
                    job: data.job,
                    isEdit: true,
                })
            })
        }
        app.setCompanyStyle()
    },
    onShow: function () {
        let region = app.globalData.region
        if (region.isSelect) {
            this.setData({
                "job.city_id": region.cid,
                "job.area_id": region.aid,
                "job.cityStr": region.cityStr
            })
            console.log(region)
            app.resetRegion()
        }
        wx.hideShareMenu()
    },
    goSelectDistrict: function (e) {
        let that = this,
            cid = e.target.dataset.cid,
            aid = e.target.dataset.aid
        app.turnPage("districtSelect/districtSelect?aid=" + aid + "&cid=" + cid)
    },
    choseQuizPosition: function () {
        let that = this;
        console.log(1)
        wx.chooseLocation({
            success: function (data) {
                let quiz, nowPosition = '', tmpPosition = that.data.tmpQuizPosition
                if (that.data.job.hasOwnProperty("quiz")) {
                    quiz = that.data.job.quiz
                    nowPosition = quiz.position || that.data.company.position.address
                }
                if (tmpPosition == "" || nowPosition == tmpPosition) {
                    data.position = data.address;
                } else
                    data.position = nowPosition
                that.setData({
                    tmpQuizPosition: nowPosition,
                    "job.quiz": data,
                })
            }
        })
    },
    quizPositionSave: function (e) {
        console.log(e)
        let that = this, quiz, nowPosition = that.data.company.position.address
        if (that.data.job.hasOwnProperty("quiz")) {
            quiz = that.data.job.quiz
            if (quiz.position)
                nowPosition = quiz.position
        }
        that.setData({
            tmpQuizPosition: nowPosition,
            "job.quiz.position": e.detail.value
        })
    },
    choseWorkPosition: function () {
        let that = this;
        console.log(1)
        wx.chooseLocation({
            success: function (data) {
                let work, nowPosition = '', tmpPosition = that.data.tmpWorkPosition
                if (that.data.job.hasOwnProperty("work")) {
                    work = that.data.job.work
                    nowPosition = work.position || that.data.company.position.address
                }
                if (tmpPosition == "" || nowPosition == tmpPosition) {
                    data.position = data.address;
                } else
                    data.position = nowPosition
                that.setData({
                    tmpWorkPosition: nowPosition,
                    "job.work": data,
                })
            }
        })
    },
    workPositionSave: function (e) {
        console.log(e)
        let that = this, work, nowPosition = that.data.company.position.address
        if (that.data.job.hasOwnProperty("work")) {
            work = that.data.job.work
            if (work.position)
                nowPosition = work.position
        }
        that.setData({
            tmpWorkPosition: nowPosition,
            "job.work.position": e.detail.value
        })
    },
    useQuiz: function (e) {
        this.setData({
            "job.work.useQuiz": e.detail.value
        })
    },
    submit: function (e) {
        let that = this,
            data = e.detail.value,
            url = that.data.isEdit ? "part/company/edit-job?jid=" + that.data.job.id : "part/company/add-job"
        data.formId = e.detail.formId;
        console.log(data)
        console.log(e.detail)
        request.post(url, data, function (res, response) {
            app.toast(response.msg, "success", function () {
                wx.redirectTo({
                    url: "/pages/job/info?id=" + res.jid
                })
            })
        })
    },
    changeStartDate: function (e) {
        let that = this,
            value = e.detail.value
        that.setData({
            "job.start_date": value
        })
    },
    changeEndDate: function (e) {
        let that = this,
            value = e.detail.value
        that.setData({
            "job.end_date": value
        })
    },
    changeStartTime: function (e) {
        let that = this,
            value = e.detail.value
        that.setData({
            "job.start_time": value
        })
    },
    changeEndTime: function (e) {
        let that = this,
            value = e.detail.value
        that.setData({
            "job.end_time": value
        })
    },
    useContact: function (e) {
        this.setData({
            "job.useCompanyContact": e.detail.value
        })
    }
})