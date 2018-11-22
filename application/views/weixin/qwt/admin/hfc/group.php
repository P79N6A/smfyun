<style type="text/css">
    .swaltable{
        width: 100%;
    }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                成团管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">充值拼团</a></li>
                <li><a href="#">成团管理</a></li>
                <li class="am-active"></li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        共 <?=count($group)?> 个
                    </div>
                    </div>
                    <form method="get" id="rankform">
                                    <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按用户昵称、商品名称搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead><tr>
                  <th>团长昵称</th>
                  <th>团长头像</th>
                  <th>参与商品</th>
                  <th>商品原价</th>
                  <th>付款金额</th>
                  <th>完成度</th>
                  <th>发起时间</th>
                </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($group):?>
                                        <?php foreach ($group as $k => $v):?>
                                            <tr>
                                                <!-- <td><?=$v->id?></td> -->
                                                <td><?=$v->trades->qrcodes->nickName?></td>
                                                <td><img src="<?=$v->trades->qrcodes->avatarUrl?>" style="height:20px;"></td>
                                                <td><?=$v->items->name?></td>
                                                <td><?=$v->items->old_price?></td>
                                                <td><?=$v->items->price?></td>
                                                <?php
                                                $nownum = ORM::factory('qwt_hfctrade')->where('bid','=',$bid)->where('teamid','=',$v->id)->count_all();?>
                                                <?php if ($v->trades->pintype==1):?>
                                                <td><a id="swal<?=$v->id?>"><?=$nownum?>/<?=$v->items->groupnum?></a></td>
                                                <?php
                                                 $trades = ORM::factory('qwt_hfctrade')->where('bid','=',$bid)->where('teamid','=',$v->id)->find_all();?>
<script type="text/javascript">
    $('#swal<?=$v->id?>').click(function(){
        swal({
            title: "团成员明细",
            text: "<table class='swaltable'><thead><tr><th>昵称</th><th>头像</th><th>付款金额</th><th>时间</th></tr></thead><tbody><?php foreach ($trades as $k => $v):?><tr><td><?=$v->qrcodes->nickName?></td><td><img src='<?=$v->qrcodes->avatarUrl?>' style='height:20px;'></td><td><?=$v->payment?></td><td><?=date('Y-m-d H:i:s',$v->createdtime)?></td></tr><?php endforeach?></tbody></table>",
            // imageUrl: window.imgsrc,
            // imageSize: "200x200",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "我知道了",
            closeOnConfirm: true,
            closeOnCancel: true,
            html: true,
        })
    })
</script>
                                            <?php else:?>
                                                <td>自购</td>
                                            <?php endif?>
                                                <td><?=date('Y-m-d H:i:s',$v->lastupdate)?></td>
                                            </tr>
                                        <?php endforeach;?>
                                    <?php endif;?>
                                    </tbody>
                                </table>
                            </form>
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
                <div class="tpl-alert"></div>
            </div>










        </div>

    </div>


