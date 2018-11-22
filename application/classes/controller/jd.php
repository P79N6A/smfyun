<?php defined('SYSPATH') or die('No direct script access.');

class Controller_jd extends Controller{
        //系统配置
    public function action_oauth1(){
        Request::instance()->redirect('https://oauth.jd.com/oauth/authorize?response_type=code&client_id=AE7B3A686D5478629686FC3364909963&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/jd/callback1&state=teststate');
    }
    public function action_callback1(){
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
            $state=$_GET["state"];
            echo $code.'<br>';
            echo $state.'<br>';
        }
        $url='https://oauth.jd.com/oauth/token?grant_type=authorization_code&client_id=AE7B3A686D5478629686FC3364909963&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/jd/callback1&code='.$code.'&state='.$state.'&client_secret=a52391525b2e41118eaef7ae9797810e';
        $data=array();
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        $result=json_decode($output);
        echo '<pre>';
        var_dump($result);
        echo '<pre>';
        // if(isset($result->access_token)){
        //     echo "<script>alert('授权成功');location.href='".URL::site("flba/home")."';</script>";
        // }
        exit;
    }
    public function action_corn1(){
        Request::instance()->redirect('https://oauth.jd.com/oauth/token?client_id=61CD25506688EAC2AB5BDA47D04F1B4B&client_secret=02044f81a99c4b4d93d07da9d12a5bdd&grant_type=refresh_token&refresh_token=bd14ad9d-1e07-468d-bacc-a949aa2e8c3c');
        exit;
    }
    public function action_test(){
        require_once Kohana::find_file('vendor', 'jd/JdClient');
        //require_once Kohana::find_file('vendor', 'jd/request/Seller/SellerVenderInfoGetApi');
        require_once Kohana::find_file('vendor', 'jd/OrderSearchRequest');
        $c = new JdClient;
        $c->appKey = "61CD25506688EAC2AB5BDA47D04F1B4B";
        $c->appSecret = "02044f81a99c4b4d93d07da9d12a5bdd";
        $c->accessToken = "268d6075-4280-4501-8b03-4d0383591d23";
        //$c->serverUrl = "https://api.jd.com/routerjson";
        $req = new OrderSearchRequest;
        $req->setPage(1);
        $req->setPageSize(100);
        $req->setOrderState('WAIT_SELLER_STOCK_OUT');
        $resp = $c->execute($req);
        echo '<pre>';
        var_dump($resp);
        echo '<pre>';
        //Request::instance()->redirect('https://api.jd.com/routerjson?360buy_param_json={""}&access_token=12345678-b0e1-4d0c-9d10-a998d9597d75&app_key=61CD25506688EAC2AB5BDA47D04F1B4B&method=360buy.order.search×tamp=2013-05-30 00:00:00&v=2.0&sign=E981702AF260F37FCCD7D60FD19AAEA7');
    }
}
