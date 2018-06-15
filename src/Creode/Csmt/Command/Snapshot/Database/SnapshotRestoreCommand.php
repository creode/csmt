<?php

namespace Creode\Csmt\Command\Snapshot\Database;

use Creode\Csmt\Command\Snapshot\SnapshotRestore;

class SnapshotRestoreCommand extends SnapshotRestore
{
    protected function configure()
    {
        $this->setName('snapshot:database:restore');
        $this->setDescription('Restores all DB snapshots');
    }

    public function restoreSnapshots()
    {
        $databases = $this->_config->get('databases');

        if (!$databases) {
            $this->sendErrorResponse('No databases defined in config');
        } elseif (count($databases) == 0) {
            $this->sendErrorResponse('No DB snapshots to restore');
        }

        if (!$this->systemCommandExists('mysql')) {
            $this->sendErrorResponse('mysql - command not found');
        }

        try {
            foreach($databases as $filename => $databaseDetails) {
                $dir = $this->getLocalStorageDir() . $databaseDetails['remote_dir'] . DIRECTORY_SEPARATOR;
                $structureInfile = $dir . SnapshotCommand::STRUCTURE_FILENAME;
                $dataInfile = $dir . SnapshotCommand::DATA_FILENAME;
                $obfuscatedDataInfile = $dir . SnapshotCommand::OBFUSCATED_DATA_FILENAME;

                // TODO: This shouldn't always be mysql
                $cmdBase = 'mysql -h ' . $databaseDetails['host'] . ' -u ' . $databaseDetails['user'] . " -p'" . $databaseDetails['pass'] . "' " . $databaseDetails['name'] . ' < ';
                
                if (file_exists($structureInfile)) {
                    $cmd = $cmdBase . $structureInfile;
                    exec($cmd);
                }

                if (file_exists($dataInfile)) {
                    $cmd = $cmdBase . $dataInfile;
                    exec($cmd);
                }

                if (file_exists($obfuscatedDataInfile)) {
                    $cmd = $cmdBase . $obfuscatedDataInfile;
                    exec($cmd);
                }
            }
        } catch (\Exception $e) {
            $this->sendErrorResponse('There was a problem restoring the ' . $filename . ' database');
        }

        $this->sendSuccessResponse('Restored DB snapshots');
    }
}
