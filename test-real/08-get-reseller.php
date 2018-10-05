<?php
require_once "vendor/autoload.php";
require_once "test/APITest.php";
/** Tests simple GET actions */
class GetResellerTest extends \APITest
{
    /** Tests that the package fetch behaves reasonably */
    public function testResellerInfo()
    {
        $api_conf = $this->getAPIConf();
        if($api_conf->services->url) {
            \TwentyI\API\Services::$serviceURL = $api_conf->services->url;
        }
        $rest = new \TwentyI\API\Services($api_conf->services->key);

        $info = $rest->getWithFields("/reseller/*/packageTypes");
        print_r($info);
        $this->assertTrue(
            is_array($info),
            "Reseller returns web types"
        );
    }
}
