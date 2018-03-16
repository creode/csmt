<?php

namespace Creode\Csmt\Command\Snapshot\Database;

use Creode\Csmt\Command\Snapshot\SnapshotTaker;

class SnapshotCommand extends SnapshotTaker
{
    protected function configure()
    {
        $this->setName('snapshot:database');
        $this->setDescription('Takes a DB snapshot');
    }

    public function takeSnapshot()
    {
        $databases = $this->_config->get('databases');

        if (!$this->systemCommandExists('mysqldump')) {
            $this->sendErrorResponse('mysqldump - command not found');
        }

        if (!file_exists($this->getLocalStorageDir())) {
            mkdir($this->getLocalStorageDir(), 0755, true);
        }

        try {
            if (count($databases)) {
                foreach($databases as $filename => $databaseDetails) {
                    // TODO: This shouldn't always be mysql
                    $outfile = $this->getLocalStorageDir() . $databaseDetails['filename'];
                    exec('mysqldump -h ' . $databaseDetails['host'] . ' -u ' . $databaseDetails['user'] . " -p'" . $databaseDetails['pass'] . "' " . $databaseDetails['name'] . ' > ' . $outfile);

                    if (filesize($outfile) == 0) {
                        throw new \Exception('Dump file size is zero');
                    }

                    $this->_storage->push($outfile, $databaseDetails['destination'], $databaseDetails['storage']);
                }
            }

            $this->sendSuccessResponse('Took DB snapshot and stored safely');
        } catch (\Exception $e) {
            $this->sendErrorResponse('Error taking DB dump - ' . $e->getMessage());
        }
    }
}
