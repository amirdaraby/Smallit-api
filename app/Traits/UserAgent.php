<?php

namespace App\Traits;

trait UserAgent
{

    protected function getOs($user_agent): ?string
    {

        $os_platform = null;
        $user_agent = strtolower($user_agent);
        $os_array = [
            '/windows nt 11/i' => 'Windows',
            '/windows nt 10/i' => 'Windows',
            '/windows nt 6.3/i' => 'Windows',
            '/windows nt 6.2/i' => 'Windows',
            '/windows nt 6.1/i' => 'Windows',
            '/windows nt 6.0/i' => 'Windows',
            '/windows nt 5.2/i' => 'Windows',
            '/windows nt 5.1/i' => 'Windows',
            '/windows xp/i' => 'Windows',
            '/windows nt 5.0/i' => 'Windows',
            '/windows me/i' => 'Windows',
            '/win98/i' => 'Windows',
            '/win95/i' => 'Windows',
            '/win16/i' => 'Windows',
            '/macintosh|mac os x/i' => 'Mac OS',
            '/mac_powerpc/i' => 'Mac OS',
            '/linux/i' => 'Linux',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/playbook/i' => 'BlackBerry',
            '/windows phone/i' => 'Windows Phone',
            '/webos/i' => 'Web OS'
        ];

        foreach ($os_array as $regex => $value)
            if (preg_match($regex, $user_agent))
                $os_platform = $value;


        return $os_platform;
    }


    protected
    function getBrowser($user_agent): ?string
    {

        $t = strtolower($user_agent);
        $t = " " . $t;

        $browsers = [
            "samsungbrowser" => "Samsung Browser",
            "miuibrowser" => "Miui Browser",
            "opera" => "Opera",
            "opr/" => "Opera",
            "fxios" => "Firefox",
            "crios" => "Chrome",
            "opios" => "Opera",
            "nokiabrowser" => "Nokia Browser",
            "chrome" => "Chrome",
            "safari" => "Safari",
            "firefox" => "Firefox",
            "msie" => "Internet Explorer",
            "trident/7" => "Internet Explorer"
        ];

        foreach ($browsers as $pattern => $value)
            if (strpos($t, $pattern))
                return $value;

        return null;
    }


}