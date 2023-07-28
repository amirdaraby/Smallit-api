<?php

namespace Tests\Unit;

use App\Traits\UserAgent;
use Tests\TestCase;

class UserAgentTraitDetectOperatingSystemTest extends TestCase
{
    use UserAgent;

    public function test_user_agent_trait_detects_os_windows(): void
    {
        $os = $this->getOs("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36");
        $this->assertSame("Windows", $os);
    }

    public function test_user_agent_trait_detects_os_mac(): void
    {
        $os = $this->getOs("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5.2 Safari/605.1.1");
        $this->assertSame("Mac OS", $os);
    }

    public function test_user_agent_trait_detects_os_linux(): void
    {
        $os = $this->getOs("Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36");
        $this->assertSame("Linux", $os);
        $os = $this->getOs("Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.");
        $this->assertSame("Linux", $os);
    }


    public function test_user_agent_trait_detects_os_iphone(): void
    {
        $os = $this->getOs("Mozilla/5.0 (iPhone; CPU iPhone OS 16_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5.2 Mobile/15E148 Safari/604.");
        $this->assertSame("iPhone", $os);
    }

    public function test_user_agent_trait_detects_os_ipod(): void
    {
        $os = $this->getOs("Mozilla/5.0 (iPod; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/115.0.5790.130 Mobile/15E148 Safari/604.1");
        $this->assertSame("iPod", $os);
    }

    public function test_user_agent_trait_detects_os_ipad(): void
    {
        $os = $this->getOs("Mozilla/5.0 (iPad; CPU OS 13_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/115.0 Mobile/15E148 Safari/605.1.15");
        $this->assertSame("iPad", $os);
    }

    public function test_user_agent_trait_detects_os_android(): void
    {
        $os = $this->getOs("Mozilla/5.0 (Linux; Android 10) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.5735.196 Mobile Safari/537.36");
        $this->assertSame("Android", $os);
    }

    public function test_user_agent_trait_detects_os_blackberry(): void
    {
        $os = $this->getOs("Mozilla/5.0 (BlackBerry; U; BlackBerry 5080; en) AppleWebKit/51400.50 (KHTML, like Gecko) Version/523.5.5.1550 Mobile Safari/51400.50 [FBAN/EMA;FBLC/en_US;FBAV/323.1.1.2.43;]");
        $this->assertSame("BlackBerry", $os);
    }

    public function test_user_agent_trait_detects_os_windows_phone(): void
    {
        $os = $this->getOs("Mozilla/5.0 (Windows Phone 10; Android 12; ZB555KL) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114 Mobile Safari/537.36");
        $this->assertSame("Windows Phone", $os);
    }

    public function test_user_agent_trait_detects_os_web_os(): void
    {
        $os = $this->getOs("Mozilla/5.0 (WebOS; Linux/SmartTV) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.5283.0 Safari/537.36");
        $this->assertSame("Web OS", $os);
    }

    public function test_user_agent_trait_detects_os_unknown(): void
    {
        $os = $this->getOs("Some fake user agent just to dump you and get unknown result");
        $this->assertSame("Unknown", $os);
    }

}