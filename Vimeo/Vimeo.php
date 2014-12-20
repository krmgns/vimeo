<?php
/**
 * Copyright 2013, Kerem Gunes <http://qeremy.com/>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

/**
 * @class Vimeo object v0.1
 */

class Vimeo
{
    // Vimeo object version
    const VERSION  = '0.1';
    // API base URL
    const URL_BASE = 'https://api.vimeo.com',
    // API authorize URL
          URL_AUTH = '/oauth/authorize/client';

    // Vimeo Client ID and Client Secret
    private $clientId,
            $clientSecret;

    // Access token that grapped from developer.vimeo.com/apps
    private $accessToken;

    private $scope = array('public');

    // Request stuff
    protected $requestHeaders  = array(),
              $requestBody     = '';
    // Response stuff
    protected $responseHeaders = array(),
              $responseBody    = '';

    // Timeouts
    protected $timeout = 5,
              $timeoutConnect = 1;

    /**
     * Initialize a Vimeo object
     * @param string $clientId
     * @param string $clientSecret
     * @param string $accessToken
     * @param mixed  $scope
     */
    public function __construct($clientId, $clientSecret, $accessToken = null, $scope = '') {
        // Set client id & secret
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;

        // Set accessToken if provided
        if ($accessToken) {
            $this->setAccessToken($accessToken);
        }

        // Set scope if provided
        if ($scope) {
            $this->setScope($scope);
        }

        // Set default headers
        $this->requestHeaders[] = 'Expect:';
        $this->requestHeaders[] = 'Accept: */*';
        $this->requestHeaders[] = sprintf('User-Agent: Vimeo/v%s <https://github.com/qeremy/vimeo>', self::VERSION);
    }

    /**
     * Set access token that grapped from developer.vimeo.com/apps
     * @param string $accessToken
     */
    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
    }

    /**
     * Get access token
     * @return string
     */
    public function getAccessToken() {
        return $this->accessToken;
    }

    /**
     * Set scope (see supported scopes on developer.vimeo.com/api/authentication)
     * @param mixed $scope
     */
    public function setScope($scope) {
        if (!is_array($scope)) {
            $scope = preg_split('~\s+~', $scope, -1, PREG_SPLIT_NO_EMPTY);
        }
        $this->scope = $scope;
    }

    /**
     * Get scope
     * @return string
     */
    public function getScope() {
        return $this->scope;
    }

    /**
     * Set timeouts
     * @param int  $timeout
     * @param int  $timeoutConnect
     */
    public function setTimeouts($timeout = null, $timeoutConnect = null) {
        // Set timeout for cURL functions to execute
        if ($timeout !== null) {
            $this->timeout = $timeout;
        }
        // Set connect timeout
        if ($timeoutConnect !== null) {
            $this->timeoutConnect = $timeoutConnect;
        }
    }

    /**
     * Provides an authorization for simple requests (i.e: GET /categories)
     * @throws VimeoException
     */
    public function authorize() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::URL_BASE . self::URL_AUTH);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            'grant_type=client_credentials&scope='. join('+', $this->scope));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
            $this->requestHeaders, array('Authorization: Basic '.
                base64_encode($this->clientId .':'. $this->clientSecret))));
        // Timeouts
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,$this->timeoutConnect);

        $response = curl_exec($ch);
        curl_close($ch);

        // Convert response
        $response =@ json_decode($response, true);
        if (!isset($response['access_token'])) {
            throw new VimeoException('No `access_token` retrieved!');
        }

        // Set access token
        $this->setAccessToken($response['access_token']);
    }

    /**
     * Make a request
     * @param  string  $cmd     (i.e: GET /categories)
     * @param  array   $params  (GET/POST)
     * @throws VimeoException
     */
    public function request($cmd, Array $params = null) {
        // Needs access token for each request (both simple or not)
        if (!isset($this->accessToken)) {
            throw new VimeoException(
                'You need `access_token` to make a request. '.
                'Call %s->authorize() method before to get it!', __class__);
        }

        // Grap method & endpoint
        @list($method, $endpoint) = preg_split('~(\w+)\s+(.*)~',
            $cmd, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
        // Needs method & endpoint
        if (!isset($method, $endpoint)) {
            throw new VimeoException('Both `method` and `endpoint` are required!');
        }

        // Make endpoint safe
        $endpoint = '/'. trim($endpoint, '/ ');

        // Prepare cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        // Timeouts
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeoutConnect);

        // Prepare cURL url & set options
        switch ($method = strtoupper($method)) {
            case 'GET':
                // Prepare query string
                $query = empty($params)
                    ? '' : '?'. http_build_query($params);
                // Set request URL
                curl_setopt($ch, CURLOPT_URL, self::URL_BASE . $endpoint . $query);
                break;
            case 'PUT':
            case 'POST':
            case 'PATCH':
            case 'DELETE':
                // Prepare request body if provided
                if (!empty($params)) {
                    $this->requestBody = http_build_query($params);
                }
                // Set request URL
                curl_setopt($ch, CURLOPT_URL, self::URL_BASE . $endpoint);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, trim($this->requestBody));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }

        // Set authorization header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
            $this->requestHeaders, array('Authorization: Bearer '. $this->accessToken)));

        // Execute curl
        $response = curl_exec($ch);
        // Set request headers
        $this->requestHeaders = $this->parseHeaders(
            'request', curl_getinfo($ch, CURLINFO_HEADER_OUT));

        // Remove curl
        curl_close($ch);

        // Explode response
        @list($responseHeaders, $responseBody) = explode("\r\n\r\n", $response, 2);
        // Set response headers & body
        $this->responseHeaders = $this->parseHeaders('response', $responseHeaders);
        $this->responseBody    = trim($responseBody);

        // Check status code
        if (preg_match('~^[45]\d{2}~', (string) $this->responseHeaders['status_code'])) {
            throw new VimeoException("Response error!\n status_code:%s status_text:%s",
                $this->responseHeaders['status_code'], $this->responseHeaders['status_text']);
        }

        // Convert response
        $responseBody =@ json_decode($responseBody, true);
        if (isset($responseBody['error'])) {
            throw new VimeoException("Response error! %s\n status_code:%s status_text:%s", $responseBody['error'],
                $this->responseHeaders['status_code'], $this->responseHeaders['status_text']);
        }
    }

    /**
     * Parse request/response headers
     * @param  string $type
     * @param  string $headers
     * @return array
     */
    protected function parseHeaders($type, $headers) {
        $return  = array();
        $headers = array_map('trim', explode("\n", $headers));
        $header  = array_shift($headers);

        if ($type == 'request') {
            // Set 0 as theRequest
            $return[0] = $header;
        } elseif ($type == 'response' &&
            preg_match('~^HTTP/[\d\.]+ (\d+) ([\w- ]+)~i', $header, $matches)) {
            // Set 0 as theRequest & specials
            $return[0] = $header;
            $return['status_code'] = (int) $matches[1];
            $return['status_text'] = $matches[2];
        }

        // Fill headers
        foreach ($headers as $i => $header) {
            @list($key, $val) = explode(':', $header, 2);
            if ($key) {
                $return[$key] = trim($val);
            }
        }

        return $return;
    }

    /**
     * Get request headers (all)
     * @return array
     */
    public function getRequestHeaders() {
        return $this->requestHeaders;
    }

    /**
     * Get request header
     * @return mixed
     */
    public function getRequestHeader($key) {
        return isset($this->requestHeaders[$key]) ? $this->requestHeaders[$key] : null;
    }

    /**
     * Get request body
     * @return string
     */
    public function getRequestBody() {
        return $this->requestBody;
    }

    /**
     * Get response headers (all)
     * @return array
     */
    public function getResponseHeaders() {
        return $this->responseHeaders;
    }

    /**
     * Get response headers
     * @return array
     */
    public function getResponseHeader($key) {
        return isset($this->responseHeaders[$key]) ? $this->responseHeaders[$key] : null;
    }

    /**
     * Get response headers
     * @return string
     */
    public function getResponseBody() {
        return $this->responseBody;
    }
}
