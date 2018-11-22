<?php defined('SYSPATH') or die('No direct script access.');

class URL extends Kohana_URL {

    public static function site2($uri = '', $protocol = FALSE)
    {
        if (I18n::$lang !== 'zh') $uri = I18n::$lang. '/' . ltrim($uri, '/');

        if ($_SERVER["HTTP_HOST"] == Kohana::config('global')->site_cdn)
            return parent::site($uri, $protocol);
        else
            return Kohana::config('global')->site_url . parent::site($uri, $protocol);

    }

}