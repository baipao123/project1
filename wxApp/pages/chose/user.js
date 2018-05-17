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
    bindPhone:function (e) {
        let data = e.detail.value;
        let that = this;
        data.formId = e.detail.formId;
        request.post("part/user/join-user",data,function(res){
            app.globalData.user = res.user;
            app.toast("注册成功")
            wx.switchTab({
                url: "/pages/user/user"
            })
        })
    },
    onShow: function () {
        let region = app.globalData.region
        console.log(region)
        if (region.isSelect) {
            this.setData({
                "user.city_id": region.cid,
                "user.area_id": region.aid,
                "user.cityStr": region.cityStr
            })
            app.resetRegion()
        }
    },
    goSelectDistrict: function (e) {
        let that = this
        wx.navigateTo({
            url: "/pages/districtSelect/districtSelect?aid=" + that.data.user.area_id + "&cid=" + that.data.user.city_id
        })
    },
})
