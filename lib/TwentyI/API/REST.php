<?php
namespace TwentyI\API;
/**
 * Helper class for 20i API calls.
 *
 * @see https://my.20i.com/reseller/api
 *
 * @copyright 2018 20i Limited
 */
class REST
{
    /**
     * @var bool If false, certificate verification will be bypassed. Only
     *  suitable for testing.
     */
    public static $verifyServerCertificate = true;

    /**
     * This will send a partly build Curl request to the URL given. The URL
     * will be canonicalised for you.
     *
     * @param string $url eg. "/foo"
     * @param array $options Curl options
     * @throws TwentyI\API\CurlException on Curl error
     * @throws TwentyI\API\HTTPException on HTTP 4xx/5xx
     * @throws TwentyI\API\HTTPException\PaymentRequired If payment is needed.
     * @return string The literal response body
     */
    private function sendRequest($url, array $options = [])
    {
        static $version = null;
        if(!$version) {
            $config = json_decode(file_get_contents(
                __DIR__ . "/../../../composer.json"
            ));
            $version = $config->version;
        }
        $original_headers = isset($options[CURLOPT_HTTPHEADER]) ?
            $options[CURLOPT_HTTPHEADER] :
            [];
        unset($options[CURLOPT_HTTPHEADER]);

        $full_url = preg_match('#^/+(.*)#', $url, $md) ?
            (static::$serviceURL . $md[1]) :
            (static::$serviceURL . $url);

        $full_user_agent = $this->userAgent ?
            "20iAPI/{$version} {$this->userAgent}" :
            "20iAPI/{$version}";
        $ch = curl_init($full_url);
        curl_setopt_array($ch, $options + [
            CURLOPT_SSL_VERIFYPEER => static::$verifyServerCertificate,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $original_headers + [
                "Expect:",
                // ^Otherwise Curl will add Expect: 100 Continue, which is wrong.
                "Authorization: Bearer " . base64_encode($this->bearerToken),
            ],
            CURLOPT_USERAGENT => $full_user_agent,
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new \TwentyI\API\CurlException($ch);
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (preg_match('/^404/', $status)) {
            trigger_error("404 on $full_url");
            $response = null;
        } elseif (preg_match('/^[45]/', $status)) {
            $decoded_body = json_decode($response);
            if(!isset($decoded_body)) {
                $decoded_body = $response;
            }
            throw \TwentyI\API\HTTPException::create($full_url, $decoded_body, $status);
        }

        curl_close($ch);
        return $response;
    }

    /**
     * @property string The access token to use, which will look like
     *     'c8e05bcafcd220013'
     */
    protected $bearerToken;

    /**
     * @property string|null $userAgent The secondary user-agent string
     */
    protected $userAgent;

    /**
     * Builds the object
     *
     * @param string $bearer_token eg. "7a528cf6921cc713"
     */
    public function __construct($bearer_token)
    {
        $this->bearerToken = $bearer_token;
    }

    /**
     * Deletes a resource.
     *
     * @param string $url
     * @param array $fields eg. ["foo"=>"bar"]
     * @param array $options @see curl_setopt_array()
     * @return mixed JSON-decoded response
     */
    public function deleteWithFields($url, $fields = [], $options = [])
    {
        if (count($fields) > 0) {
            $query = array_reduce(
                array_keys($fields),
                function ($carry, $item) use ($fields) {
                    return ($carry ? "$carry&" : "?") .
                        urlencode($item) . "=" . urlencode($fields[$item]);
                },
                ""
            );
        } else {
            $query = "";
        }

        $response = $this->sendRequest($url . $query, [
            CURLOPT_CUSTOMREQUEST => "DELETE",
        ] + $options);

        return json_decode($response);
    }

    /**
     * Fetches a resource without decoding.
     *
     * @param string $url
     * @param array $fields eg. ["foo"=>"bar"]
     * @param array $options @see curl_setopt_array()
     * @return string Raw response body
     */
    public function getRawWithFields($url, $fields = [], $options = [])
    {
        if (count($fields) > 0) {
            $query = array_reduce(
                array_keys($fields),
                function ($carry, $item) use ($fields) {
                    return ($carry ? "$carry&" : "?") .
                        urlencode($item) . "=" . urlencode($fields[$item]);
                },
                ""
            );
        } else {
            $query = "";
        }

        return $this->sendRequest($url . $query, $options);
    }

    /**
     * Fetches a resource.
     *
     * @param string $url
     * @param array $fields eg. ["foo"=>"bar"]
     * @param array $options @see curl_setopt_array()
     * @return mixed JSON-decoded response
     */
    public function getWithFields($url, $fields = [], $options = [])
    {
        $response = $this->getRawWithFields($url, $fields, $options);
        return json_decode($response);
    }

    /**
      * This will send an HTTP POST to the URL given, with the supplied
      * arguments, and return the response.
      *
      * @param string $url eg. "/foo"
      * @param array $fields As usable by http_build_query().
      * @param array $options Any custom Curl options you may need.
      * @throws TwentyI\API\Exception on error
      * @return mixed The decoded JSON from the response
      */
    public function postWithFields($url, array $fields, array $options = [])
    {
        $original_headers = isset($options[CURLOPT_HTTPHEADER]) ?
            $options[CURLOPT_HTTPHEADER] :
            [];
        unset($options[CURLOPT_HTTPHEADER]);
        $response = $this->sendRequest(
            $url,
            [
                CURLOPT_HTTPHEADER => $original_headers + [
                    "Content-Type: application/json",
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($fields),
            ] + $options
        );
        return json_decode($response);
    }

    /**
     * Creates or replaces a resource.
     *
     * @param string $url
     * @param array $fields eg. ["foo"=>"bar"]. This is the content of the
     *  resource.
     * @param array $options @see curl_setopt_array()
     * @return mixed JSON-decoded response
     */
    public function putWithFields($url, $fields, $options = [])
    {
        $original_headers = isset($options[CURLOPT_HTTPHEADER]) ?
            $options[CURLOPT_HTTPHEADER] :
            [];
        unset($options[CURLOPT_HTTPHEADER]);
        $response = $this->sendRequest($url, [
            CURLOPT_HTTPHEADER => $original_headers + [
                "Content-Length: " . strlen($fields),
            ],
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $fields,
        ] + $options);
        return json_decode($response);
    }

    /**
     * Sets the secondary User-Agent value to include in the header. This
     * module's identity will be included regardless.
     *
     * @param string|null $user_agent
     * @return self
     */
    public function setUserAgent($user_agent) {
        $this->userAgent = $user_agent;
        return $this;
    }
}
