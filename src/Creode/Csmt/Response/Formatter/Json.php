<?php

namespace Creode\Csmt\Response\Formatter;

use Creode\Csmt\Response\Response;

class Json implements Response
{
    public function send(array $responseData)
    {
        echo json_encode($responseData) . PHP_EOL;
    }
}
