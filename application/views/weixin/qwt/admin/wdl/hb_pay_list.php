
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    z-index: 2000;
  }
  label{
    text-align: left !important;
  }
    .search-btn1{
        display: inline-block;
        background-color: white;
    border-radius: 5px;
    border: 1px solid #e5e5e5;
    color: black;
    border-top-left-radius: 5px !important;
    border-bottom-left-radius: 5px !important;
    }
    #datetimepicker1{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
</style>


    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
            红包充值记录(红包雨)
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">管理后台</a></li>
                <li class="am-active">红包充值记录(红包雨)</li>
            </ol>
            <div class="tpl-portlet-components">
                            <form class="am-form">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                <tr>
                  <!-- <th>ID</th> -->
                  <th>公众号名称</th>
                  <th>登录名</th>
                  <th>充值金额</th>
                  <th>时间</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $k=>$v):?>
                <tr>
                  <td><?=$v->login->weixin_name?></td>
                  <td><?=$v->login->user?></td>
                  <td><?=$v->money?></td>
                  <td><?=date('Y-m-d h:i:s',$v->time)?></td>
                </tr>
              <?php endforeach?>
                                    </tbody>
                                </table>
                            <div class="am-u-lg-12">
                                <div class="am-cf">

                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination">
                                        <?=$pages?>
                                        </ul>
                                    </div>
                                </div>
                                <hr>

                            </div>
                        </div>

                    </div>
                </div>
                </form>
                <div class="tpl-alert"></div>
            </div>
        </div>
        </div>
<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
    <script type="text/javascript">
    </script>


