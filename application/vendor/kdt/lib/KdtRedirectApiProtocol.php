<?php
class KdtRedirectApiProtocol {
	const APP_ID_KEY = 'app_id';
	const REDIRECT_URL_KEY = 'redirect_url';
	const SCOPE_KEY = 'scope';
	const TIMESTAMP_KEY = 'timestamp';
	const CUSTOM_KEY = 'custom';
	const WITH_SIGN_KEYS_KEY = 'with_sign_keys';
	const SIGN_KEY = 'sign';
	
	const SIGN_KEYS_KEY = 'sign_keys';
	
	const ALLOWED_DEVIATE_SECONDS = 600;
	
	
	
	public static function sign($appSecret, $params, $method = 'md5') {
		if (!is_array($params)) $params = array();
		
		ksort($params);
		$text = '';
		foreach ($params as $k => $v) {
			$text .= $k . $v;
		}
		
		return self::hash($method, $appSecret . $text . $appSecret);
	}
	
	private static function hash($method, $text) {
		switch ($method) {
			case 'md5':
			default:
				$signature = md5($text);
				break;
		}
		return $signature;
	}
}

