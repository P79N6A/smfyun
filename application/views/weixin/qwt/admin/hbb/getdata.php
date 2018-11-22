
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                概况
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">口令红包</a></li>
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
                    <th>购买的口令配额</th>
            <th>生成的原始口令数</th>
            <th>裂变出来的口令数</th>
            <th>已生成的口令数</th>
            <th>原始口令已消耗</th>
            <th>裂变口令已消耗</th>
            <th>已消耗的口令数</th>
            <th>剩余的口令配额</th>
                </tr>
                                    </thead>
                                    <tbody>
                <tr style="text-align:center">
              <td><?=$result['buynum']?></td>
              <td><?=$result['creatnum']['normal']?></td>
              <td><?=$result['creatnum']['liebian']?></td>
              <td><?=$result['creatnum']['total']?></td>
              <td><?=$result['used']['normal']?></td>
              <td><?=$result['used']['liebian']?></td>
              <td><?=$result['used']['total']?></td>
              <th><?=$result['buynum']-$result['used']['normal']?></th>
            </tr>
                                    </tbody>
                                </table>

                        </div>

                    </div>
                    <div class="tpl-content-scope">
                            <div class="note note-info" style="color:red">
                                <p> 注意:裂变出来的口令不计入消耗，请根据剩余的口令配额判断是否需要续费。</p>
                            </div>
                        </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
        </div>

    </div>

