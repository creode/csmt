<?php

namespace Creode\Csmt\Storage\Remote;

use Creode\Csmt\Storage\Storage;

class AwsS3 implements Storage
{
    public function push($source, $dest, array $storageDetails)
    {
        $client = $this->connect($storageDetails);

        $result = $client->putObject([
            'Bucket'     => $storageDetails['s3']['bucket'],
            'Key'        => $dest,
            'SourceFile' => $source,
        ]);
    }

    public function pull($source, $dest, array $storageDetails)
    {
        if (!file_exists(dirname($dest))) {
            mkdir(dirname($dest), 0755, true);
        }
        
        $client = $this->connect($storageDetails);

        $result = $client->getObject([
            'Bucket'     => $storageDetails['s3']['bucket'],
            'Key'        => $source,
            'SaveAs'     => $dest,
        ]);
    }

    public function info($source, array $storageDetails)
    {
        $client = $this->connect($storageDetails);

        $result = $client->listObjects([
            'Bucket'    => $storageDetails['s3']['bucket'],
            'MaxKeys'   => 1,
            'Prefix'    => $source,
        ]);

        return isset($result['Contents'][0]) ? $result['Contents'][0] : false;
    }

    /**
     * Connects to storage
     * @param array $storageDetails 
     * @return \Aws\S3\S3Client
     */
    private function connect(array $storageDetails) {
        if (!isset($storageDetails['s3'])) {
            throw new \Exception('S3 Credentials are missing from config file');
        }

        $client = new \Aws\S3\S3Client([
            'region'  => $storageDetails['s3']['region'],
            'version' => 'latest',
            'credentials' => [
                'key'    => $storageDetails['s3']['access'],
                'secret' => $storageDetails['s3']['secret'],
            ],
        ]);

        return $client;
    }
}
