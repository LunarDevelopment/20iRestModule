<?php
require_once "vendor/autoload.php";
require_once "test/APITest.php";
/** Tests simple GET actions */
class TransferDomainTest extends \APITest
{
    /** Tests that the package fetch behaves reasonably */
    public function testResellerInfo()
    {
        if(getenv("test_domain")) {
            $api_conf = $this->getAPIConf();
            if($api_conf->services->url) {
                \TwentyI\API\Services::$serviceURL = $api_conf->services->url;
            }
            $rest = new \TwentyI\API\Services($api_conf->services->key);

            $response = $rest->postWithFields(
                "/reseller/*/transferDomain",
                [
                    "contact" => [
                        "organisation" => "Foo Ltd",
                        "name" => "Bar Foo",
                        "address" => "1 A B C, D E",
                        "telephone" => "+44.845000000",
                        "email" => "test@example.org",
                        "cc" => "GB",
                        "pc" => "ZZ99 9ZZ",
                        "sp" => null,
                        "city" => "Fooville",
                        "extension" => null,
                    ],
                    "name" => getenv("test_domain"),
                    "years" => 1,
                    "authcode" => "ac" . rand(),
                ]
            );
            $this->assertTrue(
                $response->result,
                "Transfer request is successful"
            );
        } else {
            $this->markTestSkipped(
                "Please provide a domain name to transfer. This should be done in development environments ONLY."
            );
        }
    }
}
