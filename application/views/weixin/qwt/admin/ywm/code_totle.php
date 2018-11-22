<style type="text/css">
    th{
        white-space: nowrap;
    }
    td{
        white-space: nowrap;
    }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                二维码数据统计
            </div>
            <ol class="am-breadcrumb">
                <li class="am-active"><a href="#" class="am-icon-home">二维码数据统计</a></li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-2">
                    <div class="caption font-green bold">
                        二维码数据统计
                    </div>
                    </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12" style="overflow:scroll">
                            <form class="am-form">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
            <th>购买的二维码数量</th>
            <th>已生成的二维码数量</th>
            <th>剩余可生成的二维码数量</th>
            <th>已扫码的二维码数量</th>
            <th>扫码率</th>
                </tr>
            </thead>
            <tbody>
            <tr class="gradeX">
                <td><?=$result['hbnum']?></td>
                <td><?=$result['has_created']?></td>
                <td><?=$result['residue_num']?></td>
                <td><?=$result['has_scan']?></td>
                <td><?=$result['scan_rate'].'%'?></td>
                <td>
            </tr>
      
                                    </tbody>
                                </table>
                            </form>
                            <div class="am-u-lg-12">
                                <div class="am-cf">
                                    </div>
                                </div>
                                <hr>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
        </div>
    </div>
        <script type="text/javascript">
        $('#type').change(function(){
            var a = $(this).val();
            console.log(a);
            $('#searchtype').submit();
        })
        </script>
