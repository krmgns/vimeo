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
// ), function ($request /* is $vimeoRequest that called get() method */) {
//     $response = $request->getResponseBody();
//     // pre($response);
//     $response = json_decode($response, true);
//     foreach ($response['data'] as $data) {
//         printf("Channel URI: http://vimeo.com%s (%s)\n",
//                     $data['uri'], strtolower($data['name']));
//     }
// });

# Create a new channel
// // POST /channels
// $vimeoRequest->post('channels', array(
//     'name'        => 'Test',
//     'description' => 'Lorem ipsum dolor!',
//     'privacy'     => 'anybody'
// ), function ($request) {
//     if ($request->getResponseHeader('status_code') == 201) {
//         print 'Channel created.';
//     } else {
//         print $request->getResponseHeader(0);
//     }
// });

# Edit a channel's info
// // PATCH /channels
// $vimeoRequest->patch('channels', array(
//     'name'        => 'Test (edited)',
//     'description' => 'Lorem ipsum dolor! (edited)',
//     'privacy'     => 'users'
// ), function ($request) {
//     if ($request->getResponseHeader('status_code') == 204) {
//         print 'Channel updated.';
//     } else {
//         print $request->getResponseHeader(0);
//     }
// });

# Delete a channel
// // DELETE /channels/123
// $vimeoRequest->delete('channels/:channel_id', array(
//     'channel_id' => '123',
// ), function ($request) {
//     if ($request->getResponseHeader('status_code') == 204) {
//         print 'Channel deleted.';
//     } else {
//         print $request->getResponseHeader(0);
//     }
// });

# Add a video to a channel
// // PUT /channels/123/videos/456
// $vimeoRequest->put('channels/:channel_id/videos/:video_id', array(
//     'channel_id' => '123',
//     'video_id'   => '456',
// ), function ($request) {
//     if ($request->getResponseHeader('status_code') == 204) {
//         print 'Video added to a channel.';
//     } else {
//         print $request->getResponseHeader(0);
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
