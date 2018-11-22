
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
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
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                <?=$result['title']?>
            </div>

            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">任务宝</a></li>
                <li><a>任务管理</a></li>
                <li class="am-active"><?=$result['title']?></li>
            </ol>
                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span><?=$result['title']?></span>
                            </div>

                        </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="orders<?=$result['status']?>">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block " style="overflow:-webkit-paged-x;">

                    <div class="am-g tpl-amazeui-form">


                        <div class="am-u-sm-12">
                            <form class="am-form am-form-horizontal" name="ordersform" method="post">

<?php if ($result['error']):?>
            <div class="tpl-content-scope">
                <div class="note note-warning">
                    <p> <?=$result['error']?></p>
                </div>
            </div>
<?php endif?>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">任务名称</label>
                                    <div class="am-u-sm-9">
            <input type="text" class="form-control" id="name" name="data[name]" placeholder="输入任务名称" value="<?=htmlspecialchars($_POST['data']['name'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">任务开始时间</label>
                                    <div class="am-u-sm-9">
  <input name="data[begintime]" id="datetimepicker1" size="16" type="text" value="<?=$_POST['data']['begintime']?date("Y-m-d H:i:s",$_POST['data']['begintime']):''?>" class="am-form-field" readonly>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">任务结束时间</label>
                                    <div class="am-u-sm-9">
  <input name="data[endtime]" id="datetimepicker2" size="16" type="text" value="<?=$_POST['data']['endtime']?date("Y-m-d H:i:s",$_POST['data']['endtime']):''?>" class="am-form-field" readonly>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                <div class="am-u-sm-12 am-u-md-6">
                            <div class="am-btn-toolbar">
                                <div class="am-btn-group am-btn-group-xs">
                                    <button type="button" class="add am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 新增目标级数</button>
                                    <button type="button" class="cut am-btn am-btn-default am-btn-danger"><span class="am-icon-trash-o"></span> 减少目标级数</button>
                                </div>
                            </div>
                        </div>
                        </div>
                                <div class="am-form-group taskbox">
                        <?php if(!$skus):?>
                                <div class="am-form-group task">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p>1级目标</p>
                </div>
            </div>
                                <div class="am-form-group taskgoalbox">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">累计人气值</label>
                                    <div class="am-u-sm-9">
                                    <input type="number" class="form-control goala" name="goal[0]" placeholder="累计人气值" value="">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">选择奖品</label>
                                    <div class="am-u-sm-9">
                                    <select name="prize[0]" class="input-group goalb" data-am-selected="{searchBox: 1}">
                <?php foreach ($items as $item): ?>
                  <option value="<?=$item->id?>"><?=$item->km_content?></option>
                <?php endforeach ?>
                                    </select>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">奖品库存</label>
                                    <div class="am-u-sm-9">
                                    <input type="number" class="form-control goalc" name="stock[0]" placeholder="库存" value="">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">奖品文案</label>
                                    <div class="am-u-sm-9">
                                    <input type="text" class="form-control goald" name="text[0]" placeholder="发送奖励文案" value="">
                                    </div>
                                </div>
                                </div>
            <?php else:?>
                                <div class="am-form-group task">
            <?php foreach ($skus as $k => $v):
            $ordernum=ORM::factory('qwt_rwborder')->where('bid','=',$v->bid)->where('kid','=',$v->id)->where('state','=',1)->count_all();
             $left_num=$v->stock-$ordernum;
            ?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p><?=$k+1?>级目标</p>
                </div>
            </div>
                                <div class="am-form-group taskgoalbox">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">累计人气值</label>
                                    <div class="am-u-sm-9">
                                    <input type="number" class="form-control goala" name="goal[<?=$k?>]" placeholder="累计人气值" value="<?=$v->num?>" <?=($_POST['data']['begintime'] && time()>$_POST['data']['begintime'])?'readonly':''?>>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">选择奖品</label>
                                    <div class="am-u-sm-9">
                                    <select name="prize[<?=$k?>]" class="input-group goalb" data-am-selected="{searchBox: 1}">
                <?php foreach ($items as $item): ?>
                <option value="<?=$item->id?>" <?=$v->iid==$item->id?'selected':''?>><?=$item->km_content?></option>
                <?php endforeach ?>
                                    </select>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">奖品剩余数量</label>
                                    <div class="am-u-sm-9">
                                    <input type="number" class="form-control goalc" name="stock[<?=$k?>]" placeholder="库存" value="<?=$left_num?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">奖品文案</label>
                                    <div class="am-u-sm-9">
                                    <input type="text" class="form-control goald" name="text[<?=$k?>]" placeholder="发送奖励文案" value="<?=$v->text?>">
                                    </div>
                                </div>
                              <?php endforeach?>
                              </div>
                            <?php endif?>
                            </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success "><?=$result['text']?></button>
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
<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
        <script type="text/javascript">
        <?php if ($result['error'] == '请在【个性化设置】->【消息设置】里面配置【下发奖品时的消息模板ID】'):?>
        <?php endif?>
        var goalnumdefault=$(".goala").length;
        $(document).ready(function(){
          goalnumcheck();
        <?php if ($result['error'] == '请在【个性化设置】->【消息设置】里面配置【下发奖品时的消息模板ID】'):?>
        swal({
            title: "添加失败",
            text: "<?=$result['error']?>",
            // imageUrl: window.imgsrc,
            // imageSize: "200x200",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "我知道了",
            closeOnConfirm: true,
        })
        <?php endif?>
        });
        function goalnumcheck(){
            var goalnum = $(".goala").length;
            if (goalnum==goalnumdefault) {
              $('.cut').attr("disabled",true);
            }else{
              $('.cut').attr("disabled",false);
            };
        }
        function toVaild(){
          var i=0;
          $(".goala").each(function (index,element) {
            console.log(index);
            console.log($(".goala").eq(index).val());
              if ($(this).eq(index).val() == "") {
                  i++;
              }else{
                // if(index>0){
                  if($(".goala").eq(index).val()-$(".goala").eq(index+1).val()>0){
                    i++;
                  }
                // }
              }
          })
          console.log(i);
          if(i>0){
            alert('请填写完整并保证每一级数量都大于上一级目标');
            return false;
          }else{
            return true;
          }
          return false;
        }
        <?php if (!$_POST['data']['begintime'] || time()<$_POST['data']['begintime']):?>
          $(function () {
            $("#datetimepicker1").datetimepicker({
              format: "yyyy-mm-dd hh:ii",
              language: "zh-CN",
              minView: "0",
              autoclose: true
            });
          });
        <?php endif?>
          $(function () {
            $("#datetimepicker2").datetimepicker({
              format: "yyyy-mm-dd hh:ii",
              language: "zh-CN",
              minView: "0",
              autoclose: true
            });
          });
          $('.add').click(function() {
            var goalnum = $(".goala").length;//
            if(goalnum==1){
              if($(".goala:last").val()!=''){

                }else{
                  alert('请先填写目标当前目标奖励');
                  return;
                }
              }else{
                if($(".goala:last").val()-$(".goala").eq(goalnum-2).val()>0){

                }else{
                  alert('目标要求人数需要大于上一级');
                  return;
                }
              }
              var min = $(".goala:last").val();
              $(".taskbox").append(

                                "<div class=\"am-form-group task\">"+
            "<div class=\"tpl-content-scope\">"+
                "<div class=\"note note-info\">"+
                    "<p>"+(goalnum+1)+"级目标</p>"+
                "</div>"+
            "</div>"+
                                "<div class=\"am-form-group taskgoalbox\">"+
                                    "<label for=\"user-name\" class=\"am-u-sm-3 am-form-label\">"+"累计人气值"+"</label>"+
                                    "<div class=\"am-u-sm-9\">"+
                                    "<input type=\"number\" class=\"form-control goala\" name=\"goal["+goalnum+"]\" min=\""+min+"\" placeholder=\"累计人气值\" value=\"\">"+
                                    "</div>"+
                                "</div>"+
                                "<div class=\"am-form-group\">"+
                                    "<label for=\"user-name\" class=\"am-u-sm-3 am-form-label\">"+"选择奖品"+"</label>"+
                                    "<div class=\"am-u-sm-9\">"+
                                    "<select name=\"prize["+goalnum+"]\" class=\"input-group goalb\" data-am-selected=\"{searchBox: 1}\">"+
                <?php foreach ($items as $item):?>
                  "<option value=\"<?=$item->id?>\">"+"<?=$item->km_content?>"+"</option>"+
                <?php endforeach ?>
                                    "</select>"+
                                    "</div>"+
                                "</div>"+
                                "<div class=\"am-form-group\">"+
                                    "<label for=\"user-name\" class=\"am-u-sm-3 am-form-label\">"+"奖品库存"+"</label>"+
                                    "<div class=\"am-u-sm-9\">"+
                                    "<input type=\"number\" class=\"form-control goalc\" name=\"stock["+goalnum+"]\" placeholder=\"库存\" value=\"\">"+
                                    "</div>"+
                                "</div>"+
                                "<div class=\"am-form-group\">"+
                                    "<label for=\"user-name\" class=\"am-u-sm-3 am-form-label\">"+"奖品文案"+"</label>"+
                                    "<div class=\"am-u-sm-9\">"+
                                    "<input type=\"text\" class=\"form-control goald\" name=\"text["+goalnum+"]\" placeholder=\"发送奖励文案\" value=\"\">"+
                                    "</div>"+
                                "</div>"+
                              "</div>"
                // "<div class=\"form-group\">"+
                //   "<h4 for=\"name\">"+(goalnum+1)+"级目标</h4>"+
                //   "<label class=\"goaltitle\">累计人气值:</label>"+
                // "<input type=\"number\" class=\"form-control goala\" name=\"goal["+goalnum+"]\" placeholder=\"累计人气值\" value=\"\">"+
                // "<label class=\"goaltitle\">选择奖品:</label>"+
                // "<select name=\"prize["+goalnum+"]\" class=\"input-group goalb\">"+
                //   <?php foreach ($items as $item): ?>
                //     "<option value=\"<?=$item->id?>\">"+
                //     "<?=$item->km_content?>"+
                //     "</option>"+
                //   <?php endforeach ?>
                // "</select>"+
                // "<label class=\"goaltitle\">奖品库存:</label>"+
                // "<input type=\"number\" class=\"form-control goalc\" name=\"stock["+goalnum+"]\" placeholder=\"库存\" value=\"\">"+
                // "<label class=\"goaltitle\">对应文案:</label>"+
                // "<input type=\"text\" class=\"form-control goald\" name=\"text["+goalnum+"]\" placeholder=\"发送奖励文案\" value=\"\">"+
                // "</div>"
                );
              goalnumcheck();
          });
          $('.cut').click(function() {
            var goalnum = $(".task").length;
            if(goalnum==1){
              alert('不能再减少，至少制定一个奖励级数');
            }else{
              $(".task:last").remove();
            }
            goalnumcheck();
          });
        </script>
