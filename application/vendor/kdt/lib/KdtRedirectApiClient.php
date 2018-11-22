<?php
/**
 * @require PHP>=5.3
 */
require_once __DIR__ . '/KdtRedirectApiProtocol.php';

class KdtRedirectApiClient {
	private static $apiEntry = 'http://wap.koudaitong.com/v2/open/weixin/auth';
	
	private $appId;
	private $appSecret;
	
	public function __construct($appId, $appSecret) {
		if ('' == $appId || '' == $appSecret) throw new Exception('appId 和 appSecret 不能为空');
		
		$this->appId = $appId;
		$this->appSecret = $appSecret;
	}
	
	public function redirect($url, $scope = 'snsapi_base', $custom = null, $withSignKeys = 1) {
		$jump = self::$apiEntry . '?' . http_build_query($this->buildRequestParams($url, $scope, $custom, $withSignKeys));
		
		header('Location: ' . $jump);
		exit;
	}
	
	
	
	private function buildRequestParams($url, $scope, $custom, $withSignKeys) {
		$pairs = array();
		$pairs[KdtRedirectApiProtocol::APP_ID_KEY] = $this->appId;
		$pairs[KdtRedirectApiProtocol::REDIRECT_URL_KEY] = $url;
		$pairs[KdtRedirectApiProtocol::SCOPE_KEY] = $scope;
		$pairs[KdtRedirectApiProtocol::TIMESTAMP_KEY] = date('Y-m-d H:i:s');
		if ($custom !== null) $pairs[KdtRedirectApiProtocol::CUSTOM_KEY] = $custom;
		if ($withSignKeys !== null) $pairs[KdtRedirectApiProtocol::WITH_SIGN_KEYS_KEY] = $withSignKeys;
		
		$pairs[KdtRedirectApiProtocol::SIGN_KEY] = KdtRedirectApiProtocol::sign($this->appSecret, $pairs);
		return $pairs;
	}
}
