import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONArray;
import com.alibaba.fastjson.JSONObject;
import com.aliyuncs.DefaultAcsClient;
import com.aliyuncs.IAcsClient;
import com.aliyuncs.exceptions.ClientException;
import com.aliyuncs.exceptions.ServerException;
import com.aliyuncs.green.model.v20170112.VideoAsyncScanResultsRequest;
import com.aliyuncs.http.FormatType;
import com.aliyuncs.http.HttpResponse;
import com.aliyuncs.profile.DefaultProfile;
import com.aliyuncs.profile.IClientProfile;


/**
 * Created by hyliu on 16/3/2.
 * 获取视频检测结果接口
 */
public class VideoAsyncScanResultsSample extends BaseSample {

    public static void main(String[] args) throws Exception {
        //请替换成你自己的accessKeyId、accessKeySecret
        IClientProfile profile = DefaultProfile.getProfile(regionId, accessKeyId, accessKeySecret);
        DefaultProfile.addEndpoint(getEndPointName(), regionId, "Green", getDomain()); // 添加自定义endpoint。

        IAcsClient client = new DefaultAcsClient(profile);

        VideoAsyncScanResultsRequest videoAsyncScanResultsRequest = new VideoAsyncScanResultsRequest();
        videoAsyncScanResultsRequest.setAcceptFormat(FormatType.JSON); // 指定api返回格式
        videoAsyncScanResultsRequest.setContentType(FormatType.JSON);



        JSONArray data = new JSONArray();
        data.add("ali3ozhT@HV4E$5e@MMpDXQU9-1mNXEp");

        videoAsyncScanResultsRequest.setContent(data.toJSONString().getBytes("UTF-8"), "UTF-8", FormatType.JSON);

        /**
         * 请务必设置超时时间
         */
        videoAsyncScanResultsRequest.setConnectTimeout(3000);
        videoAsyncScanResultsRequest.setReadTimeout(6000);
        try {
            HttpResponse httpResponse = client.doAction(videoAsyncScanResultsRequest);

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
