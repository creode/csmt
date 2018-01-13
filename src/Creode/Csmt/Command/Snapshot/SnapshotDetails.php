<?php

namespace Creode\Csmt\Command\Snapshot;

use Creode\Csmt\System\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class SnapshotDetails extends Snapshot
{
    /**
     * @var array
     */
    private $_files = [];

    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $this->getSnapshotInfo();
    }

    /**
     * Adds a file to the response
     * @param File $file 
     * @return type
     */
    protected function addFileToResponse(File $file) 
    {
        $this->_files[] = $file;
    }

    /**
     * Sends the success response with the file details
     */
    protected function snapshotInfoSuccess() 
    {
        $this->sendSuccessResponse(['files' => $this->_files]);
    }

    /**
     * Adds snapshot details from live server storage
     * @param array $details 
     * @param string $name 
     * @return void
     */
    protected function getLiveSnapshotInfo(array $details, $name)
    {
        $info = $this->_storage->info($details['destination'], $details['storage']);

        $file = new \Creode\Csmt\System\File($info['Key']);

        $dateTime = \DateTime::createFromFormat ( \DateTime::ISO8601 , $info['LastModified'] );

        $file->date($dateTime)
            ->size($info['Size']);

        $this->addFileToResponse($file);
    }

    /**
     * Adds snapshot details from live server storage
     * @param array $details 
     * @param string $name 
     * @return void
     */
    protected function getTestSnapshotInfo(array $details, $name)
    {
        $path = $this->getLocalStorageDir() . $details['filename'];

        $file = new \Creode\Csmt\System\File($path);

        if (file_exists($path)) {
            $date = date(\DateTime::ISO8601, filemtime($path));
            $dateTime = \DateTime::createFromFormat ( \DateTime::ISO8601 , $date );
            // $dateTime = new \DateTime($date);
            // $date->format(DATE_ISO8601);
            // $date->setTimestamp(filemtime($path));

            $file->date($dateTime)
                ->size(filesize($path));           
        } else {
            $file->size(-1);
        }

        $this->addFileToResponse($file);

        // $info = $this->_storage->info($details['destination'], $details['storage']);

        // $file = new \Creode\Csmt\System\File($info['Key']);
        // $file->date($info['LastModified'])
        //     ->size($info['Size']);

        // $this->addFileToResponse($file);
    }

    /**
     * Retrieves info about a snapshot. Should call addFileToResponse()
     * with info about the file retrieved
     */
    abstract public function getSnapshotInfo();
}
