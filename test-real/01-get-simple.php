<?php
require_once "vendor/autoload.php";
require_once "test/APITest.php";
/** Tests simple GET actions */
class GetSimpleTest extends \APITest
{
    /** Tests that the domain search behaves reasonably */
    public function testDomainSearch()
    {
        $api_conf = $this->getAPIConf();
        if($api_conf->services->url) {
            \TwentyI\API\Services::$serviceURL = $api_conf->services->url;
        }
        $rest = new \TwentyI\API\Services($api_conf->services->key);
        $domains = $rest->getWithFields("/domain-search/example.org");
        $this->assertNotNull(
            $domains[0]->header,
            "Domain search includes header chunk"
        );
    }
}
