<?php
namespace TwentyI\API;

/**
 * Helper class for 20i API calls.
 *
 * @see https://my.20i.com/reseller/api
 *
 * @copyright 2018 20i Limited
 */
class Services extends REST
{
    /**
     * @var string The URL to the service. You should not need to change this.
     */
    public static $serviceURL = "https://api.20i.com/";

    /**
     * Returns an SSO URL for the given control panel token and domain name.
     *
     * @param string $token
     * @param string|null $domain_name If set, this is a hint about which
     *     domain this is for, which may affect the end URL.
     * @return string
     */
    public function singleSignOn($token, $domain_name = null) {
        $control_panel = new \TwentyI\API\ControlPanel($token);
        $customisations = $this->getWithFields("/reseller/*/customisations");
        return $control_panel->singleSignOn($customisations, $domain_name);
    }
}
