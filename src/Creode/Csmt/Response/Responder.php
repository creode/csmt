<?php

namespace Creode\Csmt\Response;

class Responder 
{
    private $_response;

    public function __construct(Response $response) 
    {
        $this->_response = $response;
    }

    public function send(array $responseData)
    {
        $this->_response->send($responseData);
    }
}
