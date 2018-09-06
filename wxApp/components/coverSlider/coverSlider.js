const app = getApp()

Component({
    options: {
        multipleSlots: false // 在组件定义时的选项中启用多slot支持
    },
    properties: {
        sliders: {
            type: Array,
            value: [],
            observer: function (newData, oldData) {
            }
        }
    },
    data: {
        domain: app.globalData.qiNiuDomain
    },
    ready: function () {
        let that = this
    },
    methods: {
        sliderTap: function (e) {
            let that = this,
                index = e.currentTarget.dataset.index,
                sliders = that.data.sliders
            wx.previewImage({
                current: sliders[index],
                urls: sliders
            })
        }
    }
})