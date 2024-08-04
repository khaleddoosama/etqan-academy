<?php

namespace App\Services;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AwsS3Service
{
    protected $s3Client;
    protected $bucket;

    public function __construct()
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $this->bucket = env('AWS_BUCKET');
    }

    public function getPreSignedUrl($file_name, $file_type, $expiry = '+20 minutes')
    {
        try {

            $cmd = $this->s3Client->getCommand('PutObject', [
                'Bucket' => $this->bucket,
                'Key' => $file_name,
                'ContentType' => $file_type,
            ]);

            $request = $this->s3Client->createPresignedRequest($cmd, $expiry);
            $url = (string) $request->getUri();

            return $url;
        } catch (\Exception $e) {
            Log::error('Error generating pre-signed URL: ' . $e->getMessage());
            throw $e;
        }
    }

    public function duplicateObject($sourcePath, $destinationPath)
    {
        try {
            $this->s3Client->copyObject([
                'Bucket'     => $this->bucket,
                'CopySource' => "{$this->bucket}/{$sourcePath}",
                'Key'        => $destinationPath,
            ]);
        } catch (\Exception $e) {
            Log::error('Error duplicating object on S3: ' . $e->getMessage());
        }
    }
}
