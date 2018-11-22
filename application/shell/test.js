var request = require('request');
var component_access_token = 'HNO9xV9czwVQwoaxMi97c1M4uxrnw_oyvtmogT8PSmZITzCcNwRplrExK9QJLwKCp8HaVt_w8GOChtK_Ev5T9U6s8RrOZssvLvF2WRzQVigQRi2ppShdFYhj8JFpV90EWOLeAEALDA';
var url = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token='+component_access_token;
var requestData={component_appid:'wx4d981fffa8e917e7',authorizer_appid:'wxd3a678cfeb03e3a3',authorizer_refresh_token:'KHkUBBAOmI0mKiUHsqCZmeF317EQrLT063612vh_mso'};
request({
    url: url,
    method: "POST",
    json: true,
    headers: {
        "content-type": "application/json",
    },
    body: JSON.stringify(requestData)
}, function(error, response, body) {
    // console.log('wx_api_error:'+error);
    // console.log('wx_api_response:'+JSON.stringify(response));
    // console.log('wx_api_body:'+JSON.stringify(body));
    console.log('component_appid:'+requestData.component_appid);
    console.log('authorizer_appid:'+requestData.authorizer_appid);
    console.log('authorizer_refresh_token:'+requestData.authorizer_refresh_token);
    if (!error && response.statusCode == 200) {
        console.log('res：：：')
        console.log(body) // 请求成功的处理逻辑
    }
});
