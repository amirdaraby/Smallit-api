<?php

namespace Tests\Unit;

use App\Traits\UserAgent;
use Tests\TestCase;


class UserAgentTraitDetectBrowserTest extends TestCase
{
    use UserAgent;

    public function test_user_agent_trait_detects_browser_samsung_browser(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Linux; Android 11; BV6600Pro) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/22.0 Chrome/111.0.5563.116 Mobile Safari/537.36");
        $this->assertSame("Samsung Browser", $browser);
    }


    public function test_user_agent_trait_detects_browser_miui_browser(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Linux; U; Android 12; fr-fr; Redmi Note 11E Build/SP1A.210812.016) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/89.0.4389.116 Mobile Safari/537.36 XiaoMi/MiuiBrowser/12.16.3.1-gn");
        $this->assertSame("Miui Browser", $browser);
    }

    public function test_user_agent_trait_detects_browser_opera(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36 OPR/99.0.0.");
        $this->assertSame("Opera", $browser);
    }

    public function test_user_agent_trait_detects_browser_firefox(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0");
        $this->assertSame("Firefox", $browser);
    }

    public function test_user_agent_trait_detects_browser_chrome(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36");
        $this->assertSame("Chrome", $browser);
    }

    public function test_user_agent_trait_detects_browser_nokia_browser(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Symbian/3; Series60/5.3 NokiaX7-00/111.040.1511; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/535.1 (KHTML, like Gecko) NokiaBrowser/8.3.1.4 Mobile Safari/535.1 3gpp-gba");
        $this->assertSame("Nokia Browser", $browser);
    }

    public function test_user_agent_trait_detects_browser_safari(): void
    {
        $browser = $this->getBrowser("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5.2 Safari/605.1.15");
        $this->assertSame("Safari", $browser);
    }

    public function test_user_agent_trait_detects_browser_internet_explorer(): void
    {
        $browser = $this->getBrowser("	Mozilla/5.0 (Windows NT 10.0; Trident/7.0; rv:11.0) like Gecko");
        $this->assertSame("Internet Explorer", $browser);
    }
}
