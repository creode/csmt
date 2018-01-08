<?php

namespace Creode\Csmt\Command;

use Symfony\Component\Console\Command\Command;

class BaseCommand extends Command
{
    protected $_responder;
    
    /**
     * Constructor
     * @param \Creode\Csmt\Response\Responder $responder 
     */
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
    public function sendErrorResponse($responseData, $responseCode = 500)
    {
        $response = is_array($responseData) ? $responseData : ['message' => $responseData];

        $response['error'] = true;

        $this->sendResponse($response, $responseCode);
    }

    /**
     * Sends a success response
     * @param mixed $responseData 
     */
    public function sendSuccessResponse($responseData, $responseCode = 200)
    {
        $response = is_array($responseData) ? $responseData : ['message' => $responseData];

        $response['success'] = true;

        $this->sendResponse($response, $responseCode);
    }

    /**
     * Sends a response
     * @param array $response
     * @return type
     */
    protected function sendResponse(array $response, $responseCode)
    {
        $this->_responder->send($response, $responseCode);
        die();
    }
}
