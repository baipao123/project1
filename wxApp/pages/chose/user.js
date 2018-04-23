//获取应用实例
const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        type: 0,
        phone: '',
        phoneText: '',
        isAgree: false,
        user:{},
    },
    onLoad: function () {
        this.setData({
            user: app.globalData.user
        })
    },
    bindAgreeChange: function (e) {
        this.setData({
            isAgree: !!e.detail.value.length
        });
    },
    getPhoneNumber:function (e) {
        console.log(e.detail);
        let that = this;
        request.post("part/user/phone-decrypt", e.detail, function (response) {
            console.log(response);
            that.setData({
                type: 1,
                phone: response.purePhoneNumber,
                phoneText: response.phoneNumber,
            });
        })
    },
    userBindPhone:function () {
        this.setData({type:2});
    },
    bindPhone:function (e) {
        let data = e.detail.value;
        let that = this;
        data.formId = e.detail.formId;
        request.post("/part/user/join-user",data,function(res){
            app.globalData.user = res.user;
            wx.navigateTo({
                url: "/pages/user/user"
            })
        })
    }
})
