<?php
namespace Axelero\AwsS3Bundle\Services;

use Aws\S3\S3Client;

class S3Depot
{
    /**
     * @var S3Client
     */
    private $client;

    /**
     * @var string
     */
    private $baseFolder;

    /**
     * @var string
     */
    private $bucket;

    function __construct($baseFolder, $bucket, S3Client $client)
    {
        $this->client = $client;
        $this->bucket = $bucket;
        $this->baseFolder = $baseFolder;
    }


    /**
     * Uploads a file in the same relative hierarchy
     * @param string $path the path of the file relative to the kernel root dir
     * @return \Guzzle\Service\Resource\Model
     */
    function put($path)
    {
        $info = $this->getMime($path);

        return $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $path,
            'SourceFile' => $this->getAbsolutePath($path),
            'ContentType' => $info,
            'StorageClass' => 'REDUCED_REDUNDANCY'
        ]);
    }

    /**
     * @param $path
     * @return mixed
     */
    private function getMime($path)
    {
        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($info, $this->getAbsolutePath($path));
        return $mime;
    }

    /**
     * @param $path
     * @return string
     */
    private function getAbsolutePath($path)
    {
        return preg_replace('#\/{2,}#', '/', $this->baseFolder . '/' . $path);
    }


}