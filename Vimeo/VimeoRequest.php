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
 * @class VimeoRequest object v0.1
 */

class VimeoRequest
{
    // Vimeo object
    private $vimeo;

    /**
     * Initialize a VimeoRequest object
     * @param Vimeo $vimeo
     */
    public function __construct(Vimeo $vimeo) {
        $this->vimeo = $vimeo;
    }

    /**
     * Call absent methods (actually used for to call Vimeo methods)
     * @param  string $name
     * @param  array  $args
     * @return mixed
     * @throws VimeoException
     */
    public function __call($name, $args) {
        // Check self method not exists and Vimeo method's exists
        // If true, return Vimeo method result
        if (!method_exists($this, $name) && method_exists($this->vimeo, $name)) {
            return call_user_func_array(array($this->vimeo, $name), $args);
        }
        throw new VimeoException('Method not exists! method:%s', $name);
    }

    /**
     * Prepare request with endpoint/request params & call Vimeo::request method
     * @param  string  $cmd
     * @param  array   $params
     */
    public function request($cmd, Array $params = null) {
        // Set defaults
        $params = (array) $params + array(
            'end' => null,
            'req' => null
        );

        // Convert to array endpoint params
        if (is_string($params['end'])) {
            parse_str($params['end'], $params['end']);
        }

        // Convert to array GET
        if (is_string($params['req'])) {
            parse_str($params['req'], $params['req']);
        }

        // Prepare request end
        if (!empty($params['end']) && strpos($cmd, ':') !== false) {
            foreach ((array) $params['end'] as $key => $val) {
                // Replace endpoint params (i.e: /categories/:category, ['category'=>'music'])
                $cmd = str_replace(':'. $key, $val, $cmd);
            }
        }

        // Make request
        $this->vimeo->request($cmd, $params['req']);
    }

    /**
     * Make a GET request
     * @param  string   $uri
     * @param  array    $params
     * @param  Closure  $callback
     * @return mixed
     */
    public function get($uri, Array $params = null, Closure $callback = null) {
        // Make request
        $this->request('GET '. $uri, $params);
        // Call callback if callable
        if (is_callable($callback)) {
            return call_user_func($callback, $this);
        }

        // Return response if callback is null
        return $this->getResponseBody();
    }

    /**
     * Make a POST request
     * @param  string   $uri
     * @param  array    $params
     * @param  Closure  $callback
     * @return mixed
     */
    public function post($uri, Array $params = null, Closure $callback = null) {
        // Make request
        $this->request('POST '. $uri, $params);
        // Call callback if callable
        if (is_callable($callback)) {
            return call_user_func($callback, $this);
        }

        // Return response if callback is null
        return $this->getResponseBody();
    }

    /**
     * Make a PUT request
     * @param  string   $uri
     * @param  array    $params
     * @param  Closure  $callback
     * @return mixed
     */
    public function put($uri, Array $params = null, Closure $callback = null) {
        // Make request
        $this->request('PUT '. $uri, $params);
        // Call callback if callable
        if (is_callable($callback)) {
            return call_user_func($callback, $this);
        }

        // Return response if callback is null
        return $this->getResponseBody();
    }

    /**
     * Make a PATCH request
     * @param  string   $uri
     * @param  array    $params
     * @param  Closure  $callback
     * @return mixed
     */
    public function patch($uri, Array $params = null, Closure $callback = null) {
        // Make request
        $this->request('PATCH '. $uri, $params);
        // Call callback if callable
        if (is_callable($callback)) {
            return call_user_func($callback, $this);
        }

        // Return response if callback is null
        return $this->getResponseBody();
    }

    /**
     * Make a DELETE request
     * @param  string   $uri
     * @param  array    $params
     * @param  Closure  $callback
     * @return mixed
     */
    public function delete($uri, Array $params = null, Closure $callback = null) {
        // Make request
        $this->request('DELETE '. $uri, $params);
        // Call callback if callable
        if (is_callable($callback)) {
            return call_user_func($callback, $this);
        }

        // Return response if callback is null
        return $this->getResponseBody();
    }
}
