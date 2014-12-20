**Before Starting**

- Create a new app first on this page https://developer.vimeo.com/apps
- Grab your "Client Identifier" and "Client Secret"
- Use "Generate an Access Token" with full scope options
- Note: If you want to upload videos via API, you need to "Request Upload Permissions" after creating your app (and grap "Your new Access Token" at this time)

**How to Use**

```php
// Define your Client Identifier, Client Secret and Access Token
define('CLIENT_ID',     'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('CLIENT_SECRET', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('ACCESS_TOKEN',  'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
```