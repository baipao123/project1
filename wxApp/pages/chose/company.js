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
        domain: '',
        isAgree: false,
        images: [],
        imagesJson: "[]",
        coversJson:"[]",
        deleteImage: false,
        deleteCover: false,
    },
    onLoad: function (options) {
        let that = this
        app.getCompanyInfo(function () {
            let company = app.globalData.company
            that.setData({
                company: company
            });
            if(company && company.covers)
                that.setData({
                    coversJson: JSON.stringify(company.covers)
                })
            else
                that.setData({
                    "company.covers":[]
                })
            if (company && company.status == 1) {
                app.toast("您已经是企业了")
                wx.navigateBack()
                return false
            }
        })
        that.setData({
            user: app.globalData.user,
            domain: app.globalData.qiNiuDomain
        });
        wx.hideShareMenu()
        app.setCompanyStyle()
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
            that.setData({"company.covers": info.key});
        });
    },
    bindCompany: function (e) {
        let data = e.detail.value,
            that = this,
            url = that.data.company ? "part/company/edit" : "part/company/join"
        data.formId = e.detail.formId;
        data.phoneType = that.data.type;
        console.log(data)
        request.post(url, data, (res) => {
            app.globalData.company.status = 0
            app.globalData.company.refuseReason = ''
            app.globalData.user.type = data.type
            app.getCompanyInfo(function () {
                app.turnPage("user/user")
            },true)
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
                if (!company.hasOwnProperty("positionStr") || company.positionStr == "" || (company.position != undefined && company.positionStr == company.position.address))
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
                "user.purePhone": response.purePhoneNumber,
            });
        })
    },
    bindAgreeChange: function (e) {
        this.setData({
            isAgree: !!e.detail.value.length,
        });
    },
    uploadImages: function (e) {
        let that = this,
            target = e.currentTarget.dataset.target,
            images = target == "cover" ? that.data.company.covers : that.data.images
        if (target == "cover")
            that.setData({
                deleteCover: false,
            })
        else
            that.setData({
                deleteImage: false,
            })
        if (target != "cover" && images.length > 2)
            return false;
        qiNiu.choseImg(target != "cover" ? 3 - images.length : 9, function (info) {
            images.push(info.key);
            if (target == "cover")
                that.setData({
                    "company.covers": images,
                    coversJson: JSON.stringify(images)
                })
            else
                that.setData({
                    images: images,
                    imageJson: JSON.stringify(images)
                })
        });
    },
    previewImage: function (e) {
        let that = this,
            index = e.currentTarget.dataset.id,
            target = e.currentTarget.dataset.target,
            images = target == "cover" ? that.data.company.covers : that.data.images
        wx.previewImage({
            current: images[index], // 当前显示图片的http链接
            urls: images // 需要预览的图片http链接列表
        })
    },
    openDeleteImage: function (e) {
        let target = e.currentTarget.dataset.target
        if (target == "cover")
            this.setData({
                deleteCover: true
            })
        else
            this.setData({
                deleteImage: true
            })
    },
    deleteImage: function (e) {
        let that = this,
            target = e.currentTarget.dataset.target,
            images = target == "cover" ? that.data.company.covers : that.data.images,
            index = e.currentTarget.dataset.id
        images.splice(index, 1);
        if (target == "cover")
            that.setData({
                "company.covers": images,
                coversJson: JSON.stringify(images)
            })
        else
            that.setData({
                images: images,
                imageJson: JSON.stringify(images)
            })
        if (images.length == 0) {
            if (target == "cover")
                that.setData({
                    deleteCover: false,
                })
            else
                that.setData({
                    deleteImage: false,
                })
        }
    },
    goSelectDistrict: function (e) {
        let that = this
        console.log(that.data.user)
        wx.navigateTo({
            url: "/pages/districtSelect/districtSelect?aid=" + that.data.user.area_id + "&cid=" + that.data.user.city_id
        })
    },
})