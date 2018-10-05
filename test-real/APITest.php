<?php
/** API testing functionality */
abstract class APITest extends PHPUnit_Framework_TestCase
{
    /** Fetches the API conf from the file test/api-conf.json (which you should
      * create). An example might look like:
      *
      *     {
      *         "auth": {
      *             "key": "ffffffffffffffff",
      *             "url": "https://other-auth-host:1111/"
      *         },
      *         "services": {
      *             "key": "ffffffffffffffff",
      *             "url": "https://other-services-host:1111/"
      *         }
      *     }
      *
      * Specifying the URLs can be omitted in the case of testing against the
      * live API.
      */
    protected function getAPIConf() {
        $filename = "test/api-conf.json";
        if(file_exists($filename)) {
            return json_decode(file_get_contents($filename));
        } else {
            die("Please create $filename. ".
                "It should include 'auth' and 'services' sections with a 'key' ".
                "for the relevant API key and optionally a 'url' for an ".
                "alternative service to test against."
            );
        }
    }
}
