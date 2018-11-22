<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Amaze UI Admin index Examples</title>
    <meta name="description" content="这是一个 index 页面">
    <meta name="keywords" content="index">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="http://jfb.dev.smfyun.com/stand/assets/css/jquery.treemenu.css" type="text/css">
    <link rel="stylesheet" href="http://jfb.dev.smfyun.com/stand/assets/css/amazeui.min.css" />
    <link rel="stylesheet" href="http://jfb.dev.smfyun.com/stand/assets/css/admin.css">
    <link rel="stylesheet" href="http://jfb.dev.smfyun.com/stand/assets/css/app.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<style>
* {
    list-style: none;
    border: none;
}

body {
    font-family: Arial;
    background-color: #2C3E50;
}

.tree {
    color: #46CFB0;
    width: 800px;
}

.tree li,
.tree li>a,
.tree li>span {
    padding: 4pt;
    border-radius: 4px;
}

.tree li a {
    color: #46CFB0;
    text-decoration: none;
    line-height: 20pt;
    border-radius: 4px;
}

.tree li a:hover {
    background-color: #34BC9D;
    color: #fff;
}

.active {
    background-color: #34495E;
    color: white;
}

.active a {
    color: #fff;
}

.tree li a.active:hover {
    background-color: #34BC9D;
}

.typeahead__container {
    margin-bottom: 20px;
}
</style>

<body data-type="generalComponents">
    <div class="tpl-content-wrapper" style="padding-left:10px;padding-right:10px;">
        <div class="tpl-portlet-components">
            <div class="tpl-block ">
                <div class="am-g tpl-amazeui-form">
                    <form class="am-form am-form-horizontal" method="post">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-form-group">
                                <div class="portlet-title am-u-md-12">
                                    <div class="caption font-green bold">
                                        商品名称
                                    </div>
                                    <div class="am-u-sm-8">
                                        <input type="text" id="item_name" name="item_name" placeholder="商品名">
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="portlet-title am-u-md-12">
                                    <div class="caption font-green bold">
                                        产品分类
                                    </div>
                                    <div class="am-u-md-4 am-u-sm-push-3">
                                        <div class="caption">123</div>
                                    </div>
                                </div>
                                <div class="am-u-md-12">
                                    <ul class="tree">
                                        <li><a href="">Home</a></li>
                                        <li><span>Category</span>
                                            <ul>
                                                <li><a href="#">jQuery</a>
                                                    <ul>
                                                        <li><a href="#">jQuery</a></li>
                                                        <li><a href="#">jQuery UI</a></li>
                                                        <li><a href="#">jQuery Mobile</a></li>
                                                    </ul>
                                                </li>
                                                <li><a href="#">JavaScript</a>
                                                    <ul>
                                                        <li><a class="active" href="#">AngularJS</a></li>
                                                        <li><a href="#">React</a></li>
                                                        <li><a href="#">Backbone</a></li>
                                                    </ul>
                                                </li>
                                                <li><a href="#suits">Golang</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#about">About</a>
                                            <ul>
                                                <li><a href="#">Contact</a></li>
                                                <li><a href="#">Blog</a></li>
                                                <li><a href="#">Jobs</a>
                                                    <ul>
                                                        <li><a href="#jobs1">Job 1</a></li>
                                                        <li><a href="#jobs2">Job 2</a></li>
                                                        <li><a href="#jobs3">Job 3</a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-6">
                            <div class="portlet-title am-u-md-12">
                            <div class="caption font-green bold">
                                产品标准号
                            </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-12">
                                <div class="am-u-sm-4 am-u-md-4">
                                    <select>
                                        <option>国家标准号</option>
                                        <option>企业标准号</option>
                                    </select>
                                </div>
                                <div class="am-u-sm-8 am-u-md-8">
                                    <input class="am-u-sm-8 am-u-md-8" type="text" name="stn" id="tags" placeholder="键入关键字" />
                                </div>
                            </div>
                            <div class="portlet-title am-u-sm-12" style="margin-top: 20px;">
                                <div class="caption font-green bold">
                                    企业信息
                                </div>
                                <div class="tpl-portlet-input tpl-fz-ml">
                                    <div class="portlet-input input-small input-inline">
                                        <div class="am-u-sm-9 am-u-sm-push-3">
                                            <button type="button" class="am-btn am-btn-primary tpl-btn-bg-color-success addcompany">添加</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="company_info">
                                <div class="am-u-sm-4">
                                    <div class="am-form-group">
                                        <label for="key1" class="am-u-sm-12 am-form-label">名称</label>
                                        <div class="am-u-sm-12">
                                            <input class="company_name" data-id='0' type="text" name="company[name][0]" id="name0" placeholder="key">
                                        </div>
                                    </div>
                                </div>
                                <div class="am-u-sm-4">
                                    <div class="am-form-group">
                                        <label for="key1" class="am-u-sm-12 am-form-label">内容</label>
                                        <div class="am-u-sm-12">
                                            <input type="text" name="company[content][0]" id="content0" placeholder="key">
                                        </div>
                                    </div>
                                </div>
                                <div class="am-u-sm-4">
                                    <div class="am-form-group">
                                        <label for="key1" class="am-u-sm-12 am-form-label">标签</label>
                                        <div class="am-u-sm-12">
                                            <input type="text" name="company[tag][0]" id="tag0" placeholder="key">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-title am-u-sm-12 typelist" style="margin-top: 20px;">
                                <div class="caption font-green bold">
                                    其他
                                </div>
                                <div class="am-u-sm-6 am-u-sm-push-3">
                                    <input type="text" id="addtype" placeholder="添加类型">
                                </div>
                                <div class="tpl-portlet-input tpl-fz-ml">
                                    <div class="portlet-input input-small input-inline">
                                        <div class="am-u-sm-9 am-u-sm-push-3">
                                            <button type="button" class="am-btn am-btn-primary tpl-btn-bg-color-success addtype">添加</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="am-u-sm-12 typelist">
                                <div class="am-form-group">
                                    <label for="key1" class="am-u-sm-2 am-form-label">保质期</label>
                                    <div class="am-u-sm-10">
                                        <input type="text" id="key1" placeholder="key">
                                    </div>
                                </div>
                            </div> -->
                        </div>
                        <div class="portlet-title am-u-md-3">
                            <div class="caption font-green bold">
                                产品图片
                            </div>
                        </div>
                        <div class="am-u-md-3">
                            <img id="img1" src="http://jfb.dev.smfyun.com/stand/assets/img/a5.png" style="width:100%">
                        </div>
                        <div class="am-u-md-3">
                            <img id="img2" src="http://jfb.dev.smfyun.com/stand/assets/img/a5.png" style="width:100%">
                        </div>
                        <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-5">
                                <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="http://jfb.dev.smfyun.com/stand/assets/js/amazeui.min.js"></script>
    <script src="http://jfb.dev.smfyun.com/stand/assets/js/app.js"></script>
    <script src="http://jfb.dev.smfyun.com/stand/assets/js/jquery.treemenu.js"></script>
    <script>
    $(function() {
        $(".tree").treemenu({ delay: 300 }).openActive();
    });
    </script>
    <script type="text/javascript">
    $(function() {
        //产品标准号 自动补齐
        $("#tags").autocomplete({
            // source: availableTags
            source : function(request, response) {
                $.ajax({
                    type : "POST",
                    url : "/smfyun/stand",
                    dataType : "json",
                    cache : false,
                    async : false,
                    data : {
                        "enterprisea100" : $("#tags").val()
                    },
                    success : function(data) {
                        //console.log(data);

                        response($.map(data, function(item) {
                            return {
                                label : item.a100,//下拉框显示值
                                value : item.a100,//选中后，填充到下拉框的值
                            }
                        }));
                    }
                });
            },
            focus : function(event, ui) {
                $("#tags").val(ui.item.value);
                return false;
            },
            select : function(event, ui) {
                $("#tags").val(ui.item.value);
                return false;
            }
        });
        //只监听初始化的
        $('#name0').autocomplete({
            // source: availableTags
            source : function(request, response) {
                console.log('111');
                console.log(request);
                console.log(response);
                $.ajax({
                    type : "POST",
                    url : "/smfyun/stand",
                    dataType : "json",
                    cache : false,
                    async : false,
                    data : {
                        "company_name" : $('#name0').val()
                    },
                    success : function(data) {
                        response($.map(data, function(item) {
                            return {
                                label : item.enterpriseName,//下拉框显示值
                                value : item.enterpriseName,//选中后，填充到下拉框的值
                                desc : item.location,
                            }
                        }));
                    }
                });
            },
            focus : function(event, ui) {
                $("#name0").val(ui.item.value);
                $("#content0").val(ui.item.desc);
                return false;
            },
            select : function(event, ui) {
                $("#name0").val(ui.item.value);
                $("#content0").val(ui.item.desc);
                return false;
            }
        });
    });
    //其他类型
    $('.addtype').click(function(event) {
        var name = $('#addtype').val();
        var n = $('.typelist').length-1;
        console.log(n);
        if (name != '') {
            $('.typelist').last().after('<div class=\"am-u-sm-12 typelist\">' +
                '<div class=\"am-form-group\">' +
                '<label for=\"key1\" class=\"am-u-sm-2 am-form-label\">' + name + '</label>' +
                '<div class=\"am-u-sm-10\">' +
                '<input type=\"hidden\" name=\"other[key]['+n+']\" value=\"'+name+'\" id=\"key1\" placeholder=\"key\">' +
                '<input type=\"text\" name=\"other[value]['+n+']\" value=\"\" id=\"key1\" placeholder=\"value\">' +
                '</div>' +
                '</div>' +
                '</div>');
        }
    });
    //企业信息
    $('.addcompany').click(function(event) {
        //获得现在input框的数量
        var n = $('.company_info').children().length/3;
        console.log(n);
        $('.company_info').append('<div class=\"am-u-sm-4\">' +
            '<div class=\"am-form-group\">' +
            '<label for=\"key1\" class=\"am-u-sm-12 am-form-label\">名称</label>' +
            '<div class=\"am-u-sm-12\">' +
            '<input type=\"text\" data-id=\"'+n+'\"  name=\"company[name]['+n+']\" id=\"name'+n+'\" placeholder=\"key\">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class=\"am-u-sm-4\">' +
            '<div class=\"am-form-group\">' +
            '<label for=\"key1\" class=\"am-u-sm-12 am-form-label\">内容</label>' +
            '<div class=\"am-u-sm-12\">' +
            '<input type=\"text\" name=\"company[content]['+n+']\" id=\"content'+n+'\" placeholder=\"key\">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class=\"am-u-sm-4\">' +
            '<div class=\"am-form-group\">' +
            '<label for=\"key1\" class=\"am-u-sm-12 am-form-label\">标签</label>' +
            '<div class=\"am-u-sm-12\">' +
            '<input type=\"text\" name=\"company[tag]['+n+']\" id=\"tag'+n+'\" placeholder=\"key\">' +
            '</div>' +
            '</div>' +
            '</div>')
        $('#name'+n).autocomplete({
            // source: availableTags
            source : function(request, response) {
                console.log('111');
                console.log(request);
                console.log(response);
                $.ajax({
                    type : "POST",
                    url : "/smfyun/stand",
                    dataType : "json",
                    cache : false,
                    async : false,
                    data : {
                        "company_name" : $('#name'+n).val()
                    },
                    success : function(data) {
                        response($.map(data, function(item) {
                            return {
                                label : item.enterpriseName,//下拉框显示值
                                value : item.enterpriseName,//选中后，填充到下拉框的值
                                desc : item.location,
                            }
                        }));
                    }
                });
            },
            focus : function(event, ui) {
                $("#name"+n).val(ui.item.value);
                $("#content"+n).val(ui.item.desc);
                return false;
            },
            select : function(event, ui) {
                $("#name"+n).val(ui.item.value);
                $("#content"+n).val(ui.item.desc);
                return false;
            }
        });
    });
    //商品名称
    $('#item_name').autocomplete({
        source : function(request, response) {
            $.ajax({
                type : "POST",
                url : "/smfyun/stand",
                dataType : "json",
                cache : false,
                async : false,
                data : {
                    "item_name_post" : $('#item_name').val()
                },
                success : function(data) {
                    response($.map(data, function(item) {
                        return {
                            label : item.item_name,//下拉框显示值
                            value : item.item_name,//选中后，填充到下拉框的值
                        }
                    }));
                }
            });
        },
        focus : function(event, ui) {
            $("#item_name").val(ui.item.label);
            return false;
        },
        select : function(event, ui) {
            $("#item_name").val(ui.item.value);
            return false;
        }
    });
    $('#item_name').change(function(event) {
        $.ajax({
            type : "POST",
            url : "/smfyun/stand",
            dataType : "json",
            cache : false,
            async : false,
            data : {
                "pic_item_name" : $('#item_name').val()
            },
            success : function(res) {
                $('#img1').attr('src', res.pic1);
                $('#img2').attr('src', res.pic2);
            }
        });
    });
    </script>
</body>

</html>
