<?php

namespace Creode\Csmt\Command;

use Symfony\Component\Console\Command\Command;

class BaseCommand extends Command
{
    /**
     * @var \Creode\Csmt\Config\Config $_config
     */
    protected $_config;

    /**
     * @var \Creode\Csmt\Response\Responder $_responder
     */
    protected $_responder;
    
    /**
     * Constructor
     * @param \Creode\Csmt\Response\Responder $responder 
     */
    public function __construct(
        \Creode\Csmt\Config\Config $config,
        \Creode\Csmt\Response\Responder $responder
    ) {
        $this->_config = $config;
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
     * Sends an info response
     * @param mixed $responseData 
     */
    public function sendInfoResponse($responseData, $responseCode = 200)
    {
        $response = is_array($responseData) ? $responseData : ['message' => $responseData];

        $response['info'] = true;

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

    /**
     * Gets path to local storage dir
     * @return string
     */
    protected function getLocalStorageDir()
    {
        $project = $this->_config->get('project');

        if (!$project) {
            throw new \Exception('Project config node not found');
        } elseif (!isset($project['name'])) {
            throw new \Exception('Project name config node not found');
        }

        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $project['name'] . DIRECTORY_SEPARATOR;
    }

    /**
     * Checks whether system command is available
     * @param string $command 
     * @return boolean
     */
    protected function systemCommandExists($command)
    {
        exec('which ' . $command, $output);

        return count($output) > 0;
    }

    /**
     * Checks if this is the test environment
     * @return boolean
     */
    protected function isTestEnvironment()
    {
        $environment = $this->_config->get('environment');

        if (!$environment) {
            throw new \Exception('Environment config node not found');
        }

        return $environment == 'test';
    }

    /**
     * Checks if this is the live environment
     * @return boolean
     */
    protected function isLiveEnvironment()
    {
        $environment = $this->_config->get('environment');

        if (!$environment) {
            throw new \Exception('Environment config node not found');
        }

        return $environment == 'live';
    }
}
