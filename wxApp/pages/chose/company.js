const app = getApp()
const request = require("./../../utils/request.js")
const qiNiu = require("./../../utils/qiniuUploader")

Page({
    data: {
        user: {},
        company: {},
        type: 0,
        domain: app.globalData.qiNiuDomain,
        isAgree: false,
        images: [],
        imageKeys: [],
        deleteImage: false,
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
        console.log(e.detail);
        let that = this;
        request.post("part/user/phone-decrypt", e.detail, function (response) {
            console.log(response);
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
        console.log(e);
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
    }
})