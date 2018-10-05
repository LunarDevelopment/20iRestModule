<?php
namespace TwentyI\API\HTTPException;
/**
 * An exception to express that payment is needed to perform the operation.
 */
class PaymentRequired extends \TwentyI\API\HTTPException {
    /** Builds the object
      *
      * @param string $full_url
      * @param mixed|null $decoded_body
      */
    public function __construct($full_url, $decoded_body = null) {
        return parent::__construct($full_url, $decoded_body, 402);
    }
}