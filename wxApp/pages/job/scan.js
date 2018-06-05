const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        uJob: null,
        text: "",
        isDecrypt: true
    },
    onLoad: function () {
        let that = this
        that.scan()
        wx.hideShareMenu()
        app.setCompanyStyle()
    },
    getInfo: function (text) {
        let that = this
        request.post("part/company/qr-user", {code: text}, function (data) {
            that.setData({
                uJob: data.uJob,
                isDecrypt: false
            })
            wx.navigateBack()
        })
    },
    refuse: function (e) {
        let that = this,
            data = e.detail.value
        data.formId = e.detail.formId
        data.type = 0;
        wx.showModal({
            title: "确认",
            content: "确认拒绝用户 " + that.data.uJob.name + " 的入职申请?\n一经操作，无法撤销",
            success: function (res) {
                if (res.confirm) {
                    request.post("part/company/verify-user", data, function (data) {
                        that.setData({
                            "uJob.status": 9
                        })
                    })
                }
            }
        })
    },
    verify: function (e) {
        let that = this,
            data = e.detail.value
        data.formId = e.detail.formId
        data.type = 1
        wx.showModal({
            title: "确认",
            content: "确认通过用户 " + that.data.uJob.name + " 的入职申请?\n一经操作，无法撤销",
            success: function (res) {
                if (res.confirm) {
                    request.post("part/company/verify-user", data, function (data) {
                        that.setData({
                            "uJob.status": 2
                        })
                    })
                }
            }
        })
    },
    scan: function () {
        let that = this
        app.authorize("scope.camera", function () {
            wx.scanCode({
                onlyFromCamera: true,
                scanType: ["qrCode"],
                success: function (res) {
                    let text = res.result
                    that.setData({
                        text: text,
                        isDecrypt: true
                    })
                    that.getInfo(text)
                },
                fail: function (res) {
                    that.setData({
                        isDecrypt: false
                    })
                }
            })
        }, function () {
            app.toast("请开启相机权限","none")
        })
    }
})