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
                domain = that.data.domain,
                index = e.currentTarget.dataset.index,
                sliders = that.data.sliders,
                urls = []
            for (let i in sliders){
                urls.push(domain+sliders[i])
            }
            wx.previewImage({
                current: urls[index],
                urls: urls
            })
        }
    }
})