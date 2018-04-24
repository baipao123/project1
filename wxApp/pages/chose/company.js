const app = getApp()
const request = require("./../../utils/request.js")
const qiNiu = require("./../../utils/qiniuUploader")

Page({
    data: {
        user: {},
        company:{},
        icon:"",
        cover:"",
    },
    onLoad: function () {
        this.setData({
            user: app.globalData.userInfo
        });
    },
    uploadIcon: function () {
        let that = this;
        qiNiu.choseImg(1, function (info) {
            that.setData({icon: info.imageURL});
        });
    }
})