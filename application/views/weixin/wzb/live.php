<!DOCTYPE html>
<!-- saved from url=(0044)http://m.yizhibo.com/l/R2ZWUMTk2FgOC02Z.html -->
<html lang="en" data-dpr="1" style="font-size: 36px;">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,maximum-scale=1.0,user-scalable=no">
    <title><?=$config['title']?></title>
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="msapplication-tap-highlight" content="no">
    <script src="../live_test2/file/hm.js"></script>
    <script src="../live_test2/file/share.html_aio_3f0c1c7.js"></script>
    <script src="../live_test2/file/jweixin-1.0.0.js"></script>
    <?php if($_SERVER['HTTP_HOST']=='jfb.dev.smfyun.com'):?>
    <script type="text/javascript" src="http://wechatfe.github.io/vconsole/lib/vconsole.min.js"></script>
    <?php endif?>
    <link rel="stylesheet" href="../live_test2/file/share.html_aio_d74d816.css">
    <style type="text/css">
    .sns-ft{
        text-align: center;
        border-top: 1px solid #dedede;
        padding-top: 6px;
        padding-bottom: 6px;
        padding-left: 5.5%;
        padding-right: 5.5%;
    }
    .enter-shop{
        width: 100%;
        z-index: -1;
    }
    .goods-pick{
        font-size: 12px;padding: 3px 6px;
    }
    .back{
        border-top: 1px solid #dedede;
    }
    h4 {
        width: 100%;
        bottom: 0rem;
        position: absolute;
        background-color: #fff;
    }
    .mask{
        height:100%;
        display: block;
        width: 100%;
        background-color: transparent;
        position: absolute;
        z-index: 10;
        top: 0px;
        display: none;
    }
    /*.transparent{height:30%;display: block;width: 100%;background-color: transparent;}*/
    /*#hide{height:100%;padding: 0;top: 0;background-color: transparent;}    */
    .sns-ctn {
        display: block;
    }
    /*.sns-ctn.sns2 {
        margin: 0rem 0.33333rem;
        padding: 0rem;
        height: 80%;
        display: block;
        overflow-y: scroll;
        -webkit-overflow-y:scroll;
    }*/
    .sns-ctn {
        margin-left:0.33333rem;
        margin-right: 0.33333rem;
        padding: 0rem;
        max-height: 60%;
        height: 98%;
        display: block;
        background-color: #fff;
        overflow: hidden;
        /*overflow-y: scroll;*/
        /*-webkit-overflow-y: scroll;*/
    }

    .sns-share {
        max-height: 60%;
        /*background-color: transparent;*/
    }

    .goods {
        display: block;
        margin-bottom: 0.2rem;
        padding-top: 0.2rem;
        height: 2.66667rem;
        border-top-color: #dedede;
        border-top-width: 1px;
        border-top-style: solid;
    }

    .goods-img {
        /*display: inline-block;*/
        height: 100%;
        width: 30%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .goods-img img {
        display: block;
        height: auto;
        max-width: 100%;
        max-height: 100%;
    }

    .goods-detail {
        display: inline-block;
        height: 2.66667rem;
        width: 6.5rem;
        position: absolute;
        right: 0;
    }

    .goods-name {
        padding-left: 15px;
        font-size: 12px;
        display: inline-block;
    }

    .goods-price {
        padding-left: 15px;
        font-size: 15px;
        display: block;
        position: absolute;
        bottom: 5%;
        color: #ff4806;
    }

    .goods-pick {
        display: block;
        position: absolute;
        right: 0;
        bottom: 0;
    }

    @-webkit-keyframes mw-kfr-slidedown {
        0% {
            opacity: 0;
            -webkit-transform: translateY(-2em)
        }
        15% {
            opacity: 1;
            -webkit-transform: translateY(0)
        }
        70% {
            -webkit-transform: translateY(3em)
        }
        85% {
            opacity: 1;
            -webkit-transform: translateY(4.5em)
        }
        to {
            opacity: 1;
            -webkit-transform: translateY(4.5em)
        }
    }

    @-moz-keyframes mw-kfr-slidedown {
        0% {
            opacity: 0;
            -webkit-transform: translateY(-2em)
        }
        15% {
            opacity: 1;
            -webkit-transform: translateY(0)
        }
        70% {
            -moz-transform: translateY(3em);
            transform: translateY(3em)
        }
        85% {
            opacity: 1;
            -moz-transform: translateY(4.5em);
            transform: translateY(4.5em)
        }
        to {
            opacity: 1;
            -moz-transform: translateY(4.5em);
            transform: translateY(4.5em)
        }
    }

    @keyframes mw-kfr-slidedown {
        0% {
            opacity: 0;
            -webkit-transform: translateY(-2em)
        }
        15% {
            opacity: 1;
            -webkit-transform: translateY(0)
        }
        70% {
            -webkit-transform: translateY(3em);
            -moz-transform: translateY(3em);
            transform: translateY(3em)
        }
        85% {
            opacity: 1;
            -webkit-transform: translateY(4.5em);
            -moz-transform: translateY(4.5em);
            transform: translateY(4.5em)
        }
        to {
            opacity: 1;
            -webkit-transform: translateY(4.5em);
            -moz-transform: translateY(4.5em);
            transform: translateY(4.5em)
        }
    }

    @-webkit-keyframes mw-kfr-loading {
        0% {
            opacity: 1;
            -webkit-transform: translateX(-200px)
        }
        to {
            opacity: 0;
            -webkit-transform: translateX(50em)
        }
    }

    @-webkit-keyframes mw-kfr-fadeIn {
        0% {
            display: block;
            opacity: 0;
            -webkit-transform: translateY(6em)
        }
        to {
            display: block;
            opacity: 1;
            -webkit-transform: translateY(0)
        }
    }

    @-webkit-keyframes mw-kfr-fadeOut {
        0% {
            display: block;
            opacity: 1;
            -webkit-transform: translateY(0)
        }
        to {
            display: none;
            opacity: 0;
            -webkit-transform: translateY(6em)
        }
    }

    @-moz-keyframes mw-kfr-fadeIn {
        0% {
            display: block;
            opacity: 0;
            -moz-transform: translateY(6em)
        }
        to {
            display: block;
            opacity: 1;
            -moz-transform: translateY(0)
        }
    }

    @-moz-keyframes mw-kfr-fadeOut {
        0% {
            display: block;
            opacity: 1;
            -moz-transform: translateY(0)
        }
        to {
            display: none;
            opacity: 0;
            -moz-transform: translateY(6em)
        }
    }

    @keyframes mw-kfr-fadeIn {
        0% {
            display: block;
            opacity: 0;
            -webkit-transform: translateY(6em);
            -moz-transform: translateY(6em);
            transform: translateY(6em)
        }
        to {
            display: block;
            opacity: 1;
            -webkit-transform: translateY(0);
            -moz-transform: translateY(0);
            transform: translateY(0)
        }
    }

    @keyframes mw-kfr-fadeOut {
        0% {
            display: block;
            opacity: 1;
            -webkit-transform: translateY(0);
            -moz-transform: translateY(0);
            transform: translateY(0)
        }
        to {
            display: none;
            opacity: 0;
            -webkit-transform: translateY(6em);
            -moz-transform: translateY(6em);
            transform: translateY(6em)
        }
    }

    body.official-page {
        margin: 0;
        padding: 0;
        height: 100%;
        width: 100%;
        font-family: Helvetica Neue, Helvetica, Arial, sans-serif
    }

    body.official-page a,
    body.official-page a:active,
    body.official-page a:visited {
        text-decoration: none
    }

    body.official-page * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box
    }

    body.official-page h1,
    body.official-page h2,
    body.official-page h3 {
        color: #444
    }

    body.official-page .mw-page {
        display: none;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        padding: 2em;
        font-family: PingFang SC, Helvetica Neue, Hragino Sans GB, Helvetica, Microsoft YaHei, Arial;
        text-align: center;
        position: fixed;
        z-index: 1;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #fff;
        -webkit-background-size: cover;
        background-size: cover;
        background-position: bottom;
        background-repeat: no-repeat
    }

    body.official-page .mw-page h4 {
        font-size: .8em
    }

    body.official-page .mw-page.hide {
        display: none
    }

    body.official-page .mw-page.hidding {
        -webkit-animation-name: mw-kfr-fadeOut;
        -webkit-animation-duration: .3s;
        -webkit-animation-iteration-count: 1;
        -webkit-animation-timing-function: ease-in-out;
        -webkit-animation-fill-mode: forwards;
        -moz-animation-name: mw-kfr-fadeOut;
        -moz-animation-duration: .3s;
        -moz-animation-iteration-count: 1;
        -moz-animation-timing-function: ease-in-out;
        -moz-animation-fill-mode: forwards;
        -o-animation-name: mw-kfr-fadeOut;
        -o-animation-duration: .3s;
        -o-animation-iteration-count: 1;
        -o-animation-timing-function: ease-in-out;
        -o-animation-fill-mode: forwards;
        -ms-animation-name: mw-kfr-fadeOut;
        -ms-animation-duration: .3s;
        -ms-animation-iteration-count: 1;
        -ms-animation-timing-function: ease-in-out;
        -ms-animation-fill-mode: forwards;
        animation-name: mw-kfr-fadeOut;
        animation-duration: .3s;
        animation-iteration-count: 1;
        animation-timing-function: ease-in-out;
        animation-fill-mode: forwards
    }

    body.official-page .mw-page.show {
        display: block;
        opacity: 0;
        -webkit-animation-name: mw-kfr-fadeIn;
        -webkit-animation-duration: .3s;
        -webkit-animation-iteration-count: 1;
        -webkit-animation-timing-function: ease-in-out;
        -webkit-animation-fill-mode: forwards;
        -moz-animation-name: mw-kfr-fadeIn;
        -moz-animation-duration: .3s;
        -moz-animation-iteration-count: 1;
        -moz-animation-timing-function: ease-in-out;
        -moz-animation-fill-mode: forwards;
        -o-animation-name: mw-kfr-fadeIn;
        -o-animation-duration: .3s;
        -o-animation-iteration-count: 1;
        -o-animation-timing-function: ease-in-out;
        -o-animation-fill-mode: forwards;
        -ms-animation-name: mw-kfr-fadeIn;
        -ms-animation-duration: .3s;
        -ms-animation-iteration-count: 1;
        -ms-animation-timing-function: ease-in-out;
        -ms-animation-fill-mode: forwards;
        animation-name: mw-kfr-fadeIn;
        animation-duration: .3s;
        animation-iteration-count: 1;
        animation-timing-function: ease-in-out;
        animation-fill-mode: forwards
    }

    body.official-page .mw-page.loading {
        background-image: url(https://a.yzbo.tv/scripts/dist/4612ae.png)
    }

    body.official-page .mw-page.loading h3 {
        position: absolute;
        top: 40%;
        left: 0;
        width: 100%;
        text-align: center
    }

    body.official-page .mw-page.loading.show .rain-progress>span {
        -webkit-animation-name: mw-kfr-loading;
        -moz-animation-name: mw-kfr-loading;
        animation-name: mw-kfr-loading;
        -webkit-animation-iteration-count: infinite;
        -moz-animation-iteration-count: infinite;
        animation-iteration-count: infinite
    }

    body.official-page .mw-page.loading .rain-progress {
        -webkit-transform: rotate(135deg) translate(-200px, 200px);
        -moz-transform: rotate(135deg) translate(-200px, 200px);
        -ms-transform: rotate(135deg) translate(-200px, 200px);
        transform: rotate(135deg) translate(-200px, 200px);
        opacity: .5
    }

    body.official-page .mw-page.loading .rain-progress>span {
        display: block;
        height: 10px;
        -webkit-border-radius: 50px;
        border-radius: 50px;
        overflow: hidden;
        margin-bottom: 5em
    }

    body.official-page .mw-page.loading .rain-progress>span:first-child {
        -webkit-animation-duration: 2s;
        -moz-animation-duration: 2s;
        animation-duration: 2s;
        background-color: #00c;
        width: 50px
    }

    body.official-page .mw-page.loading .rain-progress>span:nth-child(2) {
        -webkit-animation-duration: .8s;
        -moz-animation-duration: .8s;
        animation-duration: .8s;
        background-color: red;
        width: 20px
    }

    body.official-page .mw-page.loading .rain-progress>span:nth-child(3) {
        -webkit-animation-duration: 1.6s;
        -moz-animation-duration: 1.6s;
        animation-duration: 1.6s;
        background-color: #9acd32;
        width: 60px
    }

    body.official-page .mw-page.loading .rain-progress>span:nth-child(4) {
        -webkit-animation-duration: .6s;
        -moz-animation-duration: .6s;
        animation-duration: .6s;
        background-color: orange;
        width: 100px
    }
dl {
        display: block;
        -webkit-margin-before: 1em;
        -webkit-margin-after: 1em;
        -webkit-margin-start: 0px;
        -webkit-margin-end: 0px;
    }

    html,
    div,
    span,
    p,
    a,
    img,
    i,
    dl,
    dt,
    dd,
    ul,
    li {
        margin: 0;
        padding: 0;
        border: 0;
        font: inherit;
        font-size: 100%;
        vertical-align: baseline;
    }

    .sku-sel-list:after {
        content: '';
        display: table;
        clear: both;
    }

    div {
        font: inherit;
        display: block;
        cursor: pointer;
    }

    .trolley {
        overflow: hidden;
        position: absolute;
        z-index: 1000px;
        background: #fff;
        bottom: 0px;
        left: 0px;
        right: 0px;
        visibility: visible;
        opacity: 1;
        box-shadow: 0 -1px 14px rgba(0, 0, 0, .9);
    }

    .main {
        border-bottom-width: 1px;
        border-bottom: 1px solid #dedede;
        position: static;
        padding: 10px 0;
        margin-left: 10px;
        width: auto;
        overflow: hidden;
    }

    .img {
        width: 50px;
        height: 50px;
        border: 1px solid #dedede;
        border-radius: 2px;
        float: left;
        margin-left: auto;
        margin-right: auto;
        background-size: cover;
        position: relative;
        overflow: hidden;
    }

    .img img {
        position: absolute;
        margin: auto;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 100%;
    }

    .details {
        margin-left: 60px;
        width: auto;
        position: relative;
        zoom: 1;
    }

    .name {
        font: inherit;
        padding-right: 52px;
        margin: 0 0 5px 0;
        font-size: 14px;
        line-height: 22px;
        position: relative;
        white-space: nowrap;
        overflow: hidden;
    }

    .price {
        padding: 0 55px 0 0;
        margin: 0;
        border: 0;
        font: inherit;
        font-size: 100%;
        vertical-align: baseline;
    }

    .clearfix {
        zoom: 1;
    }

    .clearfix:after {
        content: '';
        display: table;
        clear: both;
    }

    .price-cn {
        line-height: 20px;
        float: left;
    }

    .yuan {
        padding-top: 1px;
        float: left;
        font-size: 14px;
        color: #f60;
        font: inherit;
    }

    .number {
        font: inherit;
        vertical-align: middle;
        font-size: 14px;
        color: #f60;
    }

    .content {
        max-height: 541px;
        border: none;
        overflow-y: scroll;
        line-height: 20px;
        background-color: #f8f8f8;
        margin: 0;
        padding: 0;
        font: inherit;
        font-size: 100%;
        vertical-align: baseline;
    }

    .goods-models {
        margin: 0 0 10px 0;
        padding: 0 0 0 10px;
        list-style: none;
        font-size: 14px;
        border-top: 0;
        border-bottom: 1px solid #dedede;
        background-color: #fff;
        position: relative;
        overflow: hidden;
    }

    .classes {
        padding: 10px 0 0;
        border: 0;
        position: relative;
        line-height: 1.5;
        overflow: hidden;
        display: block;
        font: inherit;
        font-size: 100%;
        vertical-align: baseline;
    }

    .model {
        margin-bottom: 10px;
        font-size: 13px;
        cursor: pointer;
    }

    .sku-sel-list {
        zoom: 1;
        padding-left: 0;
        margin-bottom: 0;
    }

    .model-list {
        padding-right: 50px;
    }

    .tag {
        position: relative;
        margin: 0 10px 10px 0;
        min-width: 32px;
        max-width: 180px;
        line-height: 16px;
        padding: 5px 9px;
        color: #333;
        background-color: transparent;
        border: 1px solid #999;
        border-radius: 3px;
        font-size: 12px;
        display: inline-block;
        text-align: center;
        float: left;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }

    .count {
        border-top: 1px solid #dedede;
        padding: 10px 10px 10px 0;
        position: relative;
        line-height: 1.5;
        overflow: hidden;
        display: block;
    }

    .count-cn {
        line-height: 29px;
        float: left;
        display: block;
    }

    .quantity {}

    .quantity,
    .quantity .txt,
    .quantity button,
    .trolley .vertical-middle {
        vertical-align: middle
    }

    .quantity .txt,
    .quantity button {
        text-align: center;
        margin: 0
    }

    .quantity .txt {
        -webkit-tap-highlight-color: transparent
    }

    .quantity,
    .quantity .minus {
        position: relative
    }

    .quantity {
        display: inline-block
    }

    .quantity {
        font-size: 0
    }

    .quantity input[type=number]::-webkit-outer-spin-button {
        margin: 0
    }

    .quantity button {
        border: 2px solid #eee;
        font-size: 16px;
        line-height: 10px;
        font-weight: 700;
        color: #666;
        padding: 5px;
        outline: 0!important;
        width: 26px;
        height: 30px;
        text-indent: -9999px;
        overflow: hidden
    }

    .quantity .txt {
        font-size: 14px;
        width: 24px;
        height: 18px;
        border-radius: 0
    }

    .quantity .minus::before,
    .quantity .plus::before {
        width: 8px;
        height: 2px;
        top: 0;
        left: 0;
        right: 0;
        margin: auto;
        background-color: #6c6c6c;
        bottom: 0;
        content: ''
    }

    .quantity .txt:focus {
        border-color: #eee
    }

    .quantity .minus {
        border-radius: 4px 0 0 4px;
        border-right: 0 none
    }

    .quantity .minus::before {
        position: absolute
    }

    .quantity .plus {
        position: relative;
        border-left: 0 none;
        border-radius: 0 4px 4px 0
    }

    .quantity .plus::before {
        position: absolute
    }

    .quantity .plus::after {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
        content: '';
        width: 2px;
        height: 8px;
        background-color: #6c6c6c
    }

    .quantity .minus.disabled::before,
    .quantity .plus.disabled::after,
    .quantity .plus.disabled::before {
        background-color: #ddd
    }

    .quantity .response-area {
        width: 42px;
        height: 42px;
        top: -7px;
        position: absolute
    }

    .quantity .response-area-plus {
        right: -5px
    }

    .quantity .response-area-minus {
        left: -5px
    }

    .trolley .quantity {
        float: right
    }

    .trolley .quantity .minus {
        border-radius: 2px 0 0 2px
    }

    .trolley .quantity .minus.disabled {
        background-color: #f8f8f8;
        border-color: #e8e8e8 #999 #e8e8e8 #e8e8e8
    }

    .trolley .quantity .minus.disabled::before {
        background-color: #bbb
    }

    .trolley .quantity .plus {
        border-radius: 0 2px 2px 0
    }

    .trolley .quantity .plus.disabled {
        background-color: #f8f8f8;
        border-color: #e8e8e8 #e8e8e8 #e8e8e8 #999
    }

    .trolley .quantity .plus.disabled::after,
    .trolley .quantity .plus.disabled::before {
        background-color: #bbb
    }

    .trolley .quantity .txt {
        width: 33px;
        height: 25px;
        padding: 1px;
        border: 1px solid #999;
        border-width: 1px 0;
        -webkit-box-sizing: content-box;
        -moz-box-sizing: content-box;
        box-sizing: content-box;
        color: #666
    }

    .trolley .quantity .txtCover {
        position: absolute;
        top: 0;
        left: 37px;
        bottom: 0;
        right: 37px
    }

    .trolley .quantity .minus,
    .trolley .quantity .plus {
        width: 37px;
        height: 29px;
        background-color: #fff;
        border: 1px solid #999
    }

    .trolley .quantity .minus::before,
    .trolley .quantity .plus::before {
        height: 1px;
        width: 9px;
        background-color: #666
    }

    .trolley .quantity .minus::after,
    .trolley .quantity .plus::after {
        width: 1px;
        height: 9px;
        background-color: #666
    }

    .inventory {
        line-height: 14px;
        cursor: pointer;
    }

    .inventory-num {
        display: inline-block;
        margin-right: 10px;
        font-size: 12px;
        cursor: pointer;
    }

    .foot {
        padding: 0;
        font-size: 0;
        width: 100%;
        zoom: 1;
        cursor: pointer;
    }

    .two-btn {
        font-size: 0;
        overflow: hidden;
        text-align: center;
        cursor: pointer;
    }

    .orange-btn::after {
        content: "";
        position: absolute;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        top: 0;
        left: 0;
        box-sizing: border-box;
        width: 200%;
        height: 200%;
        -webkit-transform: scale(.5);
        -moz-transform: scale(.5);
        -ms-transform: scale(.5);
        transform: scale(.5);
        -webkit-transform-origin: left top;
        -moz-transform-origin: left top;
        -ms-transform-origin: left top;
        transform-origin: left top;
        pointer-events: none;
        border-top: 1px solid #E57A4C;
    }

    .orange-btn {
        width: 50%;
        padding: 0;
        border: none;
        vertical-align: top;
        height: 50px;
        float: left;
        background: #f85;
        color: #fff;
        position: relative;
        text-decoration: none;
        margin: 0;
        font: inherit;
        font-size: 16px;
        line-height: 50px;
    }

    .red-btn::after {
        content: "";
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        width: 200%;
        height: 200%;
        -webkit-transform: scale(.5);
        -moz-transform: scale(.5);
        -ms-transform: scale(.5);
        pointer-events: none;
        top: 0;
        left: 0;
    }

    .red-btn::after {
        position: absolute;
        box-sizing: border-box;
        transform: scale(.5);
        -webkit-transform-origin: left top;
        -moz-transform-origin: left top;
        -ms-transform-origin: left top;
        transform-origin: left top;
        border-top: 1px solid #E53D3D;
    }

    .red-btn {
        width: 50%;
        padding: 0;
        border: none;
        vertical-align: top;
        height: 50px;
        float: left;
        background: #f44;
        color: #fff;
        position: relative;
        text-decoration: none;
        margin: 0;
        font: inherit;
        font-size: 16px;
        line-height: 50px;
    }

    .cancel {
        position: absolute;
        right: 0;
        top: 0;
        padding: 10px;
    }

    .cancel-img {
        height: 22px;
        width: 22px;
        background-image: url(https://b.yzcdn.cn/v2/image/wap/sku/icon_close.png);
        background-size: 22px 22px;
    }

    .active {
        color: #fff;
        background-color: #f60;
        border-color: #f60;
    }
    body.official-page .mw-page.loading .rain-progress>span:nth-child(5) {
        -webkit-animation-duration: 1.2s;
        -moz-animation-duration: 1.2s;
        animation-duration: 1.2s;
        background-color: #ff4500;
        width: 40px
    }

    body.official-page .mw-page.loading .rain-progress>span:nth-child(6) {
        -webkit-animation-duration: 2.4s;
        -moz-animation-duration: 2.4s;
        animation-duration: 2.4s;
        background-color: plum;
        width: 120px
    }

    body.official-page .mw-page.loading .rain-progress>span:nth-child(7) {
        -webkit-animation-duration: 1.6s;
        -moz-animation-duration: 1.6s;
        animation-duration: 1.6s;
        background-color: #9acd32;
        width: 60px
    }

    body.official-page .mw-page.loading .rain-progress>span:nth-child(8) {
        -webkit-animation-duration: .6s;
        -moz-animation-duration: .6s;
        animation-duration: .6s;
        background-color: orange;
        width: 100px
    }

    body.official-page .mw-page.loading .rain-progress>span:nth-child(9) {
        -webkit-animation-duration: 1.2s;
        -moz-animation-duration: 1.2s;
        animation-duration: 1.2s;
        background-color: #ff4500;
        width: 40px
    }

    body.official-page .mw-page.loading .rain-progress>span:nth-child(10) {
        -webkit-animation-duration: 2.8s;
        -moz-animation-duration: 2.8s;
        animation-duration: 2.8s;
        background-color: plum;
        width: 120px
    }

    body.official-page .mw-page.scheme-notsupported,
    body.official-page .mw-page.server-error {
        background-image: url(https://a.yzbo.tv/scripts/dist/14ff85.png)
    }

    body.official-page .mw-page.scheme-redirecting {
        background-image: url(https://a.yzbo.tv/scripts/dist/298bac.png)
    }

    body.official-page .mw-page.redirect-with-button {
        background-image: url(https://a.yzbo.tv/scripts/dist/4612ae.png)
    }

    body.official-page .mw-page.redirect-with-button.default .app-icon:after {
        display: block
    }

    body.official-page .mw-page.redirect-with-button .app-icon {
        overflow: visible;
        position: relative;
        margin-bottom: 3em;
        background-color: transparent!important;
        width: 7em;
        height: 7em
    }

    body.official-page .mw-page.redirect-with-button .app-icon img {
        -webkit-border-radius: 1em;
        border-radius: 1em;
        overflow: hidden;
        position: relative;
        z-index: 2;
        -webkit-box-shadow: 0 5px 0 rgba(0, 0, 0, .1);
        box-shadow: 0 5px 0 rgba(0, 0, 0, .1)
    }

    body.official-page .mw-page.redirect-with-button .app-icon:after {
        content: " ";
        display: none;
        position: absolute;
        left: -3em;
        top: -3em;
        right: -3em;
        bottom: -3em;
        background-image: url(https://a.yzbo.tv/scripts/dist/70b816.png);
        background-repeat: no-repeat;
        background-position: 50%;
        -webkit-background-size: contain;
        background-size: contain
    }

    body.official-page .mw-page.setting-error {
        background-image: url(https://a.yzbo.tv/scripts/dist/770543.png)
    }

    body.official-page .mw-page.setting-error .mw-page-cont {
        position: absolute;
        bottom: 4em;
        left: 0;
        right: 0
    }

    body.official-page .mw-page.custom-content {
        padding: 0!important;
        z-index: 10002
    }

    body.official-page .mw-page.custom-content .mw-page-cont {
        padding-top: 80px;
        height: 100%;
        overflow: auto
    }

    body.official-page .mw-page.custom-content .mw-page-cont h1 {
        text-align: center;
        font-size: 1.25em;
        line-height: 1.1em;
        margin: 0;
        font-weight: 400;
        border-bottom: 0 solid #eee;
        padding: 1em .7em;
        color: #333
    }

    body.official-page .mw-page.custom-content .mw-page-cont .mw-cc-banner {
        background-repeat: no-repeat;
        background-position: 50%;
        -webkit-background-size: cover;
        background-size: cover;
        max-height: 50%;
        margin: 0;
        padding: 0;
        position: relative
    }

    body.official-page .mw-page.custom-content .mw-page-cont .mw-cc-banner img {
        width: 100%;
        opacity: .001
    }

    body.official-page .mw-page.custom-content .mw-page-cont .mw-cc-banner a:after {
        content: "\53BB\770B\770B";
        display: block;
        position: absolute;
        z-index: 2;
        right: 24px;
        bottom: 24px;
        background-color: rgba(113, 149, 251, .7);
        color: #fff;
        font-size: 14px;
        font-weight: 400;
        padding: 5px 24px
    }

    body.official-page .mw-page.custom-content .mw-page-cont .content {
        margin: 0;
        padding: 32px 1em 1em;
        text-align: left;
        line-height: 1.5em;
        color: #666
    }

    body.official-page .mw-page.custom-content .mw-page-footer {
        position: fixed;
        z-index: 10002;
        height: 80px;
        background-color: hsla(0, 0%, 100%, .9);
        top: 0;
        bottom: auto;
        -webkit-box-shadow: 0 0 0 transparent;
        box-shadow: 0 0 0 transparent;
        text-align: left;
        padding: 20px 24px
    }

    body.official-page .mw-page.custom-content .mw-page-footer>* {
        display: inline-block;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        vertical-align: middle
    }

    body.official-page .mw-page.custom-content .mw-page-footer .app-info {
        text-align: left;
        overflow: auto;
        padding: 0;
        margin-top: 0;
        width: 60%;
        white-space: nowrap;
        word-break: keep-all
    }

    body.official-page .mw-page.custom-content .mw-page-footer .app-info,
    body.official-page .mw-page.custom-content .mw-page-footer .app-info * {
        vertical-align: middle
    }

    body.official-page .mw-page.custom-content .mw-page-footer .app-info .app-icon {
        display: inline-block;
        width: 40px;
        height: 40px;
        margin: 0 .5em 0 0
    }

    body.official-page .mw-page.custom-content .mw-page-footer .app-info .app-name {
        display: inline-block;
        margin: 0;
        white-space: nowrap;
        word-break: keep-all;
        overflow: hidden;
        -o-text-overflow: ellipsis;
        text-overflow: ellipsis
    }

    body.official-page .mw-page.custom-content .mw-page-footer .btns {
        float: right;
        text-align: right;
        padding: 0
    }

    body.official-page .mw-page.custom-content .mw-page-footer .btns .mw-button {
        padding: 6px 14px;
        font-size: 14px;
        -webkit-border-radius: 0;
        border-radius: 0;
        margin: 0!important
    }

    body.official-page .mw-page.universal-link-download {
        background-image: url(https://a.yzbo.tv/scripts/dist/4612ae.png)
    }

    body.official-page .mw-page.universal-link-download .mw-ul-slidedown {
        text-align: left;
        margin: 1em 0 0 -.5em;
        padding: 0 2em 0 0
    }

    body.official-page .mw-page.universal-link-download p {
        margin-top: 2em;
        text-align: left;
        color: #888;
        font-size: 1em
    }

    body.official-page .mw-page.universal-link-download ol {
        padding: 0 0 0 1em;
        margin-top: .5em
    }

    body.official-page .mw-page.universal-link-download ol li {
        text-align: left;
        font-size: .8em;
        color: #aaa
    }

    body.official-page .mw-page.mw-alert {
        background-image: url(https://a.yzbo.tv/scripts/dist/4612ae.png)
    }

    body.official-page .mw-page.openinbrowser {
        background-image: none
    }

    body.official-page .mw-page.openinbrowser.default .logo {
        display: block
    }

    body.official-page .mw-page.openinbrowser.default .tips {
        bottom: 3.2em;
        -webkit-background-size: contain;
        background-size: contain
    }

    body.official-page .mw-page.openinbrowser .tips {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        right: 0;
        -webkit-background-size: cover;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: top
    }

    body.official-page .mw-page.openinbrowser .logo {
        width: 4em;
        height: 4em;
        position: absolute;
        bottom: 8em;
        left: 50%;
        margin-left: -1em;
        -webkit-border-radius: 8px;
        border-radius: 8px;
        overflow: hidden;
        display: none
    }

    body.official-page .mw-page.openinbrowser.ios .tips {
        background-image: url(https://a.yzbo.tv/scripts/dist/882428.png)
    }

    body.official-page .mw-page.openinbrowser.android .tips {
        background-image: url(https://a.yzbo.tv/scripts/dist/58c8d0.png)
    }

    body.official-page .mw-page .mw-page-footer {
        position: absolute;
        bottom: 4em;
        left: 0;
        right: 0;
        text-align: center
    }

    body.official-page .mw-page .blue {
        color: #a5c5f6;
        text-shadow: 0 1px 2px hsla(0, 0%, 100%, .2)
    }

    body.official-page .mw-page .mw-title,
    body.official-page .mw-page h3 {
        color: #656565
    }

    body.official-page .mw-page p {
        line-height: 1.4em;
        padding: 0;
        margin: 0
    }

    body.official-page .mw-page .tips {
        font-size: .8em;
        line-height: 1.5em
    }

    body.official-page .mw-page .mw-page-cont.table {
        display: table;
        vertical-align: middle;
        height: 100%;
        width: 100%
    }

    body.official-page .mw-page .mw-page-cont.table .middle {
        display: table-cell!important;
        vertical-align: middle
    }

    body.official-page .mw-page .mw-ul-slidedown {
        position: fixed;
        top: 0;
        width: 100%;
        padding: 1.5em 2em;
        font-size: 1.1em;
        line-height: 1.2em;
        color: #6bacec;
        z-index: 1
    }

    body.official-page .mw-page .mw-ul-slidedown .text {
        padding-right: 48px
    }

    body.official-page .mw-page .mw-ul-slidedown .icon-dropdown {
        position: absolute;
        top: 1em;
        right: 2em;
        left: auto;
        width: 3em;
        height: 3em;
        background-image: url(https://a.yzbo.tv/scripts/dist/fea094.png);
        background-repeat: no-repeat;
        -webkit-background-size: contain;
        background-size: contain;
        background-position: 50%;
        -webkit-animation: mw-kfr-slidedown linear 2.5s infinite;
        -moz-animation: mw-kfr-slidedown linear 2.5s infinite;
        animation: mw-kfr-slidedown linear 2.5s infinite
    }

    body.official-page .mw-page .app-icon {
        margin: auto auto 1em;
        width: 8em;
        height: 8em;
        -webkit-border-radius: 10px;
        border-radius: 10px;
        overflow: hidden;
        background-image: url(https://a.yzbo.tv/scripts/dist/3df1bd.png);
        -webkit-background-size: contain;
        background-size: contain;
        background-repeat: no-repeat
    }

    body.official-page .mw-page .app-icon img {
        width: 100%;
        background-color: #fff
    }

    body.official-page footer {
        display: block!important;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 24px;
        text-align: center;
        font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
        font-size: 12px;
        z-index: 10001;
        color: #999
    }

    body.official-page footer a {
        color: #666!important
    }

    body.official-page #mw-navigator-modal {
        background-color: transparent!important
    }

    body .mw-button {
        display: inline-block;
        margin: 2em 1em 1em;
        background-color: #7195fb;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        padding: .6em 1.2em;
        font-size: 1.1em;
        color: #fff;
        text-decoration: none;
        font-weight: 400
    }

    body .mw-button.outline {
        border: 2px solid #7195fb!important;
        background-color: transparent!important;
        color: #7195fb!important
    }

    body .mw-button.no-margin {
        margin: .5em
    }

    body .mw-button.min {
        padding: .4em .8em
    }

    body .mw-dialog {
        position: fixed;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        height: 100%;
        width: 100%;
        background-color: rgba(0, 0, 0, .6);
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        z-index: 99999999999;
        display: -ms-flex;
        display: -webkit-flex;
        display: -webkit-box;
        display: -ms-flexbox;
        display: -moz-box;
        display: flex;
        -ms-flex-align: center;
        -webkit-align-items: center;
        -webkit-box-align: center;
        -moz-box-align: center;
        align-items: center;
        -webkit-justify-content: space-between;
        -ms-flex-pack: justify;
        -webkit-box-pack: justify;
        -moz-box-pack: justify;
        justify-content: space-between
    }

    body .mw-dialog>div {
        display: block;
        width: 100%
    }

    body .mw-dialog .mw-close {
        width: 80px;
        height: 80px;
        -webkit-border-radius: 50%;
        border-radius: 50%;
        display: block;
        margin: 2em auto 0;
        background: rgba(0, 0, 0, .4);
        padding: 1.1em;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        border: 2px solid #fff
    }

    body .mw-dialog .mw-close:after,
    body .mw-dialog .mw-close:before {
        display: block;
        content: " ";
        height: 0;
        width: 100%;
        border-bottom: 2px solid hsla(0, 0%, 100%, .8);
        -webkit-transform: rotate(45deg);
        -moz-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
        position: relative;
        top: 50%
    }

    body .mw-dialog .mw-close:after {
        -webkit-transform: rotate(-45deg);
        -moz-transform: rotate(-45deg);
        -ms-transform: rotate(-45deg);
        transform: rotate(-45deg)
    }

    body .mw-dialog .mw-dialog-container {
        max-width: 640px;
        overflow: auto;
        max-height: 100%;
        width: 80%;
        padding: 2em 1em;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        font-size: 14px;
        text-align: center;
        border: 2px solid #fff;
        -webkit-border-radius: 1em!important;
        border-radius: 1em!important;
        background-color: #fff;
        color: #666;
        margin: auto;
        -webkit-box-shadow: 2px 2px 3em rgba(0, 0, 0, .2);
        box-shadow: 2px 2px 3em rgba(0, 0, 0, .2)
    }

    body .mw-dialog .mw-dialog-container .mw-app-icon {
        width: 6em;
        height: 6em
    }

    body .mw-dialog .mw-dialog-container .mw-app-name {
        font-size: 1em;
        margin-bottom: 2em;
        color: #000
    }

    body .mw-dialog .mw-dialog-container .mw-button {
        font-size: 1.5em;
        padding: 1em 2em
    }

    @media screen and (max-width:320px) {
        body.official-page {
            font-size: 12px
        }
        body.official-page.ulink-page header {
            padding: .7em 1.2em;
            font-size: .8em
        }
        body .mw-dialog .mw-dialog-container {
            font-size: 1em
        }
    }

    .mw-qrcode {
        position: fixed;
        z-index: 10000;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background-color: rgba(0, 0, 0, .8);
        -webkit-background-size: 90% auto;
        background-size: 90% auto;
        background-repeat: no-repeat;
        background-position: 100% 0;
        overflow: auto;
        text-align: center
    }

    .mw-qrcode h1 {
        font-size: 18px;
        text-align: center;
        margin: 100px auto 24px;
        color: #666
    }

    .mw-qrcode img {
        max-width: 160px;
        border: 1px solid #eee;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        padding: 10px
    }
    .w-share{
        height: .91rem;
    }

    .subscribe{
        width: 1.3rem;
        margin-top: .133333rem;
        margin-bottom: .133333rem;
        padding: 1.5px 3px;
        text-align: center;
        font-size: .293333rem;
        color: #f15e1f;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 1px 1px rgba(0,0,0,0.7);
    }

    .subscribe-done,
    .interdiction{
        position: fixed;
        height: 24px;
        line-height: 24px;
        text-align: center;
        vertical-align: middle;
        margin: auto;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        font-size: .5rem;
        color: #fff;
        padding: 8px 18px;
        border-radius: 10px;
        border-color: #000;
        border-width: 2px;
        background-color: rgba(127,127,127,0.5);
    }
    .subscribe-done{
        width: 4rem;
    }
    .interdiction{
        width: 4rem;
    }

    .otw{
        position: absolute;
        top: 1.55rem;
        left: .45rem;
        /*width: 4.56rem;*/
        /*height: .4rem;*/
    }

    .info{
        padding: 5px;
        border-radius: 12px;
    }
    .otw .info{
        font-size: .32rem;
        color: #fff;
        line-height: .32rem;
        vertical-align: middle;
        background: -webkit-gradient(linear, 0 0, 100% 0, from(#ffcc66), to(#cc6633));
        display: block;
        height: 100%;
        box-shadow: 1px 1px rgba(0,0,0,0.7);
        padding-left: 10px;
        float: left;
    }
    #content::-webkit-input-placeholder{
        color: #fff;
    }
    #content:-moz-placeholder{
        color: #fff;
    }
    #content::-moz-placeholder{
        color: #fff;
    }
    #conteng:-ms-input-placeholder{
        color: #fff;
    }
    .yellow{
        position: fixed;
        top: 0;
        left: 0;
        width: 10rem;
        height: 100%;
        z-index: 1000;
        background-color: rgb(0,0,0);
    }
    .line{
        position: absolute;
        width: 92%;
        height: 93%;
        margin-left: 4%;
        margin-top: 3.5%;
        border: 2px solid #33f6ff;
        top: 0;
        left: 0;
        z-index: 1000000000;
    }
    .content-box .room-chat-messages .room-chat-item .enter{
        width: 100%;
        height: 100%;
    }
    .wrap-enter{
        height: 100%;
    }
    .room-chat-item .enter .wrap-enter span{
        font-size: .35rem;
        text-shadow:1px 1px rgba(0,0,0,.7);
        padding-left: 0;
        color: #fc9;
        padding-right: 0;
    }
    .room-chat-content{
        color: #fff;
        line-height: .6rem;
        word-break:break-all;
    }
    .couponname{
    position: fixed;
    display: block;
    color: #fff;
    /*font-family: SimHei;*/
    width: 5.4rem;
    text-align: right;
    top: 37%;
    /*font-size: 1rem;*/
    left: 1.5rem;
    text-shadow: 1px 1px rgba(0,0,0,.7);
    text-align: center;
    z-index: 20;
    }
    .room-chat-subscribe{
        color: #f16124 !important;
        vertical-align: middle;
    }
    .room-chat-otw{
        vertical-align: middle;
        color: #f179be !important;
    }
    .room-chat-bought{
        vertical-align: middle;
        color:#f53c3c !important;
    }
    .pretime{
        position: absolute;
        width: 100%;
        height: 30px;
        font-size: 17px;
        color: white;
        top: 35%;
        z-index: 1;
        text-align: center;
    }
    .sns-hd{
        height: 15px;
        border-bottom: 1px solid #dedede;
        position: relative;
        top: 1px;
        text-align: left;
        color: #666666;
        font-size: 12px;
        padding:8px;
        line-height: 15px;
        z-index: 1;
    }
    .goods-count{
        display: inline-flex;
        height: 100%;
        vertical-align: middle;
        line-height: 15px;
        position: relative;
        top: -1px;
    }
    .goods-count span{
        margin-left: 5px;
        margin-top: 1px;
    }
    /*.barragebtn{
        float: right;
        margin-left: 5px;
    font-size: 21px;
    }
    .barrageoff{
    width: 15px;
    position: relative;
    left: -10px;
    display: none;
    }*/
    .room-chat-item .item-ctn span{
        font-size: .35rem;
    }
    .switch-btn {
        position: absolute;
        top: 1.56rem;
        right: 2.6rem;
        display: block;
        vertical-align: top;
        width: 65px;
        height: 20px;
        border-radius: 10px;
        cursor: pointer;
        z-index: 5;
    }
    .checked-switch {
        position: absolute;
        top: 0;
        left: 0;
        opacity: 0;
    }
    .text-switch {
        background-color: #ed5b49;
        border: 1px solid #d2402e;
        border-radius: inherit;
        color: #fff;
        display: block;
        font-size: 12px;
        height: inherit;
        position: relative;
        text-transform: uppercase;
    }
    .text-switch:before, .text-switch:after {
        position: absolute;
        top: 50%;
        margin-top: -5px;
        line-height: 1;
        -webkit-transition: inherit;
        -moz-transition: inherit;
        -o-transition: inherit;
        transition: inherit;
    }
    .text-switch:before {
        content: attr(data-no);
        right: 5px;
    }
    .text-switch:after {
        content: attr(data-yes);
        left: 5px;
        color: #FFFFFF;
        opacity: 0;
    }
    .checked-switch:checked ~ .text-switch {
        background-color: #00af2c;
        border: 1px solid #068506;
    }
    .checked-switch:checked ~ .text-switch:before {
        opacity: 0;
    }
    .checked-switch:checked ~ .text-switch:after {
        opacity: 1;
    }
    .toggle-btn {
        background: linear-gradient(#eee, #fafafa);
        border-radius: 100%;
        height: 20px;
        left: 1px;
        position: absolute;
        top: 1px;
        width: 20px;
    }
.checked-switch:checked ~ .toggle-btn {left: 44px;}
 .text-switch, .toggle-btn {transition: All 0.3s ease; -webkit-transition: All 0.3s ease; -moz-transition: All 0.3s ease; -o-transition: All 0.3s ease;}
    /*.likeimg{
        position: absolute; bottom: 70px;right: 23.5px;
    }*/
    .likeimg{
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }
    .likeimg img{
        width: .533333rem;
        height: .533333rem;
        position: absolute;
        bottom: 1.96667rem;
        right: .626667rem;
    }
    .like{
        width: .64rem;
        height: .64rem;
        border:0;
        color: #fff;
        position: absolute;
        padding: .213333rem;
        border-radius: 50%;
        background-color: rgba(0,0,0,0);
        position:absolute;
        right:.36rem;
        bottom:1.7rem;
    }
    .likenum{
        font-size: 12px;
        color: #fff;
        background-color: red;
        padding: .053333rem .133333rem;
        border-radius: 10px;
        bottom: 0;
        display: inline-block;
        line-height: 12px;
    }
    .likenumbox{
        position: absolute;
        bottom: 2.61333rem;
        right: .36rem;
        width: 1.06667rem;
        text-align: center;
        z-index: 1;
        line-height: 0;
    }
    .likeicon{
        position: absolute;
        bottom: .213333rem;
        right: .213333rem;
        width: .64rem;
    }
    .banner{display:block;width:90%;margin-left:100%;margin-bottom: 20px;
        position: fixed;
        top: 0;
        z-index: 6;
    }
    .banner .turnplate{display:block;width:100%;position:relative;}
    .banner .turnplate canvas.item{width:100%;}
    .banner .turnplate img.pointer{position:absolute;width:36%;height:40%;left:32%;top:28%;}

    #mark{width: 100%;height: 100%;background: rgba(0,0,0,0.5);position: fixed;top: 0;left: 0;display: none;
        z-index: 7;
    }
    #mark2{width: 100%;height: 100%;background: rgba(0,0,0,0.5);position: fixed;top: 0;left: 0;display: none;
        z-index: 7;
    }
    #huodongguize{width: 100%;height: 100%;background: rgba(0,0,0,0.5);position: fixed;top: 0;left: 0;display: none;
        z-index: 7;
    }
    .huojiangwenzi{
    color: #fff;
    font-size: 16px;
    position: fixed;
    left: 50%;
    margin-left: -25%;
    text-align: center;
    top: 60%;
    width: 50%;
    word-break:break-all;
}
    .meizhongwenzi{
    color: #fff;
    font-size: 16px;
    position: fixed;
    left: 50%;
    margin-left: -25%;
    text-align: center;
    top: 50%;
    width: 50%;
    word-break:break-all;
}
    .red-img{position: fixed;top: 10%;left: 5%;width: 90%;}
    .platebtn{
        writing-mode:tb-rl;
    position: absolute;
    right: 5px;
    top: 40%;
    background-color: red;
    color: #fff;
    z-index: 7;
    padding: 10px 0 10px 5px;
    font-size: 12px;
    border-radius: 5px 0 0 5px;
    box-shadow: -1px 1px rgba(0,0,0,.7);
    }
    .lingjianglianjie{
        color: #fff;
    }
    </style>
</head>

<body style="margin: 0px auto; font-size: 12px;" class="mui-android mui-android-6 mui-android-6-0">
<?php if($config['timesiwtch']==1&&$result['isonline']!=1):?>
    <div class="pretime">直播时间预告：<?=$config['time']?></div>
<?php endif?>
    <div class="shadow">
        <img src="../live_test2/file/shadow_0e2a4b5.png" class="shadow" style="position:absolute;width:100%;display:none;margin:0 auto;z-index: 999;">
    </div>
    <div id="rt-share">
        <img src="../live_test2/file/rt-share_95173d3.png" style="width:100%;">
    </div>
    <figure id="video-box" class="vod-box-pl" style="height: 568px;">
        <video x5-video-player-type="h5" x5-video-player-fullscreen="true" style="background: rgb(0,0,0);" id="video" loop="loop" width="100%" preload="auto" poster="" webkit-playsinline="true" playsinline="true" x-webkit-airplay="true" src="http://live.smfyun.com/AppName/<?=$_SESSION['wzb']['bid']?>.m3u8"></video>
        <!-- http://wscdn.alhls.xiaoka.tv/201753/9e1/f9c/R2ZWUMTk2FgOC02Z/index.m3u8 -->
        <!--直播背景图，上为原来的，下为黑色-->
        <!-- <div class="videobg" style="position: absolute; width: 100%; height: 100%; top: 0px; background-image: url(&quot;<?=$result['poster']?>&quot;); background-position: center center; background-size: cover;"></div> -->
        <div class="videobg" style="position: absolute; width: 100%; height: 100%; top: 0px; background-color:#000;"></div>
        <!--基准线-->
        <!-- <div class="line"></div> -->
        <section class="top-attend-layer">
            <div class="user-info" style="min-width:4.56rem; margin-right:.4rem;">
                <figure class="avatar">
                    <img src="<?=$result['logo']?>" alt="🍼酸奶妹妹呀丶">
                    <span class="rank doyen"></span>
                </figure>
                <dl class="item">
                    <dt style="text-shadow:1px 1px rgba(0,0,0,0.7)"><?=$result['name']?></dt>
                    <dd style="text-shadow:1px 1px rgba(0,0,0,0.7)">
                        <?=$result['online']['TotalUserNumber']+$config['num']?>人
                    </dd>
                </dl>
                <button class="subscribe"><?=$user->sub==1?'已订阅':'订阅'?></button>
            </div>
            <div class="avatar-ls-wrap">
                <ul class="avatar-ls" style="text-align:right">
                    <!-- <li>
                        <img src="<?=$result['logo']?>" alt="">
                    </li>
                    <li>
                        <img src="<?=$result['logo']?>" alt="">
                    </li>
                    <li>
                        <img src="<?=$result['logo']?>" alt="">
                    </li>
                    <li>
                        <img src="<?=$result['logo']?>" alt="">
                    </li>
                    <li>
                        <img src="<?=$result['logo']?>" alt="">
                    </li> -->
                </ul>
            </div>
        </section>
        <div class="otw">
            <span class="info top_info"><?=$result['buyname']?$result['buyname']+'等':''?><?=(int)$result['buynum']?>人正在去购买的路上</span>
        <!-- <div class="barragebtn">
            <a class="blindflow"><img src="../live_test2/file/barrage.png" style="width:23px;"><img class="barrageoff" src="../live_test2/file/barrageoff.png"></a>
        </div> -->
        </div>
        <section class="yizhibo-logo" style='top:1.56rem'>
            <div class="yizhibo-logo-ctn">
                <span>直播ID:<?=$result['bid']?></span>
            </div>
        </section>
                        <label class="switch-btn circle-style">
                            <input class="checked-switch" type="checkbox" checked="checked">
                            <span class="text-switch" data-yes="弹幕开" data-no="弹幕关"></span>
                            <span class="toggle-btn"></span>
                        </label>
        <div class="live-starttime" style="top:2.15rem"> <?=date('Y-m-d',time())?></div>
    <!--喜欢按钮-->
        <div class="likenumbox">
            <span class="likenum"><?=$result['zan_num']?></span>
        </div>
        <div class="likeimg"></div>
        <a id="likebtn" type="button" class="like" value="喜欢">
            <img class="likeicon" src="../live_test2/file/like.png">
        </a>
    <!--喜欢按钮结束-->
    <!--转盘开始-->
    <?php if(($result['isonline']==1&&$lswitch==1)||$_SERVER['HTTP_HOST']=='jfb.dev.smfyun.com'):?>
    <div class="platebtn">点击抽奖</div>
    <img src="../live_test2/images/xiexiecanyu.png" id="shan-img" style="display:none;" />
    <img src="/wzb/images/lottery/<?=$data1->id?>.v<?=time()?>.jpg" id="diy1-img" style="display:none;" />
    <img src="/wzb/images/lottery/<?=$data2->id?>.v<?=time()?>.jpg" id="diy2-img" style="display:none;" />
    <!-- <img src="http://<?=$_SERVER['HTTP_HOST']?>/wzba/images/lottery/<?=$data1->id?>.v<?=time()?>.jpg" id="diy1-img" style="display:none;" /> -->
    <img src="/wzb/images/lottery/<?=$data3->id?>.v<?=time()?>.jpg" id="diy3-img" style="display:none;" />
    <img src="/wzb/images/lottery/<?=$data4->id?>.v<?=time()?>.jpg" id="diy4-img" style="display:none;" />
    <div class="banner" style="margin-top: 35%">
        <div class="turnplate" style="background-image:url(../live_test2/images/cj_bg.png);background-size:100% 100%;">
            <canvas class="item" id="wheelcanvas" width="422px" height="422px"></canvas>
            <img class="pointer" src="../live_test2/images/jt2.png"/>
        </div>
        <!-- <div class="huodongshuoming">
            <img class="huodongshuomingbtn" src="../live_test2/images/huodongshuoming.png" style="margin-left:25%;width:50%;" />
        </div> -->
    </div>
    <!-- <div id="huodongguize">
        <img src="../live_test2/images/huodongguize.png" class="red-img">
        <span>
            <p>1.</p>
        </span>
    </div> -->
    <div id="mark">
        <img src="../live_test2/images/gongxizhongjiang.png" class="red-img">
        <span class="huojiangwenzi">
        <p class="huojiangneirong"></p>
        <p class="lingjiangfangshi"></p>
        <p><a class="lingjianglianjie"></a></p>
        </span>
    </div>
    <div id="mark2">
        <img src="../live_test2/images/meiyouzhongjiang.png" class="red-img">
        <span class="meizhongwenzi">
        <p class="meizhongneirong"></p>
        </span>
    </div>
<?php endif?>
    <!--转盘结束-->
        <section class="content-box">
            <div class="room-chat-scroller" id="message">
                <ul class="room-chat-messages" id="list-info">
                    <!-- <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">Jinpo-...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">暧昧俘获人心...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-2">
                            <div class="wrap-enter"> <span class="rank r-2"></span> <span class="info">大颖颖💗&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-5">
                            <div class="wrap-enter"> <span class="rank r-5"></span> <span class="info">独～醉zd6...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-2">
                            <div class="wrap-enter"> <span class="rank r-2"></span> <span class="info">可以了198...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-5">
                            <div class="wrap-enter"> <span class="rank r-5"></span> <span class="info">有你真好9u...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-5">
                            <div class="wrap-enter"> <span class="rank r-5"></span> <span class="info">独～醉zd6...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-3">
                            <div class="wrap-enter"> <span class="rank r-3"></span> <span class="info">Danbab...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-2">
                            <div class="wrap-enter"> <span class="rank r-2"></span> <span class="info">An车虫！...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="gz">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">静宝kz9c8关注了主播</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">shshzn...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn clearfix">
                            <figure class="avatar" style="background:url(https://alcdn.img.xiaoka.tv/20170126/41a/6d6/105915815/41a6d60e3ced1015c1c3ee282e09ef1a.jpg) center center;background-size: cover;"></figure>
                            <dl>
                                <dt class="room-chat-user-name"> <span class="rank r-9"></span>心语😔</dt>
                                <dd class="room-chat-content">我看到大主播都哭过</dd>
                            </dl>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn clearfix">
                            <figure class="avatar" style="background:url(https://alcdn.img.xiaoka.tv/20170217/628/af2/74728246/628af2427836c14b91ea457dfcc78ec8.jpg) center center;background-size: cover;"></figure>
                            <dl>
                                <dt class="room-chat-user-name"> <span class="rank r-8"></span>Buckethead、</dt>
                                <dd class="room-chat-content">我去会会美女</dd>
                            </dl>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">用户5590...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn clearfix">
                            <figure class="avatar" style="background:url(https://alcdn.img.xiaoka.tv/20170401/3e2/42d/220828588/3e242d642d482244ad640f041d1836d7.jpg) center center;background-size: cover;"></figure>
                            <dl>
                                <dt class="room-chat-user-name"> <span class="rank r-7"></span>🉐炮彈💣酸奶</dt>
                                <dd class="room-chat-content">她抠哥梦想好大</dd>
                            </dl>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">迎在雪儿&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-5">
                            <div class="wrap-enter"> <span class="rank r-5"></span> <span class="info">大脸猫金莉&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-5">
                            <div class="wrap-enter"> <span class="rank r-5"></span> <span class="info">有无空&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-2">
                            <div class="wrap-enter"> <span class="rank r-2"></span> <span class="info">霈霈pei&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">Janpon...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-2">
                            <div class="wrap-enter"> <span class="rank r-2"></span> <span class="info">用户6120...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-5">
                            <div class="wrap-enter"> <span class="rank r-5"></span> <span class="info">氵小壞蛋灬卩&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="gz">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">毙丸湖怒岔关注了主播</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn clearfix">
                            <figure class="avatar" style="background:url(https://alcdn.img.xiaoka.tv/20170401/3e2/42d/220828588/3e242d642d482244ad640f041d1836d7.jpg) center center;background-size: cover;"></figure>
                            <dl>
                                <dt class="room-chat-user-name"> <span class="rank r-7"></span>🉐炮彈💣酸奶</dt>
                                <dd class="room-chat-content">😃</dd>
                            </dl>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">sunsin...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-5">
                            <div class="wrap-enter"> <span class="rank r-5"></span> <span class="info">lynnll...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn clearfix">
                            <figure class="avatar" style="background:url(https://alcdn.img.xiaoka.tv/20170126/41a/6d6/105915815/41a6d60e3ced1015c1c3ee282e09ef1a.jpg) center center;background-size: cover;"></figure>
                            <dl>
                                <dt class="room-chat-user-name"> <span class="rank r-9"></span>心语😔</dt>
                                <dd class="room-chat-content">你说的不对</dd>
                            </dl>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn clearfix">
                            <figure class="avatar" style="background:url(http://tvax1.sinaimg.cn/default/images/default_avatar_male_180.gif) center center;background-size: cover;"></figure>
                            <dl>
                                <dt class="room-chat-user-name"> <span class="rank r-1"></span>毙丸湖怒岔</dt>
                                <dd class="room-chat-content">问世间缘是何物</dd>
                            </dl>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">哈雷姆星人&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-5">
                            <div class="wrap-enter"> <span class="rank r-5"></span> <span class="info">DavidY...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-6">
                            <div class="wrap-enter"> <span class="rank r-6"></span> <span class="info">海边的人88...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn clearfix">
                            <figure class="avatar" style="background:url(https://alcdn.img.xiaoka.tv/20170217/628/af2/74728246/628af2427836c14b91ea457dfcc78ec8.jpg) center center;background-size: cover;"></figure>
                            <dl>
                                <dt class="room-chat-user-name"> <span class="rank r-8"></span>Buckethead、</dt>
                                <dd class="room-chat-content">我还是比较喜欢酸奶</dd>
                            </dl>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn clearfix">
                            <figure class="avatar" style="background:url(https://alcdn.img.xiaoka.tv/20170401/3e2/42d/220828588/3e242d642d482244ad640f041d1836d7.jpg) center center;background-size: cover;"></figure>
                            <dl>
                                <dt class="room-chat-user-name"> <span class="rank r-7"></span>🉐炮彈💣酸奶</dt>
                                <dd class="room-chat-content">鸡鸡哥都出来了</dd>
                            </dl>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">我管你gl2...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn clearfix">
                            <figure class="avatar" style="background:url(https://alcdn.img.xiaoka.tv/20170126/41a/6d6/105915815/41a6d60e3ced1015c1c3ee282e09ef1a.jpg) center center;background-size: cover;"></figure>
                            <dl>
                                <dt class="room-chat-user-name"> <span class="rank r-9"></span>心语😔</dt>
                                <dd class="room-chat-content">南京</dd>
                            </dl>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn clearfix">
                            <figure class="avatar" style="background:url(https://alcdn.img.xiaoka.tv/20170217/628/af2/74728246/628af2427836c14b91ea457dfcc78ec8.jpg) center center;background-size: cover;"></figure>
                            <dl>
                                <dt class="room-chat-user-name"> <span class="rank r-8"></span>Buckethead、</dt>
                                <dd class="room-chat-content">肉肉的</dd>
                            </dl>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-3">
                            <div class="wrap-enter"> <span class="rank r-3"></span> <span class="info">瘦幽幽u9X...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">凤凰美居62...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-2">
                            <div class="wrap-enter"> <span class="rank r-2"></span> <span class="info">沙2v3tt&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-2">
                            <div class="wrap-enter"> <span class="rank r-2"></span> <span class="info">筑梦路上66...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">李家兴y5w...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">Dongzi...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">FANqai...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-5">
                            <div class="wrap-enter"> <span class="rank r-5"></span> <span class="info">平凡之路47...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">one💞言...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn clearfix">
                            <figure class="avatar" style="background:url(https://alcdn.img.xiaoka.tv/20170217/628/af2/74728246/628af2427836c14b91ea457dfcc78ec8.jpg) center center;background-size: cover;"></figure>
                            <dl>
                                <dt class="room-chat-user-name"> <span class="rank r-8"></span>Buckethead、</dt>
                                <dd class="room-chat-content">鸡哥别出来 他们会让你交电费的</dd>
                            </dl>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn clearfix">
                            <figure class="avatar" style="background:url(https://alcdn.img.xiaoka.tv/20170401/3e2/42d/220828588/3e242d642d482244ad640f041d1836d7.jpg) center center;background-size: cover;"></figure>
                            <dl>
                                <dt class="room-chat-user-name"> <span class="rank r-7"></span>🉐炮彈💣酸奶</dt>
                                <dd class="room-chat-content">南京小笼包</dd>
                            </dl>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-6">
                            <div class="wrap-enter"> <span class="rank r-6"></span> <span class="info">小小小MB仔&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-3">
                            <div class="wrap-enter"> <span class="rank r-3"></span> <span class="info">🌟ʚ勇敢D...&nbsp;进入直播间</span> </div>
                        </div>
                    </li>
                    <li class="room-chat-item">
                        <div class="item-ctn enter bg-1">
                            <div class="wrap-enter"> <span class="rank r-1"></span> <span class="info">당신이가자a...&nbsp;进入直播间</span> </div>
                        </div>
                    </li> -->
                </ul>
            </div>
        </section>
        <section class="cover" id="J_cover">
            <div class="left-side-cartoon">
                <div class="effect hide" data-amount="14" id="" data-current="14" style="margin-left: -8rem;">
                    <div class="wrap-cartoon">
                        <figure class="cartoon-avatar"> <img src="../live_test2/file/3e242d642d482244ad640f041d1836d7.jpg"> </figure>
                        <p class="nickname"><span class="rank r-7"></span>🉐炮彈..</p>
                        <p class="desc">送出了白百合</p>
                        <figure class="gift"> <img src="../live_tes41a6d60e3ced1015c1c3ee282e09ef1at2/file/822ba53167ca5ff3864236a9b792723f.png"> </figure>
                        <h4 class="count">×&nbsp;14</h4> </div>
                </div>
                <div class="effect hide" data-amount="1" id="" data-current="1" style="margin-left: -8rem;">
                    <div class="wrap-cartoon">
                        <figure class="cartoon-avatar"> <img src="../live_test2/file/.jpg"> </figure>
                        <p class="nickname"><span class="rank r-9"></span>心语😔</p>
                        <p class="desc">送出了棒棒糖</p>
                        <figure class="gift"> <img src="../live_test2/file/cc25568bfb3d2f63092b285f9552c85f.png"> </figure>
                        <h4 class="count">×&nbsp;1</h4> </div>
                </div>
                <div class="luxury hide" id="J_luxury"></div>
                <div class="gifter">
                    <div class="placeholder"></div>
                    <figure><img src="http://m.yizhibo.com/l/R2ZWUMTk2FgOC02Z.html" alt=""></figure>
                    <span class="rank r-6"></span>
                    <div class="info"><span class="nickname">MC 悠悠</span>送出</div>
                </div>
        </section>
        <section class="btm-menu">
            <div class="wrap-menu">
                <?php if($result['isonline']==1||$_SERVER['HTTP_HOST']=='jfb.dev.smfyun.com'):?>
                    <div class="w-input " style="width:4.9rem;border-radius:.3rem;">
                        <input class="input" id="content" type="text" placeholder="说点什么吧..." style="width:165px;">
                    </div>
                    <div id="out-send" class="send-msg" style="display:inline-block;position:relative;margin-left:.15rem;background-color:rgba(0,0,0,.2);width:1.6rem;border-radius:.3rem;line-height:34.13px;color:#fff;font-size:.4rem;text-align:center;height:100%;text-shadow:1px 1px rgba(0,0,0,0.7)">发送</div>
                <?php endif?>
                <figure class="cut"></figure>
                <span id="inside-send" class="send-msg">发送</span>
                <div class="w-share" style="margin:auto .36rem auto auto;float:right;width:2.2rem;">
                    <figure class="vs-share" style="width: 100%; height: 100%;">
                    <img src="../live_test2/file/shopping.png">
                    </figure>
                </div>
                <!-- <div class="w-gift">
                    <figure><img src="../live_test2/file/gift_3c4198a.png" alt=""></figure>
                </div> -->
            </div>
        </section>
        <div class='mask'>
        </div>
        <section class="sns-share" style='display:none;'>
            <div class="sns-hd">
                <div class="goods-count">
                    <img src="../live_test2/file/shopicon.png" style="height:100%;">
                    <span>全部商品</span>
                    <span style="color:#cc0066;"><?=$result['goods_count']?></span>
                </div>
            </div>
            <div class="sns-ctn" id='wrapper'>
                <div id='scroll'>
                    <?php foreach ($goods as $k => $v):?>
                    <div class="goods" data-url="<?=$v->url?>">
                        <figure class="goods-img">
                        <?php if($v->type==1):?>
                            <img src="<?=$v->pic?>">
                        <?php else:?>
                            <img src="/wzb/dbimages/setgood/<?=$v->id?>.v<?=$v->time?>.jpg">
                        <?php endif?>
                        </figure>
                        <div class="goods-detail">
                            <span class="goods-name">
                            <?=$v->title?>
                        </span>
                            <span class="goods-price">
                            ￥<?=$v->price?>
                        </span>
                            <button class="goods-pick" data-iid='<?=$v->goodid?>' data-imgurl='<?=$v->pic?>' data-price='<?=$v->price?>' data-title='<?=$v->title?>'>
                                点击购买
                            </button>
                        </div>
                    </div>
                    <?php endforeach?>
                </div>
            </div>
        <?php if($config['shop_url']):?>
            <div class="sns-ft">
                <img src="../live_test2/file/entershop.png" class="enter-shop" data-url="<?=$config['shop_url']?>">
            </div>
        <?php endif?>
            <!-- <h4 class="back">返回</h4> -->
        </section>
        <div class="pause" style="display: none;">
            <div class="pause-wrap">
                <figure>
                    <img src="../live_test2/file/pause_b5a4c7c.png" alt="">
                </figure>
            </div>
        </div>
        <div class="pay-live" style="display: none">
            <p>本直播间需要购买门票才能观看</p>
            <a id="enter_room" class="buy-btn" href="javascript:void(0)" mlink-handling="true">去购买</a>
        </div>
    </figure>
    <div class="player-lock" style="display: none;"></div>
    <div class="leadReece" <?=$result['coupon']['title']&&$result['isonline']==1?'style="display: true;"':'style="display: none;"'?>>
        <div class="close"></div>
        <figure>
            <span class="couponname"><?=$result['coupon']['title']?></span>
            <img style="position: absolute;width: 80%;height: auto;top: 35%;left: 10%;" src="../live_test2/file/lucky-gift.png" alt="">
        </figure>
    </div>
    <!-- 优惠券下发错误情况 -->
    <div class="leadReece" <?=$result['coupon']['error']&&$result['isonline']==1?'style="display: true;"':'style="display: none;"'?>>
        <div class="close"></div>
        <figure><img style="width: 80%;height: auto;margin-left: 10%;margin-top: 30%;" src="../live_test2/file/lucky-gift1.png" alt=""></figure>
        <p style="width: 80%;margin-left: 10%;margin-top: 68%;"><?=$result['coupon']['error']?></p>
        <a id="enter_room" class="openapp" href="javascript:void(0)" mlink-handling="true">我知道了</a>
        <div class="openapp-bg"></div>
    </div>
    <span class="subscribe-done" style="display:none"></span>
    <span class="interdiction" style="display:none">您已被管理员禁言</span>
    <?php if($result['isonline']!=1):?>
    <div class="guide-pay" style="display:block">
        <figure style="width:100%;height:100%;">
            <img src="../live_test2/file/weikaibo.png">
            <div class="close" style="position:absolute;width:100%;height:100%;left:0;top:0;"></div>
        </figure>
    </div>
    <?php endif?>
    <div class="yellow" style="display:none">
        <figure style="width:100%;height:100%;">
            <img style="width: 100%;" src="../live_test2/file/yellow.png">
        </figure>
    </div>
    <div class="login">
        <div class="login-wrap">
            <p>登录后才能操作哦</p>
            <div class="btn ok">去登录</div>leadReece
            <div class="btn cancel">取消</div>
            <div class="close"></div>
        </div>
    </div>

<div class="trolley" style="display:none;overflow: hidden; position: absolute; z-index: 1000; background: white; bottom: 0px; left: 0px; right: 0px; visibility: visible; transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;">
    <div class="main">
        <div class="img">
            <img src="">
        </div>
        <div class="details">
            <p class="name">
                购物车测试
            </p>
            <div class="price clearfix">
                <div class="price-cn">
                    <span class="yuan">
                            ￥
                        </span>
                    <i class="number">
                            1.00
                        </i>
                </div>
            </div>
        </div>
        <div class="cancel">
            <div class="cancel-img"></div>
        </div>
    </div>
    <div class="content">
        <div class="goods-models">
            <dl class="count clearfix">
                <dt class="count-cn">
                    <label>购买数量：</label>
                </dt>
                <dd>
                    <dl class="clearfix">
                        <div class="quantity">
                            <button class="minus" type="button" disabled="true"></button>
                            <input type="text" class="txt" pattern="[0-9]*" value="1">
                            <button class="plus" type="button"></button>
                            <div class="response-area response-area-minus"></div>
                            <div class="response-area response-area-plus"></div>
                        </div>
                    </dl>
                </dd>
                <dt class="inventory">
                    <div class="inventory-num">剩余300件
                    </div>
                </dt>
            </dl>
        </div>
    </div>
    <div class="foot">
        <div class="two-btn">
            <a href="javascript:;" class="orange-btn">加入购物车</a>
            <a href="javascript:;" class="red-btn">立即购买</a>
        </div>
    </div>
</div>
    <script>
    window.user_avatar = '<?=$user->headimgurl?>';
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: "<?=$jsapi['appId']?>", // 必填，公众号的唯一标识
        timestamp: <?=$jsapi['timestamp']?>, // 必填，生成签名的时间戳
        nonceStr: "<?=$jsapi['nonceStr']?>", // 必填，生成签名的随机串
        signature: "<?=$jsapi['signature']?>", // 必填，签名，见附录1
        jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage','hideMenuItems'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
    wx.ready(function () {
        wx.hideMenuItems({
            menuList: [
            'menuItem:copyUrl',
            "menuItem:editTag",
            "menuItem:delete",
            "menuItem:copyUrl",
            "menuItem:originPage",
            "menuItem:readMode",
            "menuItem:openWithQQBrowser",
            "menuItem:openWithSafari",
            "menuItem:share:email",
            "menuItem:share:brand",
            "menuItem:share:qq",
            "menuItem:share:weiboApp",
            "menuItem:favorite",
            "menuItem:share:facebook",
            "menuItem:share:QZone"
            ],
            success: function (res) {
            },
            fail: function (res) {
            }
        });
    })
    var shareConfig = {
            wx: {
                title: "<?=$config['wstitle']?>",//朋友
                desc: "<?=$config['wsdesc']?>",//朋友
                ptitle: "<?=$config['wsptitle']?>",//朋友圈
                imgUrl: "<?=$result['wsimg']?>",
                link: "http://<?=$_SERVER['HTTP_HOST']?>/wzb/live?bid=<?=$result['bid']?>"
            },
            sObj: {
                "nickname":"<?=$user->nickname?>",
                "online_num":<?=$config['num']?>,
                "openid": "<?=$result['openid']?>",
                "bid": "<?=$result['bid']?>",
                "sid": "<?=$result['sid']?>",
                "scid": "R2ZWUMTk2FgOC02Z",
                "status": "10",
                "memberid": "50606454",
                "showtype": "0",
                "unq_member_key": "",
                "vscreen": "1"
            },
            url: {
                "send_live_comment": "/wzb/send_live_comment",
                "buy_gift_h5": "/gift/h5api/buy_gift_h5",
                "get_my_wallet": "/member/h5api/get_my_wallet",
                "get_gift_list_background": "//pay.yizhibo.com/gift/api/get_gift_list_background",
                "has_follow": "/www/live/has_follow",
                "follow_friends": "/www/live/follow_friends"
            },
            rmzb: {
                "appid": "592819922",
            }
        },
        user = {};
    </script>
    <script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
    <script src="../live_test2/file/iscroll.js"></script>
    <script src="../live_test2/file/awardRotate.js"></script>
    <script type="text/javascript">
    <?php if($result['buyname']):?>
        var top_name = '<?=$result['buyname']?>';
        var s = top_name.length > 3?top_name.substr(0,2)+'...':top_name;
        var top_info = s+'等<?=$result['buynum']?>人正在购买的路上';
    <?php else:?>
        var top_info = '<?=(int)$result['buynum']?>人正在购买的路上';
    <?php endif?>
        $('.top_info').text(top_info);
    function check_stock(){
        var stock = parseInt($('.inventory-num').text().replace('剩余','').replace('件',''));
        if(parseInt($('.txt').val())>=stock){
            $('.txt').val(stock);
            $('.plus').addClass('disabled');
        }else{
            $('.plus').removeClass('disabled');
        }
        if(parseInt($('.txt').val())<=1){
            $('.minus').addClass('disabled');
        }else{
            $('.minus').removeClass('disabled');
        }
    }

    $("w-input").blur(function(){
        $("content").attr("value","");
    });

    $(document).on('change', '.txt', function() {
        check_stock();
    });

    $(document).on('click', '.switch-btn', function() {
            $('.content-box').toggle();
    });

    $(document).on('click','.close',function(){
        $(".leadReece").hide();
    });

    $(document).on('click','.openapp',function(){
        $(".leadReece").hide();
    });

    $(document).on('click','.subscribe',function(){
        if($('.subscribe').text()=='订阅'){
            var subaction = '1';
        }else if($('.subscribe').text()=='已订阅'){
            var subaction = '2' ;
        }
        $.ajax({
            url: '/wzb/live',
            type: 'post',
            async: false,
            dataType: 'text',
            data: {subaction:subaction},
        })
        .done(function(res) {
            console.log(res);
            $(".subscribe-done").text(res);
            if(res=='订阅成功'){
                $('.subscribe').text('已订阅')
                $.ajax({
                    url: '/wzb/send_live_comment',
                    type: 'post',
                    async: false,
                    dataType: 'text',
                    data: {sub: 1 ,openid: shareConfig.sObj.openid,bid: shareConfig.sObj.bid,sid:<?=$_SESSION['wzb']['sid']?>},
                })
                .done(function(res) {
                    console.log(res);
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });
            }else if(res=='成功取消订阅'){
                $('.subscribe').text('订阅')
            }
            $(".subscribe-done").fadeIn(1000);
            $(".subscribe-done").fadeOut(1500);
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    });

    // $(document).on('click','.subscribe-done',function(){
    //     $(".subscribe-done").fadeOut(500);
    // })
    $(document).on('click', '.enter-shop', function() {
        window.location.href = $(this).data('url');
    });

    $(document).on('click', '.goods-picks', function(event) {
        // return false;
        event.stopPropagation();
        $(".trolley").show();
        $('.img>img').attr('src', $(this).data('imgurl'));
        $('.name').text($(this).data('title'));
        $('.number').text($(this).data('price'));
        $.ajax({
            url: '/wzb/live',
            type: 'post',
            dataType: 'json',
            data: {num_iid: $(this).data('iid')},
        })
        .done(function(res) {
            // console.log(res);
            var item = res.response.item;
            console.log(item.price);
            console.log(item.title);
            var sku = item.skus;
            new_sku = Array();

            if(item.skus.length>0){
                for (var i = 0;sku[i]; i++) {
                    // console.log(sku[i].price);
                    // console.log(sku[i].sku_id);
                    // console.log(sku[i].properties_name_json);
                    sku_json = $.parseJSON(sku[i].properties_name_json);
                    console.log(sku_json);
                    // return false;
                    new_sku[i] = Array();
                    for (var a = 0; sku_json[a]; a++) {
                        // console.log(sku_json[a].kid);
                        // console.log(sku_json[a].vid);
                        // console.log(sku_json[a].k);
                        // console.log(sku_json[a].v);
                        new_sku[i]['quantity'] = sku[i].quantity;
                        new_sku[i]['sku_id'] = sku[i].sku_id;
                        new_sku[i]['price'] = sku[i].price;
                        new_sku[i][sku_json[a].k] = sku_json[a].v;
                        type_num = a;
                    };
                };
                var pro_sku = [];
                for (var i = 0; sku_json[i]; i++) {
                    pro_sku[i] = [];
                };
                for (var n= 0; new_sku[n]; n++) {
                    for(var i= 0; sku_json[i]; i++){
                        if(pro_sku[i].indexOf(sku_json[i].k)<0){
                            pro_sku[i].push(sku_json[i].k);
                        }
                        if(pro_sku[i].indexOf(new_sku[n][sku_json[i].k])<0){
                            pro_sku[i].push(new_sku[n][sku_json[i].k]);
                        }
                        // console.log(pro_sku);
                        // return false;
                        console.log(sku_json[i].k);
                        console.log(new_sku[n][sku_json[i].k]);
                    }
                }
                console.log('pro_sku');
                console.log(pro_sku);
                var str_good = '';
                var str_sku = Array();
                for (var i = 0; pro_sku[i]; i++) {
                    str_good = str_good + '<dl class="classes clearfix">'+
                            '<dt class="model">'+
                                '<label>'+pro_sku[i][0]+'</label>'+
                            '</dt>'+
                            '<dd>'+
                                '<ul class="model-list sku sel-list">'+

                                '</ul>'+
                            '</dd>'+
                        '</dl>';
                    str_sku[i] = '';
                    for (var p= 1; pro_sku[i][p]; p++) {
                        if(p==1){
                            str_sku[i] = str_sku[i] + '<li class="tag active">'
                                +pro_sku[i][p]+
                            '</li>';
                        }else{
                            str_sku[i] = str_sku[i] + '<li class="tag">'
                                +pro_sku[i][p]+
                            '</li>';
                        }
                    };
                };
                // console.log(i);
                // console.log(str_sku);
                // console.log(str_good);
                $('.classes').remove();
                // console.log($('.goods-models').text());
                $('.goods-models').prepend(str_good);
                // console.log($('.goods-models').text());
                // return false;
                for (var i = 0; str_sku[i]; i++) {
                    $('.model-list').eq(i).append(str_sku[i]);
                };
                var pro = Array();
                for (var i=0; $('.active').eq(i).text(); i++) {
                    pro[i] = $('.active').eq(i).text();
                };
                var lv = 0;
                var end = false;
                var i = 0;
                var p = 0;

                for (var n= 0; new_sku[n]; n++) {
                    for (var i = 0; i <= type_num; i++) {
                        console.log('lv:'+lv+',n:'+n+',i:'+i);
                        // console.log(pro[i]);
                        // console.log(new_sku[n][sku_json[i].k]);
                        if(pro[i]==new_sku[n][sku_json[i].k]){
                            lv++;
                        }else{
                            lv = 0;
                            break;
                        }
                        if(lv-1==type_num){
                            console.log('最终n：'+n);
                            $('.inventory-num').text('剩余'+new_sku[n]['quantity']+'件');
                            $('.number').text(new_sku[n]['price']);
                            check_stock();
                            return n;
                        }
                    };
                };
            }
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });

    });
    $(".cancel-img").click(function(){
      $(".trolley").hide();
      $('.classes').remove();
    });
    $(document).on('click', '.response-area-minus', function() {
        var stock = parseInt($('.inventory-num').text().replace('剩余','').replace('件',''));
        if(parseInt($('.txt').val())>=2){
            $('.txt').val(parseInt($('.txt').val())-1);
            check_stock()
        }
    });
    $(document).on('click', '.response-area-plus', function() {
        var stock = parseInt($('.inventory-num').text().replace('剩余','').replace('件',''));
        if(parseInt($('.txt').val())<=8&&parseInt($('.txt').val())<stock){
            $('.txt').val(parseInt($('.txt').val())+1);
            check_stock()
        }
    });
    $(document).on('click', '.tag', function() {
        $(this).parent().children().removeClass('active');
        $(this).addClass('active');
        var pro = Array();
        for (var i=0; $('.active').eq(i).text(); i++) {
            pro[i] = $('.active').eq(i).text();
        };
        var lv = 0;
        var end = false;
        var i = 0;
        var p = 0;
        for (var n= 0; new_sku[n]; n++) {
            for (var i = 0; i <= type_num; i++) {
                console.log('lv:'+lv+',n:'+n+',i:'+i);
                // console.log(pro[i]);
                // console.log(new_sku[n][sku_json[i].k]);
                if(pro[i]==new_sku[n][sku_json[i].k]){
                    lv++;
                }else{
                    lv = 0;
                    break;
                }
                if(lv-1==type_num){
                    console.log('最终n：'+n);
                    $('.inventory-num').text('剩余'+new_sku[n]['quantity']+'件');
                    $('.number').text(new_sku[n]['price']);
                    check_stock();
                    return n;
                }
            };
        };
        // console.log(new_sku);
        // console.log(type_num);
    });
    </script>
    <script src="../live_test2/file/share.html_aio_2_436fe5c.js"></script>
    <script type="text/javascript">
$('.huodongshuomingbtn').click(function(){
    $('#huodongguize').fadeIn();
});
$('#huodongguize').click(function(){
    $('#huodongguize').hide();
})

    buy_good = 1;//默认是可以点击的
    $(document).on('click', '.goods', function() {
        console.log(buy_good);
        if(buy_good == 1){
            buy_good = 0;
            var that = this;
             $.ajax({
                url: '/wzb/send_live_comment',
                type: 'post',
                async: false,
                dataType: 'text',
                data: {buy: 1 ,openid: shareConfig.sObj.openid,bid: shareConfig.sObj.bid,sid:<?=$_SESSION['wzb']['sid']?>},
             })
             .done(function(res) {
                 window.location.href = $(that).data('url');
                 buy_good = 1;
                 console.log("success");
             })
             .fail(function() {
                 console.log("error");
             })
             .always(function() {
                 console.log("complete");
             });
        }
    });
$(function () {
   window.zan_num = 0;
   setInterval(function(){
    $.ajax({
        url: '/wzb/zan',
        type: 'post',
        dataType: 'text',
        data: {zan: window.zan_num,bid:<?=$result['bid']?>},
    })
    .done(function(res) {
        var x = -100;
        var y = 100;
        var z = Number(res)-Number($('.likenum').text());
        console.log(z);
        // index = index-1;
        for (var n=1; n <= z; n++) {
        setTimeout(function(n){
            var num = Math.floor(Math.random() * 8 + 1);
            var index=$('.likeimg').children('img').length;
            var rand = parseInt(Math.random() * (x - y + 1) + y);
            $(".likeimg").append("<img src='' class=\"img1\">");
            $('.likeimg>img:eq(' + index + ')').attr('src','../live_test2/file/'+num+'.png')
            $(".likeimg>img").animate({
                bottom:"400px",
                opacity:"0",
                right: rand,
            },3000)
            setTimeout(function(){
                $(".img1").remove();
            },3000)
        },n*50)
        };

        window.zan_num = 0;
        $('.likenum').text(res);
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });

   },5000)
   $("#likebtn").click(function(){
        var x = -100;
        var y = 100;
        var num = Math.floor(Math.random() * 8 + 1);
        var index=$('.likeimg').children('img').length;
        var index2=$('.img2').length;
        console.log('index2:'+index2);
        var rand = parseInt(Math.random() * (x - y + 1) + y);
        console.log(index);
        // index = index-1;
        $(".likeimg").append("<img src='' class=\"img2\">");
        $('.likeimg>img:eq(' + index + ')').attr('src','../live_test2/file/'+num+'.png')
        $(".likeimg>img").animate({
            bottom:"400px",
            opacity:"0",
            right: rand,
        },3000);
        setTimeout(function(){
            console.log('clear'+index2);
            $(".img2").eq(0).remove();
        },3000)
        var z = $('.likenum').text();
        window.zan_num++;
        var m = Number(z) + 1;
        $('.likenum').text(m);
   })

});

    <?php if(($result['isonline']==1&&$lswitch==1)||$_SERVER['HTTP_HOST']=='jfb.dev.smfyun.com'):?>
var platestatus=0
$(document).on('click', '.platebtn', function() {
    if (platestatus==0) {
        $('.banner').stop();
        $('.banner').animate({"margin-left":"5%"},400);
        platestatus++;
        // console.log(platestatus);
        $('.platebtn').text("点击收起");
    }else{
        $('.banner').stop();
        $('.banner').animate({"margin-left":"100%"},600);
        platestatus=0;
        // console.log(platestatus);
        $('.platebtn').text("点击抽奖");
    };
});




var turnplate={
        restaraunts:[],             //大转盘奖品名称
        colors:[],                  //大转盘奖品区块对应背景颜色
        outsideRadius:192,          //大转盘外圆的半径
        textRadius:155,             //大转盘奖品位置距离圆心的距离
        insideRadius:68,            //大转盘内圆的半径
        startAngle:0,               //开始角度

        bRotate:false               //false:停止;ture:旋转
};

$(document).ready(function(){
    //动态添加大转盘的奖品与奖品区域背景颜色
    turnplate.restaraunts = ["<?=$content['type1']?>", "谢谢参与", "<?=$content['type2']?>", "谢谢参与", "<?=$content['type3']?>", "谢谢参与", "<?=$content['type4']?>", "谢谢参与"];
    turnplate.colors = ["#FFFFFF","#5fcbd5", "#FFFFFF", "#5fcbd5", "#FFFFFF","#5fcbd5", "#FFFFFF", "#5fcbd5"];


    var rotateTimeOut = function (){
        $('#wheelcanvas').rotate({
            angle:0,
            animateTo:2160,
            duration:8000,
            callback:function (){
                alert('网络超时，请检查您的网络设置！');
            }
        });
    };

    //旋转转盘 item:奖品位置; txt：提示语;
    var rotateFn = function (item, txt){
        var angles = item * (360 / turnplate.restaraunts.length) - (360 / (turnplate.restaraunts.length*2));
        if(angles<270){
            angles = 270 - angles;
        }else{
            angles = 360 - angles + 270;
        }
        $('#wheelcanvas').stopRotate();
        $('#wheelcanvas').rotate({
            angle:0,
            animateTo:angles+1800,
            duration:8000,
            callback:function (){
                $('#mark').fadeIn();
                //alert(txt);
                turnplate.bRotate = !turnplate.bRotate;
                $('#mark').click(function(){
                    $(this).hide();
                });

            }
        });
    };
    var rotateFn2 = function (item, txt){
        var angles = item * (360 / turnplate.restaraunts.length) - (360 / (turnplate.restaraunts.length*2));
        if(angles<270){
            angles = 270 - angles;
        }else{
            angles = 360 - angles + 270;
        }
        $('#wheelcanvas').stopRotate();
        $('#wheelcanvas').rotate({
            angle:0,
            animateTo:angles+1800,
            duration:8000,
            callback:function (){
                $('#mark2').fadeIn();
                //alert(txt);
                turnplate.bRotate = !turnplate.bRotate;
                $('#mark2').click(function(){
                    $(this).hide();
                });

            }
        });
    };

    $('.pointer').click(function (){
        if(turnplate.bRotate)return;
        turnplate.bRotate = !turnplate.bRotate;
        //获取随机数(奖品个数范围内)
        // $giftitem=Array{1=>6000,2=>2000,3=>1000,4=>600,5=>200,6=>100,7=>70,8=>30};
        // $rndnum=Math.random()*10000;
        // $gailv=0;
        // foreach($giftitem as $giftitemid => $giftitemrate)
        // $gailv=$gailv + $giftitemrate;
        // if($num<=$gailv){
        //     var item=$giftitemid+1;
        // }
        // endforeach
        var item = 0
        $.ajax({
            url: '/wzb/sweepstakes',
            type: 'post',
            async: false,
            dataType: 'json',
            data: {sweepstakes:1},
        })
        .done(function(res) {
            console.log(res);
            if (res.state==null) {
                $('.meizhongneirong').text('出现未知错误，请稍后重试');
                $('#mark2').fadeIn();
                turnplate.bRotate = !turnplate.bRotate;
                $('#mark2').click(function(){
                    $(this).hide();
                });
            }else{
                if (res.state==0) {
                    //次数耗尽
                    var state = res.content;
                    $('.meizhongneirong').text(state);
                    $('#mark2').fadeIn();
                    turnplate.bRotate = !turnplate.bRotate;
                    $('#mark2').click(function(){
                        $(this).hide();
                    });
                };
                if (res.state==2) {
                    //没中奖(真没中奖和库存完了)
                    var state = res.content;
                    var prize = res.iid;
                    item =prize
                    $('.meizhongneirong').text(state);
                    rotateFn2(item, turnplate.restaraunts[item-1]);
                };
                if (res.state==1) {
                    //中了
                    var prize = res.iid;
                    var type = res.type;
                    item = prize;
                    $('.lingjiangfangshi').text('');
                    $('.lingjianglianjie').text('');
                    $('.lingjianglianjie').attr("href","");
                    if (prize==1) {
                        $('.huojiangneirong').text('<?=$content['type1']?>');
                    };
                    if (prize==3) {
                        $('.huojiangneirong').text('<?=$content['type2']?>');
                    };
                    if (prize==5) {
                        $('.huojiangneirong').text('<?=$content['type3']?>');
                    };
                    if (prize==7) {
                        $('.huojiangneirong').text('<?=$content['type4']?>');
                    };
                    if (type==1) {
                        var point = res.point;
                        $('.lingjiangfangshi').text('奖品已自动下发');
                    };
                    if (type==2) {
                        var url = res.url;
                        $('.lingjianglianjie').text('点击领取');
                        $('.lingjianglianjie').attr("href",url);
                    };
                    if (type==3) {
                        var money = Math.ceil(res.num/100);
                        $('.lingjiangfangshi').text('奖品已自动下发');
                    };
                    if (type==4) {
                        var url = res.url;
                        $('.lingjianglianjie').text('点击领取');
                        $('.lingjianglianjie').attr("href",url);
                    };
                    rotateFn(item, turnplate.restaraunts[item-1]);
                };
                    //  出错
                if (res.state=='error') {
                    var state = res.error_response;
                    var prize = res.iid;
                    item =prize
                    $('.meizhongneirong').text(state);
                    rotateFn2(item, turnplate.restaraunts[item-1]);
                };
            }
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });

        // var item = rnd(1,turnplate.restaraunts.length);
        //奖品数量等于10,指针落在对应奖品区域的中心角度[252, 216, 180, 144, 108, 72, 36, 360, 324, 288]
        // rotateFn(item, turnplate.restaraunts[item-1]);
        /* switch (item) {
            case 1:
                rotateFn(252, turnplate.restaraunts[0]);
                break;
            case 2:
                rotateFn(216, turnplate.restaraunts[1]);
                break;
            case 3:
                rotateFn(180, turnplate.restaraunts[2]);
                break;
            case 4:
                rotateFn(144, turnplate.restaraunts[3]);
                break;
            case 5:
                rotateFn(108, turnplate.restaraunts[4]);
                break;
            case 6:
                rotateFn(72, turnplate.restaraunts[5]);
                break;
            case 7:
                rotateFn(36, turnplate.restaraunts[6]);
                break;
            case 8:
                rotateFn(360, turnplate.restaraunts[7]);
                break;
            case 9:
                rotateFn(324, turnplate.restaraunts[8]);
                break;
            case 10:
                rotateFn(288, turnplate.restaraunts[9]);
                break;
        } */
        console.log(item);
    });
});

function rnd(n, m){
    var random = Math.floor(Math.random()*(m-n+1)+n);
    return random;

}


//页面所有元素加载完毕后执行drawRouletteWheel()方法对转盘进行渲染
window.onload=function(){
    drawRouletteWheel();
};

function drawRouletteWheel() {
  var canvas = document.getElementById("wheelcanvas");
  if (canvas.getContext) {
      //根据奖品个数计算圆周角度
      var arc = Math.PI / (turnplate.restaraunts.length/2);
      var ctx = canvas.getContext("2d");
      //在给定矩形内清空一个矩形
      ctx.clearRect(0,0,422,422);
      //strokeStyle 属性设置或返回用于笔触的颜色、渐变或模式
      ctx.strokeStyle = "#FFBE04";
      //font 属性设置或返回画布上文本内容的当前字体属性
      ctx.font = 'bold 18px Microsoft YaHei';
      for(var i = 0; i < turnplate.restaraunts.length; i++) {
          var angle = turnplate.startAngle + i * arc;
          ctx.fillStyle = turnplate.colors[i];
          ctx.beginPath();
          //arc(x,y,r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）
          ctx.arc(211, 211, turnplate.outsideRadius, angle, angle + arc, false);
          ctx.arc(211, 211, turnplate.insideRadius, angle + arc, angle, true);
          ctx.stroke();
          ctx.fill();
          //锁画布(为了保存之前的画布状态)
          ctx.save();

          //改变画布文字颜色
          var b = i+2;
          if(b%2){
             ctx.fillStyle = "#FFFFFF";
            }else{
             ctx.fillStyle = "#E5302F";
            };

          //----绘制奖品开始----


          var text = turnplate.restaraunts[i];
          var line_height = 17;
          //translate方法重新映射画布上的 (0,0) 位置
          ctx.translate(211 + Math.cos(angle + arc / 2) * turnplate.textRadius, 211 + Math.sin(angle + arc / 2) * turnplate.textRadius);

          //rotate方法旋转当前的绘图
          ctx.rotate(angle + arc / 2 + Math.PI / 2);

          /** 下面代码根据奖品类型、奖品名称长度渲染不同效果，如字体、颜色、图片效果。(具体根据实际情况改变) **/
          if(text.indexOf("盘")>0){//判断字符进行换行
              var texts = text.split("盘");
              for(var j = 0; j<texts.length; j++){
                  ctx.font = j == 0?'bold 20px Microsoft YaHei':'bold 18px Microsoft YaHei';
                  if(j == 0){
                      ctx.fillText(texts[j]+"盘", -ctx.measureText(texts[j]+"盘").width / 2, j * line_height);
                  }else{
                      ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height*1.2); //调整行间距
                  }
              }
          }else if(text.indexOf("盘") == -1 && text.length>8){//奖品名称长度超过一定范围
              text = text.substring(0,8)+"||"+text.substring(8);
              var texts = text.split("||");
              for(var j = 0; j<texts.length; j++){
                  ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
              }
          }else{

              //在画布上绘制填色的文本。文本的默认颜色是黑色

              //measureText()方法返回包含一个对象，该对象包含以像素计的指定字体宽度
              ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
          }

          //添加对应图标

          if(text.indexOf(turnplate.restaraunts[0])>=0){
              var img= document.getElementById("diy1-img");
              img.onload=function(){
                  ctx.drawImage(img,-25,20,50,50);
              };
              ctx.drawImage(img,-25,20,50,50);
          };
          if(text.indexOf(turnplate.restaraunts[1])>=0){
              var img= document.getElementById("shan-img");
              img.onload=function(){
                  ctx.drawImage(img,-25,20,50,50);
              };
              ctx.drawImage(img,-25,20,50,50);
          };
          if(text.indexOf(turnplate.restaraunts[2])>=0){
              var img= document.getElementById("diy2-img");
              img.onload=function(){
                  ctx.drawImage(img,-25,20,50,50);
              };
              ctx.drawImage(img,-25,20,50,50);
          };
          if(text.indexOf(turnplate.restaraunts[3])>=0){
              var img= document.getElementById("shan-img");
              img.onload=function(){
                  ctx.drawImage(img,-25,20,50,50);
              };
              ctx.drawImage(img,-25,20,50,50);
          };
          if(text.indexOf(turnplate.restaraunts[4])>=0){
              var img= document.getElementById("diy3-img");
              img.onload=function(){
                  ctx.drawImage(img,-25,20,50,50);
              };
              ctx.drawImage(img,-25,20,50,50);
          };
          if(text.indexOf(turnplate.restaraunts[5])>=0){
              var img= document.getElementById("shan-img");
              img.onload=function(){
                  ctx.drawImage(img,-25,20,50,50);
              };
              ctx.drawImage(img,-25,20,50,50);
          };
          if(text.indexOf(turnplate.restaraunts[6])>=0){
              var img= document.getElementById("diy4-img");
              img.onload=function(){
                  ctx.drawImage(img,-25,20,50,50);
              };
              ctx.drawImage(img,-25,20,50,50);
          };

          if(text.indexOf(turnplate.restaraunts[7])>=0){
              var img= document.getElementById("shan-img");
              img.onload=function(){
                  ctx.drawImage(img,-25,20,50,50);
              };
              ctx.drawImage(img,-25,20,50,50);
          };


          //把当前画布返回（调整）到上一个save()状态之前
          ctx.restore();
          //----绘制奖品结束----
      }
  }
};
<?php endif?>
    // $(document).on('click', '.goods', function() {
    //      $.ajax({
    //         url: '/wzb/buy',
    //         type: 'post',
    //         async: false,
    //         dataType: 'json',
    //         data: {buyadd: 1 ,openid: shareConfig.sObj.openid,bid: shareConfig.sObj.bid,client_id:window.client_id},
    //      })
    //      .done(function(res) {
    //          console.log("success");
    //      })
    //      .fail(function() {
    //          console.log("error");
    //      })
    //      .always(function() {
    //          console.log("complete");
    //      });
    // });
    // setInterval(
    //     function(){
    //         $.ajax({
    //             url: '/wzb/buy',
    //             type: 'post',
    //             async: false,
    //             dataType: 'json',
    //             data: {buyget: 1 ,openid: shareConfig.sObj.openid,bid: shareConfig.sObj.bid,client_id:window.client_id},
    //          })
    //          .done(function(res) {
    //              console.log(res);
    //              $('.top_info').text(res.buynum+'人正在去购买的路上');
    //          })
    //          .fail(function() {
    //              console.log("error");
    //          })
    //          .always(function() {
    //              console.log("complete");
    //          })
    //     },300000)//5分钟
    </script>
    <script src="../live_test2/file/mlink.min.js"></script>
    <script src="../live_test2/file/yxa.min.js"></script>
</body>

</html>
