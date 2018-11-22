
<div class="page-heading">
            <h3>
                账号管理
            </h3>
        </div>
<section class="wrapper">
        <!-- page start-->
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        修改用户
                    </header>
                    <div class="panel-body">
                    <?php if ($result['error']):?>
                      <div class="alert alert-danger alert-dismissable"><?=$result['error']?></div>
                    <?php endif?>
                        <form role="form" method="post">
                        <div  style="width:35%">
                            <div class="form-group">
                                <label class="col-sm-3 col-sm-3" for="exampleInputPassword1">用户名</label>
                                <input type="text" maxlength="20" class="form-control" id="exampleInputPassword1" name="data[user]" placeholder="输入登录用户名" value="<?=htmlspecialchars($_POST['data']['user'])?>">
                            </div>
                             <div class="form-group">
                                <label class="col-sm-3 col-sm-3" for="exampleInputPassword1">密码</label>
                                <input type="password" maxlength="20" name="pass" placeholder="输入登录密码" class="form-control" id="exampleInputPassword1" placeholder="为空就不做修改">
                            </div>
                             <div class="form-group">
                                <label class="col-sm-3 col-sm-3" for="exampleInputPassword1">邀请码</label>
                                <input type="text" maxlength="20" name="data[code]" placeholder="" class="form-control" id="exampleInputPassword1" placeholder="为空就不做修改" value="<?=trim($_POST['data']['code'])?>">
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 col-sm-3" for="exampleInputPassword1">备注</label>
                                <input type="text" maxlength="20" class="form-control" id="exampleInputPassword1" placeholder="备注" name="data[memo]" placeholder="输入商户名称" value="<?=trim($_POST['data']['memo'])?>">
                            </div>
                        </div>
                        <?php
                        function exist($id,$bid){
                            $end = ORM::factory('qwt_buy')->where('bid', '=', $bid)->where('status', '=', 1)->where('iid', '=', $id)->find();
                            if($end->id){
                                return true;
                            }else{
                                return false;
                            }
                        }
                        function pro($id,$bid){
                            $buyitem = ORM::factory('qwt_buy')->where('bid', '=', $bid)->where('status', '=', 1)->where('iid', '=', $id)->find();
                            if($buyitem->iid==1){
                                $pro = $buyitem->hbnum;
                            }else{
                                $pro = $buyitem->expiretime;
                            }
                            return $pro;
                        }
                        ?>
                            <?php if ($result['action'] == 'edit'):?>
                            <div class="form-group">
                                <label for="exampleInputEmail1">修改产品购买权限</label><br>
                                <?php foreach ($items as $item):?>
                                    <label style="width:180px;"><input name="item[<?=$item->id?>]" <?=exist($item->id,$bid)?"checked='checked'":"";?> type="checkbox" value="<?=$item->id?>" /><?=$item->name?>
                                    <?php if($item->name=='口令红包'):?>
                                        <input style="width:100%" class="form-control" name="pro[<?=$item->id?>]" placeholder='单位：个' type="text" value="<?=pro($item->id,$bid)?pro($item->id,$bid):'';?>" />
                                    <?php else:?>
                                        <input style="width:100%" name="pro[<?=$item->id?>]" size="16" value="<?=date("Y-m-d H:i:s",pro($item->id,$bid)?pro($item->id,$bid):time());?>" readonly="" class="form_datetime form-control" type="text">
                                    <?php endif?>
                                    </label>
                                <?php endforeach?>
                            </div>
                            <?php else:?>
                            <div class="form-group">
                                <label for="exampleInputEmail1">添加产品购买权限</label><br>
                                 <?php foreach ($items as $item):?>
                                    <label style="width:180px;"><input name="item[<?=$item->id?>]" type="checkbox" value="<?=$item->id?>" /><?=$item->name?>
                                    <?php if($item->name=='口令红包'):?>
                                        <input style="width:100%" class="form-control" name="pro[<?=$item->id?>]" placeholder='单位：个' type="text" value="" />
                                    <?php else:?>
                                        <input style="width:100%" name="pro[<?=$item->id?>]" size="16" value="<?=date("Y-m-d H:i:s",time())?>" readonly="" class="form_datetime form-control" type="text">
                                    <?php endif?>
                                    </label>
                                 <?php endforeach?>
                            </div>
                            <?php endif?>
                            <?php if ($result['action'] == 'edit'):?>
                                <button type="submit" class="btn btn-primary">修改用户</button>
                            <?php else:?>
                                <button type="submit" class="btn btn-primary">添加用户</button>
                            <?php endif?>

                        </form>

                    </div>
                </section>
<script src="http://cdn.bootcss.com/jquery/2.0.1/jquery.js"></script>

