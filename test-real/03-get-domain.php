<?php
require_once "vendor/autoload.php";
require_once "test/APITest.php";
/** Tests simple GET actions */
class GetDomainTest extends \APITest
{
    /** Tests that the you can get the contract */
    public function testContract()
    {
        $api_conf = $this->getAPIConf();
        if($api_conf->services->url) {
            \TwentyI\API\Services::$serviceURL = $api_conf->services->url;
        }
        $rest = new \TwentyI\API\Services($api_conf->services->key);

        $list = $rest->getWithFields("/domain");
        if(count($list) > 0) {
            $item = end($list);
            $info = $rest->getWithFields("/domain/{$item->name}/contract");
            $this->assertTrue(
                !!preg_match(
                    '/^\d{4}-\d{2}-\d{2}$/',
                    $info->renewalDate
                ),
                "Renewal date returned"
            );
        } else {
            $this->markTestSkipped("Please add some domains to test fetch");
        }
    }
    /** Tests that the domain fetch behaves reasonably */
    public function testDomainInfo()
    {
        $api_conf = $this->getAPIConf();
        if($api_conf->services->url) {
            \TwentyI\API\Services::$serviceURL = $api_conf->services->url;
        }
        $rest = new \TwentyI\API\Services($api_conf->services->key);

        $list = $rest->getWithFields("/domain");
        if(count($list) > 0) {
            $info = $rest->getWithFields("/domain/{$list[0]->name}");
            $this->assertSame(
                $info->name,
                $list[0]->name,
                "Domain by name returns an item with the same name"
            );
        } else {
            $this->markTestSkipped("Please add some domains to test fetch");
        }
    }
    /** Tests that the you can get the upstream expiry date */
    public function testUpstreamExpiry()
    {
        $api_conf = $this->getAPIConf();
        if($api_conf->services->url) {
            \TwentyI\API\Services::$serviceURL = $api_conf->services->url;
        }
        $rest = new \TwentyI\API\Services($api_conf->services->key);

        $list = $rest->getWithFields("/domain");
        if(count($list) > 0) {
            $item = end($list);
            $info = $rest->getWithFields("/domain/{$item->name}/upstreamExpiryDate");
            $this->assertTrue(
                !!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}./', $info),
                "Expiry date returned"
            );
        } else {
            $this->markTestSkipped("Please add some domains to test fetch");
        }
    }
}
