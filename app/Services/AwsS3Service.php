<?php

namespace App\Services;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AwsS3Service
{
    protected $s3Client;
    protected $bucket;
    protected $maxObjectsLimit;

    public function __construct()
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.s3.region'),
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        $this->bucket = config('filesystems.disks.s3.bucket');
        $this->maxObjectsLimit = config('filesystems.disks.s3.max_objects_limit', 1000); // Configurable limit for total number of objects retrieved
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
            return (string) $request->getUri();
        } catch (\Exception $e) {
            Log::error('Error generating pre-signed URL: ' . $e->getMessage());
            return null;
        }
    }

    public function duplicateObject($sourcePath, $destinationPath)
    {
        try {
            $copySource = urlencode("{$this->bucket}/{$sourcePath}");
            $this->s3Client->copyObject([
                'Bucket'     => $this->bucket,
                'CopySource' => $copySource,
                'Key'        => $destinationPath,
            ]);
        } catch (\Exception $e) {
            Log::error('Error duplicating object on S3: ' . $e->getMessage());
        }
    }

    public function uploadFile($filePath, $key, $options = [])
    {
        try {
            if (!is_readable($filePath)) {
                throw new \Exception("The file {$filePath} does not exist or is not readable.");
            }

            $options = array_merge([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'SourceFile' => $filePath,
                'ACL' => 'private',
                'StorageClass' => 'STANDARD_IA',
            ], $options);
            $this->s3Client->putObject($options);
        } catch (\Exception $e) {
            Log::error('Error uploading file to S3: ' . $e->getMessage());
        }
    }

    public function deleteFile($key)
    {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting file from S3: ' . $e->getMessage());
        }
    }

    public function listFiles($prefix = '', $maxKeys = 100)
    {
        try {
            $files = [];
            $isTruncated = true;
            $continuationToken = null;
            $totalObjectsRetrieved = 0;

            while ($isTruncated && $totalObjectsRetrieved < $this->maxObjectsLimit) {
                $result = $this->s3Client->listObjectsV2([
                    'Bucket' => $this->bucket,
                    'Prefix' => $prefix,
                    'MaxKeys' => min($maxKeys, $this->maxObjectsLimit - $totalObjectsRetrieved),
                    'ContinuationToken' => $continuationToken,
                ]);

                if (isset($result['Contents'])) {
                    $files = array_merge($files, $result['Contents']);
                    $totalObjectsRetrieved += count($result['Contents']);
                }

                $isTruncated = $result['IsTruncated'] ?? false;
                $continuationToken = $result['NextContinuationToken'] ?? null;
            }

            return $files;
        } catch (\Exception $e) {
            Log::error('Error listing files from S3: ' . $e->getMessage());
            return [];
        }
    }

    public function downloadFile($key, $saveAs)
    {
        try {
            $saveDir = dirname($saveAs);
            if (!is_dir($saveDir) || !is_writable($saveDir)) {
                throw new \Exception("The directory {$saveDir} is not writable or does not exist.");
            }

            if (basename($saveAs) !== $saveAs) {
                throw new \Exception("Invalid file name {$saveAs}. Path traversal is not allowed.");
            }

            $this->s3Client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'SaveAs' => $saveAs,
            ]);
        } catch (\Exception $e) {
            Log::error('Error downloading file from S3: ' . $e->getMessage());
        }
    }

    public function getFileUrl($key, $expiry = '+1 hour')
    {
        try {
            $cmd = $this->s3Client->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
            $request = $this->s3Client->createPresignedRequest($cmd, $expiry);
            return (string) $request->getUri();
        } catch (\Exception $e) {
            Log::error('Error getting file URL from S3: ' . $e->getMessage());
            return null;
        }
    }

    public function fileExists($key)
    {
        try {
            $this->s3Client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
            return true;
        } catch (\Aws\S3\Exception\S3Exception $e) {
            if ($e->getStatusCode() == 404) {
                return false;
            }
            Log::error('Error checking if file exists on S3: ' . $e->getMessage());
            return false;
        }
    }
}
