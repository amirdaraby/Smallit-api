<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class AgentController extends Controller
{

    static public function getOs($user_agent)
    {

        $os_platform = "Unknown OS Platform";

        $os_array = [
            '/windows nt 11/i'      => 'Windows',
            '/windows nt 10/i'      => 'Windows',
            '/windows nt 6.3/i'     => 'Windows',
            '/windows nt 6.2/i'     => 'Windows',
            '/windows nt 6.1/i'     => 'Windows',
            '/windows nt 6.0/i'     => 'Windows',
            '/windows nt 5.2/i'     => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     => 'Windows',
            '/windows xp/i'         => 'Windows',
            '/windows nt 5.0/i'     => 'Windows',
            '/windows me/i'         => 'Windows',
            '/win98/i'              => 'Windows',
            '/win95/i'              => 'Windows',
            '/win16/i'              => 'Windows',
            '/windows phone/i'      => 'Windows Phone',
            '/macintosh|mac os x/i' => 'Mac OS',
            '/mac_powerpc/i'        => 'Mac OS',
            '/linux/i'              => 'Linux',
            '/ubuntu/i'             => 'Ubuntu',
            '/iphone/i'             => 'iPhone',
            '/ipod/i'               => 'iPod',
            '/ipad/i'               => 'iPad',
            '/android/i'            => 'Android',
            '/blackberry/i'         => 'BlackBerry',
            '/webos/i'              => 'Mobile'
        ];

        foreach ($os_array as $regex => $value)
            if (preg_match($regex, $user_agent))
                $os_platform = $value;

        return $os_platform;
    }


    static public function getBrowser($user_agent)
    {

        $t = strtolower($user_agent);
        $t = " " . $t;

//        dd(strpos($t,"safari"));

        if (strpos($t, 'chrome')) return 'Chrome';
        elseif (strpos($t, 'edge')) return 'Edge';
        elseif (strpos($t, 'opera') || strpos($t, 'opr/')) return 'Opera';
        elseif (strpos($t, 'safari')) return 'Safari';
        elseif (strpos($t, 'firefox')) return 'Firefox';
//        elseif (strpos($t,''))
//        elseif (strpos($t, 'tor')) return 'Tor';
        elseif (strpos($t, 'msie') || strpos($t, 'trident/7')) return 'Internet Explorer';
        return 'Unknown';
    }

}
