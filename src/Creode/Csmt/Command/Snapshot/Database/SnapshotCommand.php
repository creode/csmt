<?php

namespace Creode\Csmt\Command\Snapshot\Database;

use Creode\Csmt\Command\Snapshot\SnapshotTaker;

class SnapshotCommand extends SnapshotTaker
{
    const STRUCTURE_FILE_PREFIX = '01_structure_';
    const DATA_FILE_PREFIX = '02_data_';

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
                    $this->takeStructureSnapshot($databaseDetails);
                    $this->takeDataSnapshot($databaseDetails);
                }
            }

            $this->sendSuccessResponse('Took DB snapshot and stored safely');
        } catch (\Exception $e) {
            $this->sendErrorResponse('Error taking DB dump - ' . $e->getMessage());
        }
    }


    private function takeStructureSnapshot($databaseDetails) {
        $outfilename = self::STRUCTURE_FILE_PREFIX . $databaseDetails['filename'];
        $outfile = $this->getLocalStorageDir() . $outfilename;

        // TODO: This shouldn't always be mysql
        $cmd = 'mysqldump --no-data -h ' . $databaseDetails['host'] . ' -u ' . $databaseDetails['user'] . " -p'" . $databaseDetails['pass'] . "' " . $databaseDetails['name'] . ' > ' . $outfile;
        exec($cmd);

        $this->pushToStorage($outfile, $outfilename, $databaseDetails);
    }

    private function takeDataSnapshot($databaseDetails) {
        $additionalParams = [];

        if (isset($databaseDetails['data'])) {
            if (isset($databaseDetails['data']['exclude'])) {
                foreach($databaseDetails['data']['exclude'] as $table) {
                    $additionalParams[] = '--ignore-table=' . $databaseDetails['name'] . '.' . $table;
                }
            }
        }

        $outfilename = self::DATA_FILE_PREFIX . $databaseDetails['filename'];
        $outfile = $this->getLocalStorageDir() . $outfilename;

        // TODO: This shouldn't always be mysql
        $cmd = 'mysqldump --no-create-info ' . implode(' ', $additionalParams) . ' -h ' . $databaseDetails['host'] . ' -u ' . $databaseDetails['user'] . " -p'" . $databaseDetails['pass'] . "' " . $databaseDetails['name'] . ' > ' . $outfile;
        exec($cmd);

        $this->pushToStorage($outfile, $outfilename, $databaseDetails);
    }

    private function pushToStorage($file, $destinationFileName, $databaseDetails) {
        if (filesize($file) == 0) {
            throw new \Exception('Dump file (' . $file . ') size is zero');
        }

        // support for old versions of csmt.yml where `destination` was a full file path
        $destination = isset($databaseDetails['remote_dir'])
                ? $databaseDetails['remote_dir'] . '/' . $destinationFileName
                : $databaseDetails['destination'];

        $this->_storage->push($file, $destination, $databaseDetails['storage']);
    }        
}
