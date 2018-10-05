<?php
require_once "vendor/autoload.php";
require_once "test/APITest.php";
/** Tests stack user stuff */
class StackUserTest extends \APITest
{
    /** Tests that stack user stuff behaves reasonably */
    public function testStackUser()
    {
        $api_conf = $this->getAPIConf();
        if($api_conf->services->url) {
            \TwentyI\API\Services::$serviceURL = $api_conf->services->url;
        }
        $services = new \TwentyI\API\Services($api_conf->services->key);

        $response = $services->postWithFields("/reseller/*/susers", [
            "newUser" => [
                "person_name" => rand(),
                "company_name" => rand(),
                "address" => rand(),
                "city" => rand(),
                "sp" => null,
                "pc" => "ZZ99 9ZZ",
                "cc" => "GB",
                "voice" => "0845 6447750",
                "notes" => "HELLO",
                "billing_ref" => rand(),
                "email" => rand()."@example.org",
                "nominet_contact_type" => null,
            ],
        ]);
        $user_ref = $response->result->ref;
        $this->assertNotNull($user_ref, "User ref exists");
        $response = $services->postWithFields("/reseller/*/susers", [
            "users" => [
                $user_ref => [
                    "delete" => true
                ],
            ],
        ]);
        $this->assertSame($response->result->updated, 1, "One user deleted");
    }

    /** Tests that stack user stuff behaves reasonably with odd phone formats */
    public function testStackUserOddPhoneFormats()
    {
        $api_conf = $this->getAPIConf();
        if($api_conf->services->url) {
            \TwentyI\API\Services::$serviceURL = $api_conf->services->url;
        }
        $services = new \TwentyI\API\Services($api_conf->services->key);

        $response = $services->postWithFields("/reseller/*/susers", [
            "newUser" => [
                "person_name" => rand(),
                "company_name" => rand(),
                "address" => rand(),
                "city" => rand(),
                "sp" => null,
                "pc" => "ZZ99 9ZZ",
                "cc" => "NL",
                "voice" => "0900-2354532",
                "notes" => "HELLO",
                "billing_ref" => rand(),
                "email" => rand()."@example.org",
                "nominet_contact_type" => null,
            ],
        ]);
        $user_ref = $response->result->ref;
        $this->assertNotNull($user_ref, "User ref exists");
        $response = $services->postWithFields("/reseller/*/susers", [
            "users" => [
                $user_ref => [
                    "delete" => true
                ],
            ],
        ]);
        $this->assertSame($response->result->updated, 1, "One user deleted");
    }
}
