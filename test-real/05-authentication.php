<?php
require_once "vendor/autoload.php";
require_once "test/APITest.php";
/** Tests simple GET actions */
class AuthenticationTest extends \APITest
{
    /** Tests that single sign-on works */
    public function testSingleSignOn()
    {
        $api_conf = $this->getAPIConf();
        if($api_conf->auth->url) {
            \TwentyI\API\Authentication::$serviceURL = $api_conf->auth->url;
            \TwentyI\API\Authentication::$verifyServerCertificate = false;
        }
        $rest = new \TwentyI\API\Authentication($api_conf->auth->key);

        $list = $rest->getWithFields("/user/stack-user");
        if(count($list) > 0) {
            $token_info = $rest->apiTokenForUser(
                $list[0]->type . ":" . $list[0]->id,
                $api_conf->services->key
            );
            $this->assertNotNull(
                $token_info->access_token,
                "Access token is in response"
            );
            $this->assertSame(
                $token_info->token_type,
                "bearer",
                "Correct token type"
            );
            $this->assertNotNull(
                $token_info->expires_in,
                "Expiry time is set"
            );
            $this->assertTrue(
                in_array("manageWeb", explode(" ", $token_info->scope)),
                "Required scopes set"
            );

            $token_info = $rest->controlPanelTokenForUser(
                $list[0]->type . ":" . $list[0]->id
            );
            $this->assertNotNull(
                $token_info->access_token,
                "Access token is in response"
            );
            $this->assertSame(
                $token_info->token_type,
                "bearer",
                "Correct token type"
            );
            $this->assertNotNull(
                $token_info->expires_in,
                "Expiry time is set"
            );
            $this->assertTrue(
                in_array("interactive", explode(" ", $token_info->scope)),
                "Required scopes set"
            );
        } else {
            $this->markTestSkipped("Please add some stack users to test fetch");
        }
    }
}
