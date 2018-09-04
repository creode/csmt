<?php

namespace Creode\Csmt\Command\Snapshot\Filesystem;

use Creode\Csmt\Command\Snapshot\SnapshotRestore;

class SnapshotRestoreCommand extends SnapshotRestore
{
    protected function configure()
    {
        $this->setName('snapshot:filesystem:restore');
        $this->setDescription('Restores all filesystem snapshots');
    }

    public function restoreSnapshots()
    {
        $filesystem = $this->_config->get('filesystem');

        if (!$filesystem) {
            $this->sendErrorResponse('No files defined in config');
        } elseif (count($filesystem) == 0) {
            $this->sendErrorResponse('No file snapshots to restore');
        }

        if (!$this->systemCommandExists('unzip')) {
            $this->sendErrorResponse('unzip - command not found');
        }

        foreach($filesystem as $label => $details) {
            $zipFilename = strtolower($details['zip_dir']) . '.zip';
            $localFilePath = $this->getLocalStorageDir() . $details['remote_dir'] . DIRECTORY_SEPARATOR . $zipFilename;            

            if (!file_exists($localFilePath)) {
                throw new \Exception("Could not find file to restore, expected $localFilePath");
            }

            try {
                exec('cd ' . $details['parent_dir'] . ' && unzip -qq -o ' . $localFilePath, $output, $returnVar);

                if ($returnVar !== 0) {
                    throw new \Exception('Unzip failed');
                }
            } catch (\Exception $e) {
                $this->sendErrorResponse('There was a problem restoring ' . $zipFilename);
            }
        }

        $this->sendSuccessResponse('Restored filesystem snapshots');
    }
}
