<?php

namespace Creode\Csmt\Command\Snapshot\Database;

use Creode\Csmt\Command\Snapshot\SnapshotTaker;
use Symfony\Component\Yaml\Yaml;

class SnapshotCommand extends SnapshotTaker
{
    const STRUCTURE_FILENAME = '01_structure.sql';
    const DATA_FILENAME = '02_data.sql';
    const OBFUSCATED_DATA_FILENAME = '03_data_obfuscated.sql';
    const OBFUSCATION_MANIFEST = 'manifest.yml';

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
                foreach($databases as $name => $config) {
                    $this->takeStructureSnapshot($config);
                    $this->takeDataSnapshot($config);
                    $this->takeObfuscatedDataSnapshot($config);
                }
            }

            $this->sendSuccessResponse('Took DB snapshot and stored safely');
        } catch (\Exception $e) {
            $this->sendErrorResponse('Error taking DB dump - ' . $e->getMessage());
        }
    }


    private function takeStructureSnapshot($config) {
        $localFilename = self::STRUCTURE_FILENAME;
        $localFile = $this->getLocalStorageDir() . $localFilename;

        // TODO: This shouldn't always be mysql
        $cmd = 'mysqldump --no-data -h ' . $config['host'] . ' -u ' . $config['user'] . " -p'" . $config['pass'] . "' " . $config['name'] . ' > ' . $localFile;
        exec($cmd);

        $this->pushToStorage(
            $localFile,
            $config['remote_dir'],
            $localFilename,
            $config['storage']['general']
        );
    }


    private function takeDataSnapshot($config) {
        $additionalParams = [];

        if (isset($config['data'])) {
            if (isset($config['data']['exclude'])) {
                foreach($config['data']['exclude'] as $table) {
                    $additionalParams[] = '--ignore-table=' . $config['name'] . '.' . $table;
                }
            }

            if (isset($config['data']['obfuscate'])) {
                foreach($config['data']['obfuscate'] as $table) {
                    foreach($table as $name => $fields) {
                        $additionalParams[] = '--ignore-table=' . $config['name'] . '.' . $name;
                    }
                }
            }
        }

        $localFilename = self::DATA_FILENAME;
        $localFile = $this->getLocalStorageDir() . $localFilename;

        // TODO: This shouldn't always be mysql
        $cmd = 'mysqldump --no-create-info ' . implode(' ', $additionalParams) . ' -h ' . $config['host'] . ' -u ' . $config['user'] . " -p'" . $config['pass'] . "' " . $config['name'] . ' > ' . $localFile;
        exec($cmd);

        $this->pushToStorage(
            $localFile,
            $config['remote_dir'],
            $localFilename,
            $config['storage']['general']
        );
    }


    private function takeObfuscatedDataSnapshot($config) {
        $tables = [];

        if (isset($config['data'])) {
            if (isset($config['data']['obfuscate'])) {
                foreach($config['data']['obfuscate'] as $table) {
                    foreach($table as $name => $fields) {
                        $tables[] = $name;
                    }
                }
            }
        }

        $localFilename = self::OBFUSCATED_DATA_FILENAME;
        $localFile = $this->getLocalStorageDir() . $localFilename;

        // TODO: This shouldn't always be mysql
        $cmd = 'mysqldump -h ' . $config['host'] . ' -u ' . $config['user'] . " -p'" . $config['pass'] . "' " . $config['name'] . ' ' . implode(' ', $tables) . ' > ' . $localFile;
        exec($cmd);

        $project = $this->_config->get('project');
        $storagePrefix = $project['name'] . DIRECTORY_SEPARATOR;

        $this->pushToStorage(
            $localFile,
            $storagePrefix . $config['remote_dir'],
            $localFilename,
            $config['storage']['obfuscated']
        );

        $this->generateObfuscationManifest($config);
    }

    private function generateObfuscationManifest($config) {
        $localManifestFilename = self::OBFUSCATION_MANIFEST;
        $localManifestFile = $this->getLocalStorageDir() . $localManifestFilename;

        $generalStorageDetails = $this->getStorageDetails($config['storage']['general']);

        $manifestData = array(
            'destination' => [
                'type' => 'S3',
                'bucket' => $generalStorageDetails['bucket'],
                'region' => $generalStorageDetails['region'],
                'access' => $generalStorageDetails['access'],
                'secret' => $generalStorageDetails['secret'],
                'dir' => $config['remote_dir'],
                'filename' => self::OBFUSCATED_DATA_FILENAME
            ],
            'data' => $config['data']['obfuscate']
        );

        $manifestYaml = Yaml::dump($manifestData);
        file_put_contents($localManifestFile, $manifestYaml);

        $project = $this->_config->get('project');
        $storagePrefix = $project['name'] . DIRECTORY_SEPARATOR;

        $this->pushToStorage(
            $localManifestFile,
            $storagePrefix . $config['remote_dir'],
            $localManifestFilename,
            $config['storage']['obfuscated']
        );
    }   
}
