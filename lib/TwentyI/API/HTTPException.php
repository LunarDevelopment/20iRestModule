<?php
namespace TwentyI\API;
/**
 * Wraps an HTTP error. You'll have the literal HTTP error number in the error
 * code.
 */
class HTTPException extends \TwentyI\API\Exception {
    /**
     * This dispatches to the appropriate HTTP exception object.
     *
     * @param string $full_url
     * @param mixed|null $decoded_body The body of the response, decoded from
     *     JSON where possible.
     * @param int $status
     */
    public static function create(
        $full_url,
        $decoded_body = null,
        $status = 400
    ) {
        switch($status) {
            case 402:
                return new \TwentyI\API\HTTPException\PaymentRequired(
                    $full_url,
                    $decoded_body
                );
            default:
                return new self(
                    $full_url,
                    $decoded_body,
                    $status
                );
        }
    }

    /**
     * @property mixed|null The error message payload in decoded form. This is
     *     particularly of interest for 400 errors.
     */
    public $decodedBody;

    /**
     * @property string The URL requested
     */
    public $fullURL;

    /**
     * Creates the object.
     *
     * @param string $full_url
     * @param mixed|null $decoded_body The body of the response, decoded from
     *     JSON where possible.
     * @param int $status
     */
    public function __construct(
        $full_url,
        $decoded_body = null,
        $status = 400
    ) {
        $this->decodedBody = $decoded_body;
        $this->fullURL = $full_url;
        $main_message = "HTTP error {$status} on {$full_url}";
        if($decoded_body) {
            $text_body = is_string($decoded_body) ?
                $decoded_body :
                json_encode($decoded_body);
            if(strlen($text_body) < 1024) {
                $message = "$main_message: $text_body";
            } else {
                $message = "$main_message: <" . strlen($text_body) . " byte response>";

            }
        } else {
            $message = $main_message;
        }
        parent::__construct($message, $status);
    }
}