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
        schools: [],
        school_value: 0,
        school_year: 0,
        schoolYears: [],
    },
    onLoad: function () {
        this.setData({
            user: app.globalData.user
        })
        if (app.globalData.user)
            this.getSchools(app.globalData.user.city_id, app.globalData.user.school_id)
        this.getYears()
        wx.hideShareMenu()
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
        data.school_id = that.data.school_id
        data.school_year = that.data.school_year
        request.post("part/user/join-user",data,function(res){
            app.globalData.user = res.user;
            app.toast("注册成功")
            app.turnPage("user/user")
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
            this.getSchools(region.cid)
        }
    },
    goSelectDistrict: function (e) {
        let that = this
        wx.navigateTo({
            url: "/pages/districtSelect/districtSelect?aid=" + that.data.user.area_id + "&cid=" + that.data.user.city_id
        })
    },
    getSchools: function (city_id,school_id) {
        let that = this
        city_id = city_id == undefined ? 0 : city_id
        school_id = school_id == undefined ? 0 : school_id
        request.get("part/school/list", {city_id: city_id,school_id:school_id}, function (res) {
            that.setData({
                schools:res.schools,
                school_value:res.index
            })
        })
    },
    getYears:function (year) {
        let that = this,
            thisYear = (new Date()).getFullYear(),
            arr = [],
            value = 0
        year = year == undefined ? 0 : year
        arr.push("请选择学年")
        if(year < thisYear - 4 && year > 0) {
            arr.push(year)
            value = 1
        }
        for (let index = thisYear - 4; index <= thisYear; index++) {
            arr.push(index)
            if(year == index)
                value = index - thisYear + 4
        }
        that.setData({
            schoolYears: arr,
            school_year: value
        })
        console.log(arr)

    },
    choseSchool:function (e) {
        let that = this,
            index = e.detail.value,
            school = that.data.schools[index],
            school_id = school.id,
            school_name = school.name
        that.setData({
            school_id:school_id,
            school_name:school_id == 0 ? "" : school_name
        })
    },
    choseSchoolYear:function (e) {
        let that = this,
            index = e.detail.value,
            year = that.data.schoolYears[index]
        that.setData({
            school_year:index == 0 ? 0 : year
        })
    },
})
