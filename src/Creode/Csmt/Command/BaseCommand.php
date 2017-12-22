<?php

namespace Creode\Csmt\Command;

use Symfony\Component\Console\Command\Command;

class BaseCommand extends Command
{
    protected $_responder;
    
    public function __construct(
        \Creode\Csmt\Response\Responder $responder
    ) {
        $this->_responder = $responder;

        parent::__construct();     
    }

    /**
     * Sends an error response
     * @param mixed $responseData 
     */
    public function sendErrorResponse($responseData, $errorCode = 500)
    {
        $response = is_array($responseData) ? $responseData : ['message' => $responseData];

        $response['error'] = true;

        $this->sendResponse($response);
    }

    /**
     * Sends a success response
     * @param mixed $responseData 
     */
    public function sendSuccessResponse($responseData)
    {
        $response = is_array($responseData) ? $responseData : ['message' => $responseData];

        $response['success'] = true;

        $this->sendResponse($response);
    }

    /**
     * Sends a response
     * @param array $response
     * @return type
     */
    protected function sendResponse(array $response)
    {
        $this->_responder->send($response);

        exit;
    }
}
