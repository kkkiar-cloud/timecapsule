<?php
// File storage for capsule attachments.
//
// This is the only file that reads or writes uploaded files. They are kept in an Amazon S3
// bucket under the "uploads/" prefix. The rest of the app only uses the three functions
// storage_save(), storage_delete() and storage_stream().
//
// The server has no access keys: the AWS SDK gets temporary credentials from the IAM role
// attached to the EC2 instance (timecapsule-dev-app-role).

require_once ROOT_DIR . '/vendor/autoload.php';

use Aws\S3\S3Client;

// One S3 client for the whole request.
function s3_client(): S3Client
{
    static $client = null;
    if ($client === null) {
        $client = new S3Client([
            'version' => 'latest',
            'region'  => config('AWS_REGION', 'eu-central-1'),
        ]);
    }
    return $client;
}

function s3_bucket(): string
{
    return config('S3_BUCKET');
}

// S3 key of a stored file. basename() makes sure the key always stays under "uploads/".
function s3_key(string $storedName): string
{
    return 'uploads/' . basename($storedName);
}

// Save an uploaded file ($_FILES['...'] entry) and return its stored name, e.g. "3f9a...e1.png".
// The name is random, so users cannot guess other people's file names.
function storage_save(array $uploadedFile, string $extension): string
{
    $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
    $mime = mime_content_type($uploadedFile['tmp_name']) ?: 'application/octet-stream';

    try {
        s3_client()->putObject([
            'Bucket'      => s3_bucket(),
            'Key'         => s3_key($storedName),
            'SourceFile'  => $uploadedFile['tmp_name'],
            'ContentType' => $mime,
        ]);
    } catch (Throwable $e) {
        throw new RuntimeException('Could not save the uploaded file.');
    }
    return $storedName;
}

function storage_delete(?string $storedName): void
{
    if ($storedName === null || $storedName === '') {
        return;
    }

    s3_client()->deleteObject([
        'Bucket' => s3_bucket(),
        'Key'    => s3_key($storedName),
    ]);
}

// Send the browser to a signed S3 link that is valid for 5 minutes.
// $disposition: "inline" (show in the browser) or "attachment" (download).
// The checks that the user may see this capsule are done by the caller, before this function.
function storage_stream(string $storedName, string $mime, string $downloadName, string $disposition): void
{
    // Keep only safe characters for the plain filename; the UTF-8 name goes in filename*.
    $asciiName = preg_replace('/[^A-Za-z0-9._ -]/', '_', $downloadName);

    $command = s3_client()->getCommand('GetObject', [
        'Bucket'                     => s3_bucket(),
        'Key'                        => s3_key($storedName),
        'ResponseContentType'        => $mime,
        'ResponseContentDisposition' => sprintf(
            '%s; filename="%s"; filename*=UTF-8\'\'%s',
            $disposition,
            $asciiName,
            rawurlencode($downloadName)
        ),
    ]);

    $request = s3_client()->createPresignedRequest($command, '+5 minutes');

    header('Location: ' . (string) $request->getUri(), true, 302);
    exit;
}
