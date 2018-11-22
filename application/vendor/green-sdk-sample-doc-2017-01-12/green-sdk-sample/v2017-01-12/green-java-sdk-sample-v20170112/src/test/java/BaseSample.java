import java.io.IOException;
import java.util.Properties;

/**
 * Created by liuhai.lh on 17/01/12.
 */
public class BaseSample {

    protected static String accessKeyId;
    protected static String accessKeySecret;

    protected static String regionId;

    static {
        Properties properties = new Properties();

        try {
            properties.load(BaseSample.class.getResourceAsStream("config.properties"));
            accessKeyId = properties.getProperty("accessKeyId");
            accessKeySecret = properties.getProperty("accessKeySecret");
            regionId = properties.getProperty("regionId");
        } catch(IOException e) {
            e.printStackTrace();
        }

    }


    protected static String getDomain(){
         if("cn-shanghai".equals(regionId)){
             return "green.cn-shanghai.aliyuncs.com";
         }

        return "green.cn-hangzhou.aliyuncs.com";
    }

    protected static String getEndPointName(){
        return regionId;
    }

}
