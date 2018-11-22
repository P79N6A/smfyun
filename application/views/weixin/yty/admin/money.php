<style type="text/css">
  .tag{
    display: inline-block;
    border:1px solid #CAC1C1;
    padding:5px;
    margin-left: 10px;
    border-radius: 5px;
    margin-top: 5px;
  }
  .tactive{
    background-color: rgb(255, 232, 148);
  }
  .box-tools{
    display: inline-block;
    position: absolute;
    top: 4px;
    left: -116px
  }
  #inputNum1{
    display: inline-block;
    position: absolute;
    top: 0px;
    left: -333px;
    height: 33px;
    width: 150px;
    border-radius: 10px;
    background-color: #fff;
    border: 1px solid #A2CD5A;
  }
  #inputNum2{
    display: inline-block;
    position: absolute;
    left: -154px;
    height: 33px;
    width: 150px;
    border-radius: 10px;
    top:0px;
    background-color: #fff;
    border: 1px solid #A2CD5A;
  }
  #add89{
    display: inline-block;
    width: 50px;
    height: 33px;
    line-height: 19px;
    border-radius: 10px;
    margin-left: 10px;
  }
  .add88{
    display: inline-block;
    border-radius: 10px;
    background-color: #fff;
  }
  #ssbtn{
    display: inline-block;
    width: 40px;
    height: 33px;
    border-radius: 10px;
    margin-left: 3px;
    background-color: #fff;
    border: 1px solid #A2CD5A;
  }
  .text88{
    position: absolute;
    top: 5px;
    left: -177px;
    font-size: 16px;
  }
</style>
<section class="content-header">
  <h1>
    概况
    <small><?=$desc?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/ytya/money">概况</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-lg-12">


      <div class="nav-tabs-custom">

          <div class="tab-pane active" id="orders<?=$result['status']?>">
            <div class="table-responsive">
            <form method="get">
             <table class="table table-hover">
                <tbody>
                  <tr>
                    <th>累计提现金额</th>
                    <th>待提现金额</th>
               </tr>
                  <tr>
                    <td><?=-$result['money_has']?></td>
                    <td><?=$result['money_having']?></td></td>
                  </tr>
                  </tbody>
             </table>
            </form>
            </div><!-- table-resonsivpe -->
          </div><!-- tab-pane -->
      </div><!-- nav-tabs-custom -->
    </div>
  </div>
</section><!-- /.content -->
