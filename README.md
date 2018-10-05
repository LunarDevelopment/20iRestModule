20i REST Module
===============

Introduction
------------

This is the REST module for 20i services. It provides the necessary wrappers to
access 20i services without having to write your own REST client.

Requirements
------------

* Composer
* PHP5 or later. Please note that future versions may require PHP7.

Service Examples
----------------

In the below examples, the services API key will be given as "API KEY". You
should replace this with the actual value as on the API page
https://my.20i.com/reseller/api.

Add a new hosting service:

```
<?php
$general_api_key = "API KEY";
$services_api = new \TwentyI\API\Services($general_api_key);
$type = "5678";
$domain_name = "example.org";
$other_domain_names = ["example.net"];

$response = $services_api->postWithFields(
    "/reseller/*/addWeb",
    [
      "type" => $type,
      "domain_name" => $domain_name,
      "extra_domain_names" => $other_domain_names,
    ]
);
```

Set the nameservers on a domain:

```
<?php
$general_api_key = "API KEY";
$services_api = new \TwentyI\API\Services($general_api_key);

$domains = $services_api->getWithFields("/domain");
foreach ($domains as $domain) {
    if ($domain->name == "example.org") {
        $id = $domain->id;
        $old_nameservers = $services_api->getWithFields(
            "/domain/{$id}/nameservers"
        )->result;
        $services_api->postWithFields(
            "/domain/{$id}/nameservers",
            [
                "ns" => ["ns1.example.org", "ns2.example.org"],
                "old-ns" => $old_nameservers,
            ]
        );
    }
}
```

Find domains:

```
<?php
$general_api_key = "API KEY";
$services_api = new \TwentyI\API\Services($general_api_key);
$domains = $services_api->getWithFields("/domain-search/mybusinessname");
print_r($domains);
```

Authentication Examples
-----------------------

These only apply if you're trying to write your own version of the Stack control
panel.

In the below examples, the auth API client key will be given as "CLIENT KEY".
You should replace this with the actual value as on the API page
https://my.20i.com/reseller/api.

Authenticate a user with their username/password:

```
<?php
$oauth_client_key = "CLIENT KEY";
$username = "mycoolusername";
$password = "thatpasswordilike";

$auth_api = new \TwentyI\API\Authentication($oauth_client_key);
$response = $auth_api->postWithFields("/login/authenticate", [
    "grant_type" => "password",
    "username" => $username,
    "password" => $password,
]);
$new_access_token = $response->access_token;
```

Authenticate as a user you own:

```
<?php
$oauth_client_key = "CLIENT KEY";
$subuser_reference = "stack-user:97";

$auth_api = new \TwentyI\API\Authentication($oauth_client_key);
$response = $auth_api->postWithFields("/login/authenticate", [
    "grant_type" => "client_credentials",
    "scope" => $subuser_reference,
]);
$new_access_token = $response->access_token;
```

Other Examples
--------------

Single sign-on:

```
$oauth_client_key = "CLIENT KEY";
$general_api_key = "API KEY";

$services_api = new \TwentyI\API\Services($general_api_key);
$auth_api = new \TwentyI\API\Authentication($oauth_client_key);


$all_packages = $services_api->getWithFields("/package");
$package_id = $all_packages[0]->id; // This is just your first package

$stack_users = $services_api->getWithFields("/package/{$package_id}/stackUserList");
$token_info = $auth_api->controlPanelTokenForUser(
    $stack_users[0]->identity
);
$url = $services_api->singleSignOn($token_info->access_token, $package_info->name);
```

Notes
-----

Domain services are directly mapped from EPP format where possible. This means
that when a registry like Nominet uses hyphens (for example `opt-out`) in their
tag names, they end up being hyphens in the resultant object too. You can still
use them, you just need to write the code a bit differently. For example,
`$company_number = $contact->extension->{'co-no'};`
# 20iRestModule
