
<style type="text/css">
    #datetimepicker1{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    #datetimepicker2{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
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
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    z-index: 2000;
  }
  .nickname{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
if ($result['fuser']) $title = $result['fuser']->nickname.'的下线';
if ($result['ffuser']) $title=$result['ffuser']->nickname.'的二级';
if ($result['id'] || $result['s']) $title = '搜索结果';
if ($result['ticket']) $title = '已生成海报';
?>


    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                仓库中的商品
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">代理哆</a></li>
                <li class="am-active">商品管理</li>
            </ol>
            <div class="tpl-portlet-components">
                            <form class="am-form">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共<? if($result['countall']) echo $result['countall'];else echo 0;?> 种出售的商品
                    </div>
                    </div>
              <form method="get" name="qrcodesform">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按商品名称搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtdlda/setgoods1?refresh=1" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-refresh"></span> 刷新商品列表</a>
                        </div>
                </form>


                </div>
            <?php
            $config['kaiguan_needpay']=ORM::factory('qwt_dldcfg')->where('bid','=',$bid)->where('key','=','kaiguan_needpay')->find()->value;
            ?>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                <tr>
                  <th>缩略图</th>
                  <th>商品名称</th>
                  <th>零售价(元)</th>
                  <th>库存</th>
                  <th>代理哆上的销量</th>
                  <th>是否允许分享</th>
                  <th>操作</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php foreach ($result['goods'] as $v){
                   $num_iid=$v->num_iid;
                  $soldednum=DB::query(database::SELECT,"select sum(temp.num)as tonum from (SELECT qwt_dldorders.* FROM `qwt_dldtrades`,qwt_dldorders WHERE qwt_dldorders.tid=qwt_dldtrades.tid and qwt_dldtrades.status!='TRADE_CLOSED' and qwt_dldtrades.status!='TRADE_CLOSED_BY_USER' and qwt_dldtrades.status!='NO_REFUND' and qwt_dldorders.goodid=$num_iid) as temp where temp.bid=$bid")->execute()->as_array();
                    //var_dump($soldednum);
                ?>
                <tr>
                  <td style="border-top:2px solid #e5e5e5;"><img src="<?=$v->pic?>" width="32" height="32"></td>
                  <td style="border-top:2px solid #e5e5e5; width:30%;word-wrap:break-word;word-break:break-all;"><?=$v->title?></td>
                  <td style="border-top:2px solid #e5e5e5;"><?=$v->price?></td>
                  <td style="border-top:2px solid #e5e5e5;"><?=$v->num?></td>
                  <td style="border-top:2px solid #e5e5e5;"><?=empty($soldednum[0]['tonum'])?0:$soldednum[0]['tonum']?><?//=$v['sold_num']?></td>
                  <td style="border-top:2px solid #e5e5e5;">
                  <?php
                  if ($v->status == 0)
                    echo '<span class="label label-warning">不允许</span>';
                  if ($v->status == 1)
                    echo '<span class="label label-danger">允许</span>';
                  ?>
                  </td>
                  <td style="border-top:2px solid #e5e5e5;" nowrap="">
                    <a class="edit am-btn am-btn-danger am-btn-xs" data-toggle="modal" data-target="#actionModel" data-num_id="<?=$v->num_iid?>" data-price="<?=$v->price?>"  data-name="<?=$v->title?>" data-money="<?=$v->money?>"  data-status="<?=$v->status?>" data-type="<?=$v->type?>" >
                      <span>修改</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                  <input type="hidden" value='<?=$num_iid?>'>
                </tr>

                <?php
                  if ($v->type==1) {
                    $skus = ORM::factory('qwt_dldgoodsku')->where('bid','=',$bid)->where('item_id','=',$v->num_iid)->find_all();
                    foreach ($skus as $sku){
                      echo '<tr>
                              <td></td>
                              <td>'.$sku->title.'</td>
                              <td>'.$sku->price.' </td>
                              <td>'.$sku->num.'</td>
                              <td></td>
                              <td></td>
                              <td></td>
                            </tr>';
                    }
                  }
                }?>
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
 <div class="shadow useredit" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:20%;margin-left:0;width:60%;max-height:80%;overflow:scroll;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold nickname">
                      确认修改
                    </div>
                  </div>
                </div>
          <div class="am-tabs tpl-index-tabs" data-am-tabs>
            <div class="am-tabs-bd">
              <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                <div id="wrapperA" class="wrapper">
                  <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                      <div class="am-u-sm-12">
                        <form method="post" class="am-form am-form-horizontal" name="qrcodesform" >
                        <div class="modal-body"></div>
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">修改</button>
                            </div>
                          </div>
                          </form>

                          </div>
                          </div>
                          </div>
                          </div>
                          </div>
                          </div>
                          </div>
</div>
</div>
</div>
</div>


<script>
<?php
$arr = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
?>
window.skus = Array();
window.smoney = Array();
window.suite = Array();
<?php foreach ($result['goods'] as $v) {
    if($v->type==1){
      $skus2 = ORM::factory('qwt_dldgoodsku')->where('bid','=',$bid)->where('item_id','=',$v->num_iid)->find_all();
      echo "skus[\"".$v->num_iid."\"]= Array();";
      foreach ($skus2 as $key=>$sku2) {
        echo "skus[\"".$sku2->item_id."\"][".$key."]= Array();";
        echo "skus[\"".$sku2->item_id."\"][".$key."][\"price\"] = \"".$sku2->price."\";";
        echo "skus[\"".$sku2->item_id."\"][".$key."][\"moeny\"] = \"".$sku2->money."\";";
        echo "skus[\"".$sku2->item_id."\"][".$key."][\"title\"] = \"".$sku2->title."\";";
        echo "skus[\"".$sku2->item_id."\"][".$key."][\"skuid\"] = \"".$sku2->id."\";";
        echo "skus[\"".$sku2->item_id."\"][".$key."][\"suite\"] = Array();";
        foreach ($result['suite'] as $k3=>$v3) {
          $sku = ORM::factory('qwt_dldgoodsku')->where('id','=',$sku2->id)->find();
          $value = ORM::factory('qwt_dldsmoney')->where('bid','=',$bid)->where('sid','=',$v3->id)->where('sku_id','=',$sku->sku_id)->find();
          echo "skus[\"".$sku2->item_id."\"][".$key."][\"suite\"][".$v3->id."] = Array();";
          echo "skus[\"".$sku2->item_id."\"][".$key."][\"suite\"][".$v3->id."][\"money\"] = \"".$value->money."\";";
          $value->id = (string)$value->id?$value->id:$arr[$key].$arr[$k3];
          echo "skus[\"".$sku2->item_id."\"][".$key."][\"suite\"][".$v3->id."][\"smoneyid\"] = \"".$value->id."\";";
          echo "skus[\"".$sku2->item_id."\"][".$key."][\"suite\"][".$v3->id."][\"name\"] = \"".$value->suite->name."\";";
          echo "skus[\"".$sku2->item_id."\"][".$key."][\"suite\"][".$v3->id."][\"skuid\"] = \"".$sku2->id."\";";
        }
      }
    }else{
      echo "skus[\"".$v->num_iid."\"]= Array();";
      echo "skus[\"".$v->num_iid."\"][0]= Array();";
      echo "skus[\"".$v->num_iid."\"][0][\"suite\"]= Array();";
        foreach ($result['suite'] as $k3=>$v3) {
          $value = ORM::factory('qwt_dldsmoney')->where('bid','=',$bid)->where('sid','=',$v3->id)->where('item_id','=',$v->num_iid)->find();
          echo "skus[\"".$v->num_iid."\"][0][\"suite\"][".$v3->id."] = Array();";
          echo "skus[\"".$v->num_iid."\"][0][\"suite\"][".$v3->id."][\"money\"] = \"".$value->money."\";";
          $value->id = (string)$value->id?$value->id:$arr[$key].$arr[$k3];
          echo "skus[\"".$v->num_iid."\"][0][\"suite\"][".$v3->id."][\"smoneyid\"] = \"".$value->id."\";";
          echo "skus[\"".$v->num_iid."\"][0][\"suite\"][".$v3->id."][\"name\"] = \"".$value->suite->name."\";";
        }
    }
}?>
<?php foreach ($result['suite'] as $k1=>$v1) {
   echo "suite[".$v1->id."] = Array();";
   echo "suite[".$v1->id."]['name'] = \"".$v1->name."\";";
}?>
console.log(skus);
$(document).ready(function() {
  <?php if($gnum>0):?>
    // gselect = '<label for="fscore">选择分组：</label>';
    select = '<option value=\"0\">不加入分组</option>';
    <?php foreach ($suite as $k => $v):?>
        select = select+ "<option value=\"<?=$v->id?>\"><?=$v->name?></option>";
    <?php endforeach?>
    $('#groupselect').html(select);
  <?php else:?>
  $('#groupselect').parent().parent().hide();
  <?php endif?>
});
$('.edit').on('click',function(){
  var id = $(this).data('num_id').toString();
  var name = $(this).data('name');
  var uname = $(this).data('uname');
  var tel = $(this).data('tel');
  var bz = $(this).data('bz');
  var lv = $(this).data('lv');
  var fid = $(this).data('fid');
  var fname = $(this).data('fname');
  var price=$(this).data('price');
  var money=$(this).data('money');
  var status=$(this).data('status');
  var information=name;
  var type = $(this).data('type');
  // $('#userid').val(id);
  // $('.nickname').text(name);
  // $('#fname').val(uname);
  // $('#ftel').val(tel);
  // $('#bz').val(bz);
  // $('#flock').val(lv);
  // $('#groupselect').val(suite);
  // if (lv==1) {$('#switch-1').addClass('green-on')}else{$('#switch-4').addClass('red-on')};
  if(type == 1){
    var form = '';
    // console.log(skus[id]);
    // console.log(skus[id]['price']);
    // // console.log(skus[id][0]["price"]);
    // // console.log(skus[id][1]["price"]);
    // // console.log(skus[id][0]["title"]);

      for(i=0;skus[id][i];i++){
        console.log(skus[id][i]["price"]);
        console.log(skus[id][i]["moeny"]);
        console.log(skus[id][i]["title"]);
        var smoney = '';
        $.each(skus[id][i]["suite"], function(index, value, array){
          if(value){
            smoney = smoney + '<br><br><label for="fscore">'+suite[index]['name']+'拿货价（元）：</label><input class="form-control" id="fscore" name="form[smoney]['+value['smoneyid']+'][money]" max="999" type="number" step="0.01" style="width:150px; display:inline-block;" value="'+value['money']+'"><input name="form[smoney]['+value['smoneyid']+'][skuid]" type="hidden" value="'+value['skuid']+'"><input name="form[smoney]['+value['smoneyid']+'][suiteid]" type="hidden" value="'+index+'">';
          }
        });
        // for (a =1; skus[id][i]["suite"][a]; a++) {
        //   smoney = smoney + '<br><br><label for="fscore">'+suite[a-1]['name']+'拿货价（元）：</label><input class="form-control" id="fscore" name="form[smoney]['+skus[id][i]['suite'][a]['smoneyid']+'][money]" max="999" type="number" step="0.01" style="width:150px; display:inline-block;" value="'+skus[id][i]['suite'][a]['money']+'"><input name="form[smoney]['+skus[id][i]['suite'][a]['smoneyid']+'][skuid]" type="hidden" value="'+skus[id][i]['suite'][a]['skuid']+'"><input name="form[smoney]['+skus[id][i]['suite'][a]['smoneyid']+'][suiteid]" type="hidden" value="'+a+'">';
        // };
        form+= '<div class="form-group"><label for="fscore">规格：'+skus[id][i]['title']+'</label><br><label for="fscore">零售价（元）：'+skus[id][i]['price']+'</label><br><label for="fscore">普通拿货价（元）：</label><input class="form-control" id="fscore" name="form[money]['+skus[id][i]['skuid']+']" max="999" type="number" step="0.01" style="width:150px; display:inline-block;" value="'+skus[id][i]['moeny']+'">'+smoney+'</div>'+'<hr>';
      }
      // form+='<div class="form-group"><label for="fscore">拿货价（元）</label><input class="form-control" id="fscore" name="form[money]" max="999" type="number" step="0.01" style="width:150px" value="'+money+'"></div>';
     form += '<div class="form-group"><label for="flock">商品审核（审核通过后用户可以分享）：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="status0" value="0" '+ (status==0 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">不通过</span></label> <label class="checkbox-inline"><input type="radio" name="form[status]" id="flock1" value="1"'+ (status==1 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">通过</div></div>';
      form += '<input type="hidden" name="form[num_iid]" value="'+ id +'">';
      form += '<input type="hidden" name="form[type]" value="'+ type +'">';
      form += '<input type="hidden" name="form[title]" value="'+ name +'">';
  }else{
    var form = '';
    var smoney = '';
    $.each(skus[id][0]["suite"], function(index, value, array) {
      // console.log(skus[id][0]["suite"][index]);
      console.log(index);
      console.log(value);
      if(value){
        smoney = smoney + '<label for="fscore">'+suite[index]['name']+'拿货价（元）：</label><input class="form-control" id="fscore" name="form[smoney]['+value['smoneyid']+'][money]" max="999" type="number" step="0.01" style="width:150px; display:inline-block;" value="'+value['money']+'"><br><br><input name="form[smoney]['+value['smoneyid']+'][suiteid]" type="hidden" value="'+index+'">';
      }
    });
    // for (var i = 1; skus[id][0]["suite"][i]; i++) {
    //   console.log(skus[id][0]["suite"][i]);
    //   smoney = smoney + '<label for="fscore">'+suite[i-1]['name']+'拿货价（元）：</label><input class="form-control" id="fscore" name="form[smoney]['+skus[id][0]['suite'][i]['smoneyid']+'][money]" max="999" type="number" step="0.01" style="width:150px; display:inline-block;" value="'+skus[id][0]['suite'][i]['money']+'"><br><br><input name="form[smoney]['+skus[id][0]['suite'][i]['smoneyid']+'][suiteid]" type="hidden" value="'+i+'">';
    // };
      form+='<div class="form-group"><label for="fscore">普通拿货价（元）</label><input class="form-control" id="fscore" name="form[money]" max="999" type="number" step="0.01" style="width:150px" value="'+money+'"></div>';
      form = form+smoney;
     form += '<div class="form-group"><label for="flock">商品审核（审核通过后用户可以分享）：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="status0" value="0" '+ (status==0 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">不通过</span></label> <label class="checkbox-inline"><input type="radio" name="form[status]" id="flock1" value="1"'+ (status==1 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">通过</div></div>';
      form += '<input type="hidden" name="form[num_iid]" value="'+ id +'">';
      form += '<input type="hidden" name="form[title]" value="'+ name +'">';
  }
  $('.nickname').text(information);
  $('.modal-body').html(form);
  $('.useredit').fadeIn();
})
$(document).on('click','.close',function(){
    $(".shadow").fadeOut(500);
});
                    $('#switch-1').click(function(){
                      $('#flock').val(1);
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('green-on');
                    });
                    $('#switch-4').click(function(){
                      $('#flock').val(3);
                      $('#switch-1').removeClass('green-on');
                      $(this).addClass('red-on');
                    });
</script>
