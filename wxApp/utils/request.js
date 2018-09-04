/**
 * Created by huangchen on 2018/4/17.
 */
const post = (url, params, success, fail, complete) => {
    request("POST", url, params, success, fail, complete);
}

const get = (url, params, success, fail, complete) => {
    request("GET", url, params, success, fail, complete);
}

const request = (method, url, params, success, fail, complete) => {
    if (url.substr(0, 5) != "https")
        // url = "https://demo.wx-dk.cn/" + url;
        url = "https://dev.jiuyeli.org/" + url;
    wx.request({
        url: url,
        data: params,
        method: method,
        dataType: "json",
        header: {
            "Content-Type": "application/x-www-form-urlencoded",
            cookie: wx.getStorageSync('cookie')
        },
        success: function (res) {
            if (res.header['Set-Cookie'])
                wx.setStorageSync('cookie', res.header['Set-Cookie']);
            if (res.data.code != undefined) {
                if (res.data.code == 0) {
                    if (res.data.hasOwnProperty("msg") && res.data.msg != '') {
                        wx.showToast({
                            title: res.data.msg,
                            icon: 'success'
                        })
                    }
                    if (typeof success == 'function')
                        success(res.data.data, res.data);
                } else if (res.data.code == -1)
                    getApp().login();
                else {
                    wx.showToast({
                        title: res.data.msg,
                        icon: 'none'
                    })
                    if (typeof fail == 'function')
                        fail(res.data)
                }
            } else {
                wx.showToast({
                    title: "500,服务器解析异常",
                    icon: 'none'
                })
            }
        },
        fail: fail,
        compete: complete
    });
}

module.exports = {
    post: post,
    get: get,
}