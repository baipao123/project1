const app = getApp()
const request = require("./../../utils/request.js")
const qiNiu = require("./../../utils/qiniuUploader")

Page({
    data: {
        user: {
            area_id: 0,
            city_id: 0,
            cityStr: "请选择",
        },
        company: {
            positionStr: "",
        },
        type: 0,
        domain: app.globalData.qiNiuDomain,
        isAgree: false,
        images: [],
        imageKeys: [],
        deleteImage: false,
    },
    onLoad: function () {
        let that = this
        if (app.globalData.company == {}) {
            app.getCompanyInfo(function () {
                that.setData({
                    company: app.globalData.company
                });
            })
        }
        this.setData({
            user: app.globalData.user,
            company: app.globalData.company
        });
        wx.hideShareMenu()
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
    uploadIcon: function () {
        let that = this;
        qiNiu.choseImg(1, function (info) {
            that.setData({"company.icon": info.key});
        });
    },
    uploadCover: function () {
        let that = this;
        qiNiu.choseImg(1, function (info) {
            that.setData({"company.cover": info.key});
        });
    },
    bindCompany: function (e) {
        let data = e.detail.value;
        data.formId = e.detail.formId;
        data.phoneType = this.data.type;
        request.post("part/company/join", data, (res) => {
            wx.navigateTo({
                url: "/pages/user/user"
            })
        })
    },
    typeChange: function (e) {
        this.setData({
            "company.type": e.detail.value
        })
    },
    chosePosition: function () {
        let that = this;
        wx.chooseLocation({
            success: function (data) {
                let company = that.data.company,
                    obj = {
                        "company.position": data
                    };
                if (company.positionStr == "" || (company.position != undefined && company.positionStr == company.position.address))
                    obj = {
                        "company.position": data,
                        "company.positionStr": data.address,
                    };
                that.setData(obj);
            }
        })
    },
    positionSave: function (e) {
        this.setData({
            "company.positionStr": e.detail.value
        });
    },
    useBindPhone: function () {
        this.setData({type: 2});
    },
    getPhoneNumber: function (e) {
        let that = this;
        request.post("part/user/phone-decrypt", e.detail, function (response) {
            that.setData({
                type: 1,
                "user.phone": response.purePhoneNumber,
            });
        })
    },
    bindAgreeChange: function (e) {
        this.setData({
            isAgree: !!e.detail.value.length,
        });
    },
    uploadImages: function () {
        let that = this,
            images = that.data.images,
            imageKeys = that.data.imageKeys;
        this.setData({
            deleteImage: false,
        })
        if (images.length > 2)
            return false;
        qiNiu.choseImg(3 - images.length, function (info) {
            images.push(that.data.domain + info.key);
            imageKeys.push(info.key);
            that.setData({
                images: images,
                imageKeys: imageKeys
            })
        });
    },
    previewImage: function (e) {
        let that = this,
            index = e.currentTarget.dataset.id,
            images = that.data.images;
        wx.previewImage({
            current: images[index], // 当前显示图片的http链接
            urls: images // 需要预览的图片http链接列表
        })
    },
    openDeleteImage: function (e) {
        this.setData({
            deleteImage: true
        })
    },
    deleteImage: function (e) {
        let that = this,
            index = e.currentTarget.dataset.id,
            images = that.data.images,
            imageKeys = that.data.imageKeys;
        images.splice(index, 1);
        imageKeys.splice(index, 1);
        that.setData({
            images: images,
            imageKeys: imageKeys
        })
        if (images.length == 0)
            that.setData({
                deleteImage: false
            })
    },
    goSelectDistrict: function (e) {
        let that = this
        wx.navigateTo({
            url: "/pages/districtSelect/districtSelect?aid=" + that.data.user.area_id + "&cid=" + that.data.user.city_id
        })
    },
})