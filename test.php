<?php
header('Content-Type: text/plain; charset=utf-8');

// Prepare output
ob_start(function($content) {
    $content = strip_tags($content);
    $content = str_replace('&gt;', '>', $content);
    return trim($content);
});

// Simple dump
function pre($input, $exit = false){
    print_r($input);
    if ($exit) {
        exit;
    }
}

/******************************************/

require(__DIR__.'/Vimeo/VimeoException.php');
require(__DIR__.'/Vimeo/VimeoRequest.php');
require(__DIR__.'/Vimeo/Vimeo.php');

# these could be found on https://developer.vimeo.com/apps
# if not, create a new app and also "Generate an Access Token" with full scope
// Client Identifier
define('CLIENT_ID', 'xxx');
// Client Secret
define('CLIENT_SECRET', 'xxx');
// Personal access tokens
define('ACCESS_TOKEN', 'xxx');

$vimeo = new Vimeo(CLIENT_ID, CLIENT_SECRET, ACCESS_TOKEN);
$vimeoRequest = new VimeoRequest($vimeo);

# simple
// $response = $vimeoRequest->get('categories');
// pre($response, 1);

# with endpoint/request params (array)
// $response = $vimeoRequest->get('categories/:category/channels', array(
//     'end' => array('category' => 'music'),
//     'req' => array('page' => 1, 'per_page' => 2)
// ));
// pre($response, 1);

# with endpoint/request params (query string)
// $response = $vimeoRequest->get('categories/:category/channels', array(
//     'end' => 'category=music',
//     'req' => 'page=1&per_page=2'
// ));
// pre($response, 1);

# with endpoint/request params
// $vimeoRequest->get('categories/:category/channels', array(
//     'end' => array('category' => 'music'),
//     'req' => array('page' => 1, 'per_page' => 2)
// ));
// Get response in this style
// $response = $vimeoRequest->getResponseBody();
// pre($response, 1);

# with callback
// $vimeoRequest->get('categories/:category/channels', array(
//     'end' => array('category' => 'music'),
//     'req' => array('page' => 1, 'per_page' => 2)
// ), function ($request) {
//     $response = $request->getResponseBody();
//     // pre($response);
//     $response = json_decode($response, true);
//     foreach ($response['data'] as $data) {
//         printf("Channel URI: http://vimeo.com%s (%s)\n",
//                     $data['uri'], strtolower($data['name']));
//     }
// });

# try/catch
// try {
//    // Non-existent endpoint
//    $vimeoRequest->get('foo');
// } catch (VimeoException $e) {
//     print $e->getMessage() ."\n\n";
//     print $vimeoRequest->getResponseHeader(0) ."\n";
//     print $vimeoRequest->getResponseHeader('status_code') ."\n";
//     print $vimeoRequest->getResponseHeader('status_text') ."\n";
//     print_r($vimeoRequest->getResponseHeaders();
// }

# try/catch (searching for response codes)
// try {
//    // Non-existent endpoint
//    $vimeoRequest->get('foo');
// } catch (VimeoException $e) {
//     $headers = $vimeoRequest->getResponseHeaders();
//     switch ($headers['status_code']) {
//         case 403:
//         case 404:
//             print 'Error: '. $headers['status_text'];
//             break;
//         case 200:
//             print 'Enpoint found.';
//             break;
//     }
// }
