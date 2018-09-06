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
        position: {},
        schools: [],
        school_value: 0,
        school_year: 0,
        schoolYears: [],

        changeCover: false,
        deleteCover: false
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
        app.setNavBarBackColor()
        that.getSchools(app.globalData.user.city_id,app.globalData.user.school_id)
        that.getYears(app.globalData.user.school_year)
    },
    onShow: function () {
        let region = app.globalData.region,
            that = this
        console.log(region)
        if (region.isSelect) {
            this.setData({
                "user.city_id": region.cid,
                "user.area_id": region.aid,
                "user.cityStr": region.cityStr
            })
            app.resetRegion()
            wx.showModal({
                title: '提示',
                content: '修改地区为 ' + region.cityStr + ' ?',
                success: function (res) {
                    if (res.confirm) {
                        let data = {
                            cid: region.cid,
                            aid: region.aid,
                            name: 'city'
                        }
                        that.edit(data)
                        that.getSchools(region.cid,that.data.user.school_id)
                    }
                }
            })
        }
        wx.hideShareMenu()
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
        request.post("part/user/edit", data, function (res) {
            app.toast("修改成功")
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
    },
    choseRegion: function () {
        let that = this,
            aid = that.data.user.area_id,
            cid = that.data.user.city_id
        app.turnPage("districtSelect/districtSelect?aid=" + aid + "&cid=" + cid)
    },
    tipsName: function (e) {
        app.toast("企业名称无法修改", "none")
    },
    tipsUser: function (e) {
        if (this.data.company.type == 3)
            app.toast("个人招聘者无法修改联系人","none");
        else
            this.prompt(e)
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
        if (school_id == that.data.user.school_id)
            return true
        app.confirm(school_id == 0 ? '确定不选择学校？' : "确定选择学校:" + school_name + "?", function (e) {
            let data = {
                name: "school_id",
                value: school_id
            }
            that.edit(data)
        })
    },
    choseSchoolYear:function (e) {
        let that = this,
            index = e.detail.value,
            year = that.data.schoolYears[index]
        if (year == that.data.user.school_year)
            return true
        app.confirm(index == 0 ? "确定不选择学年？" : "确定选择学年:" + year + "?", function (e) {
            let data = {
                name: "school_year",
                value: year
            }
            that.edit(data)
        })
    },


    uploadImages: function (e) {
        let that = this,
            images =  that.data.company.covers
            that.setData({
                deleteCover: false,
            })
        qiNiu.choseImg( 9, function (info) {
            images.push(info.key);
            that.setData({
                "company.covers": images,
            })
        });
    },
    previewImage: function (e) {
        let that = this,
            index = e.currentTarget.dataset.id,
            images = that.data.company.covers
        wx.previewImage({
            current: images[index], // 当前显示图片的http链接
            urls: images // 需要预览的图片http链接列表
        })
    },
    openDeleteImage: function (e) {
        this.setData({
            deleteCover: true
        })
    },
    deleteImage: function (e) {
        let that = this,
            images = that.data.company.covers,
            index = e.currentTarget.dataset.id
        images.splice(index, 1);
        that.setData({
            "company.covers": images,
        })
        if (images.length == 0) {
            that.setData({
                deleteCover: false,
            })
        }
    },
    goChangeCovers:function () {
        this.setData({
            changeCover: true
        })
    },
    saveCovers: function () {
        let that = this,
            data = {
                name: "cover",
                value: JSON.stringify(that.data.company.covers)
            }

        app.confirm("确定修改企业面貌？",function () {
            that.edit(data)
            that.setData({
                changeCover: false
            })
        })
    },

})