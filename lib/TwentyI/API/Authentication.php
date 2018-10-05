<?php
namespace TwentyI\API;

/**
 * Helper class for 20i API calls.
 *
 * @see https://my.20i.com/reseller/api
 *
 * @copyright 2018 20i Limited
 */
class Authentication extends REST
{
    /**
     * @var string The URL to the service. You should not need to change this.
     */
    public static $serviceURL = "https://auth-api.20i.com:3000/";

    /**
     * Returns a new access token for the user. This is suitable for ongoing
     * API use, not interactive (control panel) use.
     *
     * @var string|null $user The user reference, eg. "stack-user:1234"
     * @var string $from_token The original token to derive from (typically a
     * full API access token).
     * @throws
     * @return array {
     *     @var string $access_token
     *     @var string|null $refresh_token
     * }
     */
    public function apiTokenForUser(
        $user,
        $from_token
    ) {
        if(!$user) {
            throw new \TwentyI\API\Exception("User is required");
        } elseif(!is_string($user)) {
            throw new \TwentyI\API\Exception("User must be a string");
        }
        return $this->postWithFields("/login/authenticate", [
            "grant_type" => "refresh_token",
            "refresh_token" => $from_token,
            "scope" => $user,
        ]);
    }

    /**
     * Returns a new access token for the user. This is suitable for ongoing
     * API use, not interactive (control panel) use.
     *
     * @var string $user The user reference, eg. "stack-user:1234"
     * @var string[]|null The scopes to use. Usually unset.
     * @throws
     * @return array {
     *     @var string $access_token
     *     @var string|null $refresh_token
     * }
     */
    public function controlPanelTokenForUser(
        $user,
        array $scopes = null
    ) {
        if(!$user) {
            throw new \TwentyI\API\Exception("User is required");
        } elseif(!is_string($user)) {
            throw new \TwentyI\API\Exception("User must be a string");
        }
        return $this->postWithFields("/login/authenticate", [
            "grant_type" => "client_credentials",
            "scope" => isset($scopes) ?
                $user . "<" . implode(",", $scopes) . ">" :
                $user,
        ]);
    }
}
