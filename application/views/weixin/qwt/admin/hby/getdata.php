
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                概况
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">红包雨</a></li>
                <li><a href="#">数据统计</a></li>
                <li class="am-active">概况</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            概况
                        </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main">
                                    <thead>
                <tr>
                    <th>购买的红包码配额</th>
                    <th>生成的红包码数量</th>
                    <th>已消耗的红包码数</th>
                    <th>剩余的红包码配额</th>
                    <!-- <th>总的发送金额</th> -->
                </tr>
                                    </thead>
                                    <tbody>
                <tr style="text-align:center">
              <td><?=$result['buynum']?></td>
              <td><?=$result['creatnum']['normal']?></td>
              <td><?=$result['used']['total']?></td>
              <th><?=$result['buynum']-$result['used']['normal']?></th>
              <!-- <th><?=$result['money']?>元</th> -->
            </tr>
                                    </tbody>
                                </table>

                        </div>

                    </div>
                    <div class="tpl-content-scope">
                            <!-- <div class="note note-info" style="color:red">
                                <p> 注意:裂变出来的口令不计入消耗，请根据剩余的口令配额判断是否需要续费。</p>
                            </div> -->
                        </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
        </div>

    </div>

