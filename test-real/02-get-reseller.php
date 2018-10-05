<?php
require_once "vendor/autoload.php";
require_once "test/APITest.php";
/** Tests simple GET actions */
class GetResellerTest extends \APITest
{
    /** Tests that the domain search behaves reasonably */
    public function testDemoLoginURL()
    {
        $api_conf = $this->getAPIConf();
        if($api_conf->services->url) {
            \TwentyI\API\Services::$serviceURL = $api_conf->services->url;
        }
        $rest = new \TwentyI\API\Services($api_conf->services->key);
        $url = $rest->getWithFields("/reseller/*/demoLoginURL");
        $this->assertTrue(
            is_string($url),
            "Demo login URL returns a string"
        );
        $this->assertRegexp(
            "#^https?://#",
            $url,
            "Demo login URL returns a URL"
        );
    }
}
