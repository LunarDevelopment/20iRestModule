<?php
require_once "vendor/autoload.php";
require_once "test/APITest.php";
/** Tests simple GET actions */
class GetPackageTest extends \APITest
{
    /** Tests that the package fetch behaves reasonably */
    public function testPackageInfo()
    {
        $api_conf = $this->getAPIConf();
        if($api_conf->services->url) {
            \TwentyI\API\Services::$serviceURL = $api_conf->services->url;
        }
        $rest = new \TwentyI\API\Services($api_conf->services->key);

        $list = $rest->getWithFields("/package");
        if(count($list) > 0) {
            $info = $rest->getWithFields("/package/{$list[0]->name}");
            $this->assertSame(
                $info->names[0],
                $list[0]->name,
                "Package by name returns an item with the same name"
            );
        } else {
            $this->markTestSkipped("Please add some packages to test fetch");
        }
    }
}
