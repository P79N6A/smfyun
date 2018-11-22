import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.aliyuncs.DefaultAcsClient;
import com.aliyuncs.IAcsClient;
import com.aliyuncs.exceptions.ClientException;
import com.aliyuncs.exceptions.ServerException;
import com.aliyuncs.green.model.v20170112.VideoAsyncScanRequest;
import com.aliyuncs.http.FormatType;
import com.aliyuncs.http.HttpResponse;
import com.aliyuncs.profile.DefaultProfile;
import com.aliyuncs.profile.IClientProfile;

import java.util.*;

/**
 * Created by hyliu on 16/3/2.
 * 视频异步检测接口
 */
public class VideoAsyncScanRequestSample extends BaseSample {

    public static void main(String[] args) throws Exception {
        //请替换成你自己的accessKeyId、accessKeySecret
        IClientProfile profile = DefaultProfile.getProfile(regionId, accessKeyId, accessKeySecret);
        DefaultProfile.addEndpoint(getEndPointName(), regionId, "Green", getDomain());
        IAcsClient client = new DefaultAcsClient(profile);

        VideoAsyncScanRequest videoAsyncScanRequest = new VideoAsyncScanRequest();
        videoAsyncScanRequest.setAcceptFormat(FormatType.JSON); // 指定api返回格式
        videoAsyncScanRequest.setContentType(FormatType.JSON);
        videoAsyncScanRequest.setMethod(com.aliyuncs.http.MethodType.POST); // 指定请求方法

        List<Map<String, Object>> tasks = new ArrayList<Map<String, Object>>();
        Map<String, Object> task = new LinkedHashMap<String, Object>();
        task.put("dataId", UUID.randomUUID().toString());
        task.put("interval", 5);
        task.put("length", 3600);
        task.put("url", "http://cloud.video.taobao.com/play/u/228898015/p/1/e/1/t/1/***.swf");


        List<Map<String, Object>> frames = new ArrayList<Map<String, Object>>();
        Map<String, Object> frame1 = new LinkedHashMap<String, Object>();
        frame1.put("offset", 0);
        frame1.put("url", "http://pic12.nipic.com/20110221/6727421_210944911000_2.jpg");

        Map<String, Object> frame2 = new LinkedHashMap<String, Object>();
        frame2.put("offset", 5);
        frame2.put("url", "http://rifleman-share.oss-cn-hangzhou.aliyuncs.com/test/%E6%AD%A3%E5%B8%B8/***.jpg");

        Map<String, Object> frame3 = new LinkedHashMap<String, Object>();
        frame3.put("offset", 10);
        frame3.put("url", "http://rifleman-share.oss-cn-hangzhou.aliyuncs.com/test/%E6%AD%A3%E5%B8%B8/68d5883924c9e8cc88806a73bd7a8995.jpg");
        frames.addAll(Arrays.asList(frame1, frame2, frame3));

        task.put("frames", frames);
        tasks.add(task);

        JSONObject data = new JSONObject();
        data.put("scenes", Arrays.asList("porn"));
        data.put("tasks", tasks);

        videoAsyncScanRequest.setContent(data.toJSONString().getBytes("UTF-8"), "UTF-8", FormatType.JSON);

        /**
         * 请务必设置超时时间
         */
        videoAsyncScanRequest.setConnectTimeout(3000);
        videoAsyncScanRequest.setReadTimeout(6000);
        try {
            HttpResponse httpResponse = client.doAction(videoAsyncScanRequest);

            if(httpResponse.isSuccess()){
                JSONObject jo = JSON.parseObject(new String(httpResponse.getContent(), "UTF-8"));
                System.out.println(JSON.toJSONString(jo, true));
            }else{
                System.out.println("response not success. status:" + httpResponse.getStatus());
            }
        } catch (ServerException e) {
            e.printStackTrace();
        } catch (ClientException e) {
            e.printStackTrace();
        }
    }
}
