**Before Starting**

- Create a new app first on this page https://developer.vimeo.com/apps
- Grab your "Client Identifier" and "Client Secret"
- Use "Generate an Access Token" with full scope options (you will need this for some special requests i.e `GET /me`, and actually I used this for all requests cos it is some complicated to work with Vimeo API, so grap "Your new Access Token" at same time)
- I used "Unauthenticated Requests" for all, see: https://developer.vimeo.com/api/authentication#unauthenticated-requests
- See for scops: https://developer.vimeo.com/api/authentication#scopes
- I am not going to show examples for all endpoints, see all endpoints here: https://developer.vimeo.com/api/endpoints
- Note: If you want to upload videos via API, you need to "Request Upload Permissions" after creating your app

**How to Use**

```php
// Define your Client Identifier, Client Secret and Access Token
define('CLIENT_ID',     'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('CLIENT_SECRET', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('ACCESS_TOKEN',  'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('SCOPE', 'public private create');

// Init Vimeo object
// I used this this way using "Generate an Access Token" with full scope
$vimeo = new Vimeo(CLIENT_ID, CLIENT_SECRET, ACCESS_TOKEN, SCOPE);

// But does not need for all requests
$vimeo = new Vimeo(CLIENT_ID, CLIENT_SECRET);
$vimeo->setScope('public private');
// This will authorize your simple requests
// (i.e: GET /categories) and set Vimeo::accessToken
$vimeo->authorize();

// But you can store it after `authorize`,
// if you don't wanna make new requests for accessToken
if (!isset($_SESSION['accessToken'])) {
    $vimeo->authorize();
    $_SESSION['accessToken'] = $vimeo->getAccessToken();
} else {
    $vimeo->setAccessToken($_SESSION['accessToken']);
}

// After all, init VimeoRequest object
// that makes our all requests to the API
$vimeoRequest = new VimeoRequest($vimeo);
```

Note: See `pre()` function in test.php.

** simple
```php
$response = $vimeoRequest->get('categories');
pre($response, 1);
```

** with endpoint/request params (array)
```php
$response = $vimeoRequest->get('categories/:category/channels', array(
    'end' => array('category' => 'music'),
    'req' => array('page' => 1, 'per_page' => 2)
));
pre($response, 1);
```

** with endpoint/request params (query string)
```php
$response = $vimeoRequest->get('categories/:category/channels', array(
    'end' => 'category=music',
    'req' => 'page=1&per_page=2'
));
pre($response, 1);
```

** with endpoint/request params
```php
$vimeoRequest->get('categories/:category/channels', array(
    'end' => array('category' => 'music'),
    'req' => array('page' => 1, 'per_page' => 2)
));
// Get response in this style
$response = $vimeoRequest->getResponseBody();
pre($response, 1);
```

** with callback
```php
$vimeoRequest->get('categories/:category/channels', array(
    'end' => array('category' => 'music'),
    'req' => array('page' => 1, 'per_page' => 2)
), function ($vimeoRequest) {
    $response = $vimeoRequest->getResponseBody();
    // pre($response);
    $response = json_decode($response, true);
    foreach ($response['data'] as $data) {
        printf("Channel URI: http://vimeo.com%s (%s)\n",
                $data['uri'], strtolower($data['name']));
    }
});
```

**Error Handling**

** try/catch
```php
try {
    // Non-exist endpoint
    $vimeoRequest->get('foo');
} catch (VimeoException $e) {
    print $e->getMessage() ."\n\n";
    print $vimeoRequest->getResponseHeader(0) ."\n";
    print $vimeoRequest->getResponseHeader('status_code') ."\n";
    print $vimeoRequest->getResponseHeader('status_text') ."\n";
    print_r($vimeoRequest->getResponseHeaders();
}
```

** try/catch (searching for response codes)
```php
try {
    // Non-exist endpoint
    $vimeoRequest->get('foo');
} catch (VimeoException $e) {
    $headers = $vimeoRequest->getResponseHeaders();
    switch ($headers['status_code']) {
        case 403:
        case 404:
            print 'Error: '. $headers['status_text'];
            break;
        case 200:
            print 'Enpoint found.';
            break;
    }
}
```
