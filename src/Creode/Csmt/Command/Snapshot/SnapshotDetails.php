<?php

namespace Creode\Csmt\Command\Snapshot;

use Creode\Csmt\System\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

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
        if (!in_array($file, $this->_files)) {
            $this->_files[] = $file;
        }
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
        $storage = $this->getStorageDetails($details['storage']['general']);

        $info = $this->_storage->info($details['remote_dir'], $storage);

        foreach($info as $remoteFile) {
            $file = new \Creode\Csmt\System\File($remoteFile['Key']);

            $dateTime = \DateTime::createFromFormat(\DateTime::ISO8601, $remoteFile['LastModified']);

            $file->date($dateTime)
                ->size($remoteFile['Size']);

            $this->addFileToResponse($file);
        }
    }

    /**
     * Adds snapshot details from live server storage
     * @param array $details 
     * @param string $name 
     * @return void
     */
    protected function getTestSnapshotInfo(array $details, $name)
    {
        $localStorageDirectory = $this->getLocalStorageDir() . $details['remote_dir'];

        if (!file_exists($localStorageDirectory)) {
            if (!mkdir($localStorageDirectory, 0755, true)) {
                throw new \Exception("Could not create tmp directory - $localStorageDirectory");
            }
        }

        $finder = new Finder();
        $finder->files()->in($localStorageDirectory)->sortByName();

        foreach ($finder as $snapshotFile) {
            $file = new \Creode\Csmt\System\File($snapshotFile->getPathname());

            if (file_exists($snapshotFile->getRealPath())) {
                $date = date(\DateTime::ISO8601, filemtime($snapshotFile->getRealPath()));
                $dateTime = \DateTime::createFromFormat ( \DateTime::ISO8601 , $date );

                $file->date($dateTime)
                    ->size(filesize($snapshotFile->getRealPath()));           
            } else {
                $file->size(-1);
            }

            $this->addFileToResponse($file);
        }
    }

    /**
     * Retrieves info about a snapshot. Should call addFileToResponse()
     * with info about the file retrieved
     */
    abstract public function getSnapshotInfo();
}
