<?php defined('SYSPATH') or die('No direct script access.');

    $this->baseurl = 'http://'. $_SERVER['HTTP_HOST'] .'/qwtxdb/';
    $this->cdnurl = 'http://cdn.jfb.smfyun.com/qwt/xdb/';
    $config = ORM::factory('qwt_xdbcfg')->getCfg($bid,1);

    // $userinfo = $wx->getUserInfo($openid);
    // $EventKey = $wx->getRevEvent()['key'];
    $Ticket = $wx->getRevTicket();
        //当前微信用户
    $model_q = ORM::factory('qwt_xdbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

    Kohana::$log->add("qwtxdb:EventKey", $EventKey);
    Kohana::$log->add("qwtxdb:scene_id", $scene_id);
    Kohana::$log->add("qwtxdb:wxget_scene_id", $wx->getRevSceneId());
    if($EventKey == $bid || $EventKey == 'xdb'.$bid){
        $EventKeyget = 1;
    }
    if($wx->getRevSceneId() == $bid || $wx->getRevSceneId() == 'xdb'.$bid){
        $wxgetRevSceneId = 1;
    }
    // if ($userinfo && ($Event == 'SCAN' && $EventKey == $scene_id) || ($wx->getRevSceneId() == $scene_id)) {
    if ($userinfo && ($Event == 'SCAN' && $EventKeyget == 1) || ($wxgetRevSceneId == 1)) {
        //新用户
        if (!$model_q->id) {
            // Kohana::$log->add("qwtxdb:$bid:model_q", 'model_q');
            $model_flag = 1;
            Kohana::$log->add("model_flag$bid$openid",$model_flag);
            $model_q->bid = $bid;
            $model_q->qid = $qr_user->id;
            $model_q->values($userinfo);
            //$model_q->ip = Request::$client_ip;
            if ($userinfo) $model_q->save();
        }else{
            $model_flag = 2;
            Kohana::$log->add("model_flag$bid$openid",$model_flag);
            $model_q->subscribe = $userinfo['subscribe'];
            $model_q->subscribe_time = $userinfo['subscribe_time'];
            $model_q->jointime = time();
            $model_q->save();
        }
        //根据 Ticket 获取二维码来源用户
        //小俞 -> 伯乐 oVOgUs0cGsvun_FM8-ywVpCd8AHk -> 珂心如意 oVOgUs9valnS-IN-NZzOmcxuAGuw -> 念念不忘 oVOgUs0vuImayOiVuD1Uf7SATZG8
        //珂心如意
        Kohana::$log->add("xdb_ticket:$bid:$openid",$Ticket);

        $fuser = ORM::factory('qwt_xdbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//上一级
        if ($fuser->openid) {
            $model_q->fopenid = $fuser->openid;
            $model_q->save();
        }

        Kohana::$log->add("xdb_fuser:$bid:$openid",$fuser->nickname);

        $msg['touser'] = $openid;
        $msg['msgtype'] = 'text';

        Kohana::$log->add("$bid:xdb:weixin_scan88:$openid", print_r($model_flag,true));
        // $url = $config['buy_url'];
        $content = str_replace("%s", $fuser->nickname, $config['buy_content']);
        // $msg['text']['content'] = "【".$fuser->nickname."】为".$biz->weixin_name."代言，并给你推荐了好物一枚~\n".."新用户送花瓶，点击购买》</a>";
        // $msg['text']['content'] = "【".$fuser->nickname."】为".$biz->weixin_name."代言，并给你推荐了好物一枚~\n".'<a href="'.$url.'">'.$content.'</a>';
        $msg['text']['content'] = $content;
        $wx->sendCustomMessage($msg);
    }
