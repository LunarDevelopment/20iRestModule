<?php
namespace TwentyI\API;
/**
 * Wraps a Curl error. You'll have the literal Curl error number in the error
 * code.
 */
class CurlException extends \TwentyI\API\Exception {
    /**
     * Wraps a Curl error.
     *
     * @param mixed $ch The Curl handle
     */
    public function __construct($ch) {
        parent::__construct("Curl error: " . curl_error($ch), curl_errno($ch));
    }
}
