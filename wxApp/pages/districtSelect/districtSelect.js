const app = getApp()
const request = require("./../../utils/request.js")

Page({
    data: {
        cities: [],
        areas: [],
        city: {},
        area: {},
        value: [0, 0],
        cid:0,
        aid:0
    },
    onLoad: function (options) {
        let that = this
        request.get("part/district/all", options, function (data) {
            console.log(data.cities);
            that.setData({
                cities: data.cities,
                city: data.cities[data.value[0]],
                areas: data.cities[data.value[0]].areas,
                area: data.cities[data.value[0]].areas[data.value[1]],
                cid:data.cid,
                aid:data.aid,
            })
            that.setData({
                value:data.value
            })
        })
    },
    change: function (e) {
        let val = e.detail.value,
            cities = this.data.cities,
            areas = this.data.areas,
            city = cities[val[0]],
            area = areas[val[1]];
        if (this.data.value[0] != val[0]) {
            this.setData({
                city: city,
                areas: city.areas,
                area:city.areas[0],
                value:[val[0],0]
            })
        } else if (this.data.value[1] != val[1]) {
            this.setData({area: area,value:val})
        }
    },
    _cancel: function () {
        wx.navigateBack();
    },
    _submit: function () {
        app.globalData.region = {
            cid: this.data.city.cid,
            aid: this.data.area.aid,
            cityStr: this.data.city.city + " " + this.data.area.area,
            isSelect: true
        }
        wx.navigateBack();
    },
})