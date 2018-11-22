<?php defined('SYSPATH') or die('No direct script access.');

class Request extends Kohana_Request {

	public function __construct($uri)
	{
		// Remove trailing slashes from the URI
		$uri = trim($uri, '/');

		//Lang
		if (substr($uri, 0, 2) === 'en') {
		    I18n::lang('en');
		    $uri = substr($uri, 3);
    	}

        return parent::__construct($uri);
    }

}
