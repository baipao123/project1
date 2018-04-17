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
    wx.request({
        url: url,
        data: params,
        method: method,
        dataType: "json",
        success: success,
        fail: fail,
        compete: complete
    });
}

module.exports = {
    post: post,
    get: get,
}