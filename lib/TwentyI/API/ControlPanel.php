<?php
namespace TwentyI\API;

/**
 * Helper class for 20i Stack Control Panel access.
 *
 * @see https://my.20i.com/reseller/api
 *
 * @copyright 2018 20i Limited
 */
class ControlPanel extends REST
{
    /**
     * @var string The URL to the service. You should not need to change this.
     */
    public static $serviceURL = "https://www.stackcp.com/";

    /**
     * Returns an SSO URL for the given brand info and current token.
     *
     * Should not be called directly, see singleSignOn for Services.
     *
     * @param object $customisations
     * @param string|null $domain_name If set, this is a hint about which
     *     domain this is for, which may affect the end URL.
     * @return string The sign-on URL
     */
    public function singleSignOn($customisations, $domain_name = null) {
        $response = $this->postWithFields("/login/implicitBranded", [
            "brand" => $customisations,
        ]);
        if(@$customisations->brandDomain) {
            $control_panel_domain = ($customisations->brandDomain == "*") ?
                $domain_name :
                $customisations->brandDomain;
            if($control_panel_domain) {
                $prefix = $customisations->brandSubdomain;
                $service_url =
                    "http://{$prefix}.{$control_panel_domain}/";
            } else {
                $service_url = self::$serviceURL;
            }
        } else {
            $service_url = self::$serviceURL;
        }
        return $service_url .
            "login/sso?" .
            http_build_query([
              "PHPSESSID" => $response->session_id
            ]);
    }
}
