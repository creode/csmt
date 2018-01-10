<?php

namespace Creode\Csmt\Command\Csmt;

use Creode\Csmt\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HandshakeCommand extends BaseCommand
{   
    private $username;
    private $password;
    private $encryptedPassword;

    protected function configure()
    {
        $this->setName('handshake');
        $this->setDescription('Sets up security and provides login details to client');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $this->rejectIfSecured();
        $this->generateCredentials();
        $this->secureDirectory();
        $this->respond();
    }

    private function rejectIfSecured()
    {
        if (file_exists('.htpasswd')) {
            $this->sendErrorResponse(
                '.htpasswd already exists. Delete this if you want to perform a handshake',
                401 //Unauthorized
            );
        }
    }

    private function generateCredentials()
    {
        // Password to be encrypted for a .htpasswd file
        $password = bin2hex(random_bytes(50));

        $this->encryptedPassword = crypt($password, base64_encode($password));
        $this->password = $password;
        $this->username = base64_encode(uniqid());
    }

    private function secureDirectory()
    {
        file_put_contents('.htpasswd', $this->username . ':' . $this->encryptedPassword);

        $htaccessContents = '
AuthType Basic
AuthName "Password Protected Area"
AuthUserFile ' . getcwd() . '/.htpasswd
Require valid-user

<files csmt.yml>
 order allow,deny
 deny from all
</files>
';
    
        file_put_contents('.htaccess', $htaccessContents);

    }

    private function respond() 
    {
        $this->sendSuccessResponse([
            'message' => 'Tool was secured, credentials are attached',
            'user' => $this->username,
            'pass' => $this->password
        ]);
    }
}
