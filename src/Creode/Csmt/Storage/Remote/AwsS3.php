<?php

namespace Creode\Csmt\Storage\Remote;

use Creode\Csmt\Storage\Storage;

class AwsS3 implements Storage
{

    public function transfer($source, $dest, array $storageDetails)
    {
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

        $result = $client->putObject([
            'Bucket'     => $storageDetails['s3']['bucket'],
            'Key'        => $dest,
            'SourceFile' => $source,
        ]);
    }

    public function info($source, array $storageDetails)
    {
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

        $result = $client->listObjects([
            'Bucket'    => $storageDetails['s3']['bucket'],
            'MaxKeys'   => 1,
            'Prefix'    => $source,
        ]);

        return isset($result['Contents'][0]) ? $result['Contents'][0] : false;
    }
}
