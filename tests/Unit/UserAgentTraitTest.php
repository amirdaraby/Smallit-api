<?php

namespace Tests\Unit;

use App\Traits\UserAgent;
use Tests\TestCase;


class UserAgentTraitTest extends TestCase
{
    use UserAgent;

    public function testUserAgentTraitDetectsOsWindows(): void
    {
        $os = $this->getOs("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36");
        $this->assertSame("Windows", $os);
    }

    public function testUserAgentTraitDetectsOsMac(): void
    {
        $os = $this->getOs("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5.2 Safari/605.1.1");
        $this->assertSame("Mac OS", $os);
    }

    public function testUserAgentTraitDetectsOsLinux(): void
    {
        $os = $this->getOs("Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36");
        $this->assertSame("Linux", $os);
        $os = $this->getOs("Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.");
        $this->assertSame("Linux", $os);
    }


    public function testUserAgentTraitDetectsOsIphone(): void
    {
        $os = $this->getOs("Mozilla/5.0 (iPhone; CPU iPhone OS 16_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5.2 Mobile/15E148 Safari/604.");
        $this->assertSame("iPhone", $os);
    }

    public function testUserAgentTraitDetectsOsIpod(): void
    {
        $os = $this->getOs("Mozilla/5.0 (iPod; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/115.0.5790.130 Mobile/15E148 Safari/604.1");
        $this->assertSame("iPod", $os);
    }

    public function testUserAgentTraitDetectsOsIpad(): void
    {
        $os = $this->getOs("Mozilla/5.0 (iPad; CPU OS 13_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/115.0 Mobile/15E148 Safari/605.1.15");
        $this->assertSame("iPad", $os);
    }

    public function testUserAgentTraitDetectsOsAndroid(): void
    {
        $os = $this->getOs("Mozilla/5.0 (Linux; Android 10) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.5735.196 Mobile Safari/537.36");
        $this->assertSame("Android", $os);
    }

    public function testUserAgentTraitDetectsOsBlackberry(): void
    {
        $os = $this->getOs("Mozilla/5.0 (BlackBerry; U; BlackBerry 5080; en) AppleWebKit/51400.50 (KHTML, like Gecko) Version/523.5.5.1550 Mobile Safari/51400.50 [FBAN/EMA;FBLC/en_US;FBAV/323.1.1.2.43;]");
        $this->assertSame("BlackBerry", $os);
    }

    public function testUserAgentTraitDetectsOsWindowsPhone(): void
    {
        $os = $this->getOs("Mozilla/5.0 (Windows Phone 10; Android 12; ZB555KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114 Mobile Safari/537.36");
        $this->assertSame("Windows Phone", $os);
    }

    public function testUserAgentTraitDetectsOsWebOs(): void
    {
        $os = $this->getOs("Mozilla/5.0 (WebOS; Linux/SmartTV) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.5283.0 Safari/537.36");
        $this->assertSame("Web OS", $os);
    }

    public function testUserAgentTraitDetectsUnknownOsAndReturnsNull(): void
    {
        $os = $this->getOs("Some fake user agent just to dump you and get null result");
        $this->assertSame(null, $os);
    }

    public function testUserAgentTraitDetectsBrowserSamsung(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Linux; Android 11; BV6600Pro) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/22.0 Chrome/111.0.5563.116 Mobile Safari/537.36");
        $this->assertSame("Samsung Browser", $browser);
    }


    public function testUserAgentTraitDetectsBrowserMiui(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Linux; U; Android 12; fr-fr; Redmi Note 11E Build/SP1A.210812.016) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/89.0.4389.116 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.16.3.1-gn");
        $this->assertSame("Miui Browser", $browser);
    }

    public function testUserAgentTraitDetectsBrowserOpera(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36 OPR/99.0.0.");
        $this->assertSame("Opera", $browser);
    }

    public function testUserAgentTraitDetectsBrowserFirefox(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0");
        $this->assertSame("Firefox", $browser);
    }

    public function testUserAgentTraitDetectsBrowserChrome(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36");
        $this->assertSame("Chrome", $browser);
    }

    public function testUserAgentTraitDetectsBrowserNokia(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Symbian/3; Series60/5.3 NokiaX7-00/111.040.1511; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/535.1 (KHTML, like Gecko) NokiaBrowser/8.3.1.4 Mobile Safari/535.1 3gpp-gba");
        $this->assertSame("Nokia Browser", $browser);
    }

    public function testUserAgentTraitDetectsBrowserSafari(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5.2 Safari/605.1.15");
        $this->assertSame("Safari", $browser);
    }

    public function testUserAgentTraitDetectsBrowserInternetExplorer(): void
    {
        $browser = $this->getBrowser("	Mozilla/5.0 (Windows NT 10.0; Trident/7.0; rv:11.0) like Gecko");
        $this->assertSame("Internet Explorer", $browser);
    }

    public function testUserAgentTraitDetectsUnknownBrowserAndReturnsNull(): void
    {
        $os = $this->getBrowser("Some fake user agent just to dump you and get null result");
        $this->assertSame(null, $os);
    }
}
