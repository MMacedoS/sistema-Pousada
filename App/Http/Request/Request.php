<?php

namespace App\Http\Request;

class Request {
    protected $method;
    protected $uri;
    protected $queryParams;
    protected $bodyParams;
    protected $headers;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->queryParams = $_GET;
        $this->bodyParams = $_POST;
        $this->headers = getallheaders();
    }

    public function getMethod() {
        return $this->method;
    }

    public function getUri() {
        return $this->uri;
    }

    public function getQueryParams() {
        return $this->queryParams;
    }

    public function getBodyParams() {
        return $this->bodyParams;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function getParam($key) {
        return $this->queryParams[$key] ?? $this->bodyParams[$key] ?? null;
    }
}
