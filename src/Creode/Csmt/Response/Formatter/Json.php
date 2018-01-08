<?php

namespace Creode\Csmt\Response\Formatter;

use Creode\Csmt\Response\Response;

class Json implements Response
{
    public function send(array $responseData, $responseCode)
    {
        header('Content-type:application/json;charset=utf-8');

        http_response_code($responseCode);

        echo json_encode($responseData);
    }
}
