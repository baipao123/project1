Page({
    data: {
        link: ""
    },
    onLoad: function (options) {
        let url = options && options.hasOwnProperty("url") ? options.url : ""
        if (url == "") {
            wx.navigateBack()
        } else {
            this.setData({
                link: url
            })
        }
        wx.hideShareMenu()
    }
})