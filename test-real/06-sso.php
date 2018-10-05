<?php
require_once "vendor/autoload.php";
require_once "test/APITest.php";
/** Tests single sign-on */
class SsoTest extends \APITest
{
    /** Tests that the package fetch behaves reasonably */
    public function testSso()
    {
        $api_conf = $this->getAPIConf();
        if($api_conf->services->url) {
            \TwentyI\API\Services::$serviceURL = $api_conf->services->url;
        }
        $services = new \TwentyI\API\Services($api_conf->services->key);

        if($api_conf->auth->url) {
            \TwentyI\API\Authentication::$serviceURL = $api_conf->auth->url;
            \TwentyI\API\Authentication::$verifyServerCertificate = false;
        }
        $auth = new \TwentyI\API\Authentication($api_conf->auth->key);

        $list = $services->getWithFields("/package");
        if(count($list) > 0) {
            $stack_user = $list[0]->stackUsers[0];
            $token_info = $auth->controlPanelTokenForUser($stack_user);
            $url = $services->singleSignOn(
                $token_info->access_token,
                $list[0]->name
            );
            $this->assertTrue(
                !!preg_match('#^https?:\/\/(?<host>[^/]+)#', $url, $md),
                "URL is set"
            );
            if(getenv("control_panel_domain")) {
                $this->assertSame(
                    $md["host"],
                    getenv("control_panel_domain"),
                    "control panel domain is set appropriately"
                );
            }
        } else {
            $this->markTestSkipped("Please add some packages to test fetch");
        }
    }
    /** Tests that the package fetch behaves reasonably by name */
    public function testSsoByName()
    {
        $api_conf = $this->getAPIConf();
        if($api_conf->services->url) {
            \TwentyI\API\Services::$serviceURL = $api_conf->services->url;
        }
        $services = new \TwentyI\API\Services($api_conf->services->key);

        if($api_conf->auth->url) {
            \TwentyI\API\Authentication::$serviceURL = $api_conf->auth->url;
            \TwentyI\API\Authentication::$verifyServerCertificate = false;
        }
        $auth = new \TwentyI\API\Authentication($api_conf->auth->key);

        $list = $services->getWithFields("/package");
        if(count($list) > 0) {
            $sus = $services->getWithFields("/package/{$list[0]->name}/stackUserList");
            $stack_user = $sus[0]->identity;
            $token_info = $auth->controlPanelTokenForUser($stack_user);
            $url = $services->singleSignOn($token_info->access_token);
            $this->assertTrue(!!preg_match('/^https?:/', $url), "URL is set for stack user via named package");
        } else {
            $this->markTestSkipped("Please add some packages to test fetch");
        }
    }
}
