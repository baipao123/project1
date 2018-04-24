const app = getApp()
const request = require("./../../utils/request.js")
const qiNiu = require("./../../utils/qiniuUploader")

Page({
    data: {
        user: {},
        company: {},
        type: 0,
        domain: app.globalData.qiNiuDomain,
        phone: "",
        isAgree: false,
    },
    onLoad: function () {
        this.setData({
            user: app.globalData.user
        });
    },
    uploadIcon: function () {
        let that = this;
        qiNiu.choseImg(1, function (info) {
            that.setData({icon: info.key});
        });
    },
    uploadCover: function () {
        let that = this;
        qiNiu.choseImg(1, function (info) {
            that.setData({cover: info.key});
        });
    },
    bindCompany: function (e) {
        let data = e.detail.value;
        data.formId = e.detail.formId;
        request.post("/part/company/join", data, (res) => {
            wx.navigateTo({
                url: "/pages/user/user"
            })
        })
    },
    chosePosition: function () {
        let that = this;
        wx.chooseLocation({
            success: function (data) {
                let company = that.data.company;
                if (company.positionStr == "" || (company.position != undefined && company.positionStr == company.position.address))
                    company.positionStr = data.address;
                company.position = data;
                that.setData({
                    company: company
                });
            }
        })
    },
    positionSave: function (e) {
        let company = this.data.company;
        company.positionStr = e.detail.value;
        this.setData({
            company: company
        });
    },
    useBindPhone: function () {
        this.setData({type: 2});
    },
    getPhoneNumber: function (e) {
        console.log(e.detail);
        let that = this;
        request.post("part/user/phone-decrypt", e.detail, function (response) {
            console.log(response);
            that.setData({
                type: 1,
                phone: response.purePhoneNumber,
            });
        })
    },
    bindAgreeChange: function (e) {
        this.setData({
            isAgree: !!e.detail.value.length
        });
    },
})