**Before Starting**

- Create a new app first on this page https://developer.vimeo.com/apps
- Grab your "Client Identifier" and "Client Secret"
- Use "Generate an Access Token" with full scope options (you will need this for some special requests i.e GET /me, and actually I used this for all requests cos it is some comlicated)
- Note: If you want to upload videos via API, you need to "Request Upload Permissions" after creating your app (and grap "Your new Access Token" at this time)
- See for scops: https://developer.vimeo.com/api/authentication#scopes

**How to Use**

```php
// Define your Client Identifier, Client Secret and Access Token
define('CLIENT_ID',     'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('CLIENT_SECRET', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('ACCESS_TOKEN',  null);
define('SCOPE', 'public private create');

// Init Vimeo object
// I used this this way using "Generate an Access Token" with full scope
$vimeo = new Vimeo(CLIENT_ID, CLIENT_SECRET, ACCESS_TOKEN, SCOPE);

// But does not need for all requests
$vimeo = new Vimeo(CLIENT_ID, CLIENT_SECRET);
$vimeo->setScope('public private');
// This will authorize your simple requests (i.e: GET /categories) and set Vimeo::accessToken
$vimeo->authorize();

// But you can store it after `authorize` if you dont wanna make new requests for accessToken
if (!isset($_SESSION['accessToken'])) {
    $vimeo->authorize();
    $_SESSION['accessToken'] = $vimeo->getAccessToken();
} else {
    $vimeo->setAccessToken($_SESSION['accessToken']);
}
```

** Simple
```php
$response = $vimeoRequest->get('categories');
pre($response, 1);
```