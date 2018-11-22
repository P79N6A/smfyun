
<style type="text/css">
    label{
        text-align: left !important;
    }
    .loc .am-form-group select{
        width: 30% !important;
        display: inline-block !important;
    }
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                发送规则
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">消息宝</a></li>
                <li>发送规则</li>
                <li class="am-active">规则设置</li>
            </ol>
            <div class="tpl-portlet-components" style="overflow: -webkit-paged-x;">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            规则设置
                        </div>
                </div>
                <div class="am-u-sm-12 am-u-md-12">
                        <div class="tpl-form-body tpl-amazeui-form">
                            <form class="am-form am-form-horizontal" method="post" enctype="multipart/form-data">
<?php if ($result['err']):?>
            <div class="tpl-content-scope">
                <div class="note note-danger">
                    <p> <?=$result['err']?> </p>
                </div>
            </div>
          <?php endif?>
                                <div class="am-form-group gendar">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">性别</label>
                                    <div class="am-u-sm-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-0" onclick="change(0)" class="switch-type green <?=$gendar0==3 ? 'green-on' : ''?>">全部</li>
                                    <li id="switch-1" onclick="change(1)" class="switch-type green <?=$gendar0==1 ? 'green-on' : ''?>">男</li>
                                    <li id="switch-2" onclick="change(2)" class="switch-type green <?=$gendar0==2 ? 'green-on' : ''?>">女</li>
                                    <li id="switch-3" onclick="change(3)" class="switch-type green <?=$gendar0==0 ? 'green-on' : ''?>">未知性别</li>
                                    <?php if ($result['action']=='edit'):?>
                                    <input id="gendar" type="hidden" value="<?=$gendar0?>" name="rule[gendar]">
                                    <?php else:?>
                                    <input id="gendar" type="hidden" value="0" name="rule[gendar]">
                                <?php endif?>
                        </ul>
                            </div>
                            </div>
                            </div>
                            <div class="am-form-group">
                        <div class="am-u-sm-12 loc" id="city1">
                                    <label for="user-name" class="am-form-label">地区</label>
                        <div class="am-form-group">
                          <select class="prov" name="area[pro]"></select>
                          <select class="city" name="area[city]" disabled="disabled"></select>
                          <select class="dist" name="area[dis]" disabled="disabled"></select>
                        </div>
                        </div>
                        </div>
                        <?php if(count($items)==0):?>
            <div class="tpl-content-scope">
                <div class="note note-danger">
                    <p> 您还没有添加发送内容，请先前往消息设置添加发送内容 </p>
                </div>
            </div>
        <?php endif?>
                                <div class="am-form-group" style="<?count($items)==0 ? 'display:none' : ''?>" >
                                    <label for="user-phone" class="am-u-sm-3 am-form-label">发送内容</label>
                                    <div class="am-u-sm-9">
                                        <select data-am-selected="{searchBox: 1}" name="rule[content]" value="<?=$iid?>">
                                        <?php foreach ($items as $k => $v):?>
  <option value="<?=$v->id?>" <?=$v->id==$iid?'selected':''?>><?=$v->name?></option>
<?php endforeach?>
</select>
                                    </div>
                                </div>
                        <div class="am-u-sm-12" style="padding:0">
                        <hr>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-success"><i class="fa fa-edit"></i>保存发送规则</button>
      <?php if ($result['action'] == 'edit'):?>
      <a class="am-btn am-btn-danger" id="delete"><i class="fa fa-remove"></i> <span>删除该规则</span></a>
    <?php endif?>
                        </div>
                </div>
                </div>
                </form>
            </div>
        </div>

    </div>

                <script src="http://<?=$_SERVER['HTTP_HOST']?>/qwt/js/jquery.cityselect.js"></script>
                <script src="/wdy/plugins/citySelect/city.min1.js"></script>
<script>

<?php if($result['err']):?>
$(document).ready(function(){
    swal({
        title: "失败",
        text: "<?=$result['err']?>",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "我知道了",
        closeOnConfirm: true,
    })
})
<?php endif?>
$(document).ready(function(){
    var a = $('#gendar').val();
    var b = a.length;
    console.log(b);
})
function change(i){
    $('.switch-type').removeClass('green-on');
    if (i==0) {
        $('#switch-0').addClass('green-on');
        $('#gendar').val(3);
    };
    if (i==1) {
        $('#switch-1').addClass('green-on');
        $('#gendar').val(1);
    };
    if (i==2) {
        $('#switch-2').addClass('green-on');
        $('#gendar').val(2);
    };
    if (i==3) {
        $('#switch-3').addClass('green-on');
        $('#gendar').val(0);
    };
  }
  <?php if($result['action']=='add'):?>
                  $("#city1").citySelect({
                    prov:'',
                    city:'',
                    dist:'',
                    required:false
                  });
                  <?php else:?>
                  $("#city1").citySelect({
                    prov:'<?=$pro0?>',
                    city:'<?=$city0?>',
                    dist:'<?=dis0?>',
                    required:false
                  });
                  <?php endif?>
    $('#delete').click(function(){
  swal({
    title: "确认要删除吗？",
    text: "该操作不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认删除',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwtxxba/rules/edit/<?=$rid?>?DELETE=1";
    })
  })
</script>


