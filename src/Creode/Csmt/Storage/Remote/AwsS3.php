<?php

namespace Creode\Csmt\Storage\Remote;

use Creode\Csmt\Storage\Storage;

class AwsS3 implements Storage
{
    public function push($source, $dest, array $storageDetails)
    {
        $client = $this->connect($storageDetails);

        $result = $client->putObject([
            'Bucket'     => $storageDetails['bucket'],
            'Key'        => $dest,
            'SourceFile' => $source,
        ]);
    }

    public function pull($source, $dest, array $storageDetails)
    {
        if (!file_exists(dirname($dest))) {
            if (!mkdir(dirname($dest), 0755, true)) {
                throw new \Exception("Could not create directory " . dirname($dest));
            }
        }
        
        $client = $this->connect($storageDetails);

        $result = $client->doesObjectExist(
            $storageDetails['bucket'],
            $source
        );

        if (!$result) {
            throw new \Exception("Cannot find $source in source bucket");
        }

        if (!is_writeable(dirname($dest)) || (file_exists($dest) && !is_writeable($dest))) {
            throw new \Exception('Cannot write file ' . $dest);
        }

        $result = $client->getObject([
            'Bucket'     => $storageDetails['bucket'],
            'Key'        => $source,
            'SaveAs'     => $dest,
        ]);
    }

    public function info($source, array $storageDetails)
    {
        $client = $this->connect($storageDetails);

        $result = $client->listObjects([
            'Bucket'    => $storageDetails['bucket'],
            'MaxKeys'   => 100,
            'Prefix'    => $source,
        ]);

        return isset($result['Contents']) ? $result['Contents'] : false;
    }

    public function downloadLink($source, $validFor, array $storageDetails) 
    {
        $client = $this->connect($storageDetails);

        $validFor = is_numeric($validFor) ? $validFor : 5;

        $cmd = $client->getCommand('GetObject', [
            'Bucket' => $storageDetails['bucket'],
            'Key'    => $source
        ]);

        $result = $client->createPresignedRequest($cmd, '+' . $validFor . ' minutes');

        return (string)$result->getUri();
    }

    /**
     * Connects to storage
     * @param array $storageDetails 
     * @return \Aws\S3\S3Client
     */
    private function connect(array $storageDetails) {
        if (
            !isset($storageDetails['access']) ||
            !isset($storageDetails['secret']) ||
            !isset($storageDetails['region']) ||
            !isset($storageDetails['bucket'])
        ) {
            throw new \Exception('S3 config requires access, secret, bucket and region nodes to be defined');
        }

        $client = new \Aws\S3\S3Client([
            'region'  => $storageDetails['region'],
            'version' => 'latest',
            'credentials' => [
                'key'    => $storageDetails['access'],
                'secret' => $storageDetails['secret'],
            ],
        ]);

        return $client;
    }
}
