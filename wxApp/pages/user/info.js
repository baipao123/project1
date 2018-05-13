const app = getApp()
const request = require("./../../utils/request.js")
const qiNiu = require("./../../utils/qiniuUploader")


Page({
    data: {
        user: {},
        company: {},
        isCompany: false,
        domain: app.globalData.qiNiuDomain,
        isPrompt: false,
        isPosition: false,
        prompt: {
            title: '标题',
            name: '',
            value: '',
            nameText: ''
        },
        position: {}
    },
    onLoad: function () {
        let that = this
        that.setData({
            user: app.globalData.user,
            isCompany: app.globalData.user && app.globalData.user.type > 1
        })
        app.getCompanyInfo(function () {
            that.setData({
                company: app.globalData.company,
            })
        })
    },
    prompt: function (e) {
        let that = this,
            name = e.currentTarget.dataset.name,
            value = '',
            user = that.data.user,
            company = that.data.company,
            isCompany = that.data.isCompany
        switch (name) {
            case "name":
                value = company.name
                break
            case 'realname':
                value = user.username
                break
            case 'phone':
                value = ''
                break
            case 'description':
                value = isCompany ? company.description : user.description
                break
            case 'position':
                value = company.position.address
                break
            case 'tips':
                value = company.tips
                break
            default:
                break
        }
        that.setData({
            isPrompt: true,
            prompt: {
                title: e.currentTarget.dataset.title,
                name: name,
                value: value
            },
            isPosition: name == 'position',
            position: {}
        })
    },
    cancel: function () {
        let that = this
        that.setData({
            isPrompt: false
        })
    },
    submitPrompt: function (e) {
        console.log(e)
        let that = this,
            data = e.detail.value
        data.formId = e.detail.formId
        data.name = that.data.prompt.name
        that.edit(data)
    },
    uploadIcon: function () {
        this.submitImage("icon", "修改企业Logo?");
    },
    uploadCover: function () {
        this.submitImage("cover", "修改企业背景?");
    },
    submitImage: function (name, text) {
        let that = this
        wx.showModal({
            title: '提示',
            content: text,
            success: function (res) {
                if (res.confirm) {
                    qiNiu.choseImg(1, function (info) {
                        console.log(info)
                        let data = {
                            name: name,
                            value: info.key
                        }
                        that.edit(data)
                    })
                }
            }
        })
    },
    edit: function (data) {
        let that = this
        request.post("/part/user/edit", data, function (res) {
            wx.showToast({
                title: "修改成功",
                icon: "success"
            })
            if (res.hasOwnProperty("user")) {
                app.globalData.user = res.user
                that.setData({
                    isPrompt: false,
                    user: res.user
                })
            }
            if (res.hasOwnProperty("company")) {
                app.globalData.company = res.company
                that.setData({
                    isPrompt: false,
                    company: res.company
                })
            }
        })
    },
    chosePosition: function (e) {
        let that = this
        wx.chooseLocation({
            success: function (data) {
                that.setData({
                    "prompt.value": data.address,
                    position: data
                })
            }
        })
    }
})