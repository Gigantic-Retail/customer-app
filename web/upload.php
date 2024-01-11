<?php
require 'vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;

// Function to handle file uploads to Google Cloud Storage
function uploadToGCS($file)
{
    // Path to your GCP keyfile
    $keyFilePath = 'keyfile.json';

    // Google Cloud Storage bucket name
    $bucketName = 'Temp-Bucket';

    // Create a GCP client
    $gcpClient = new StorageClient([
        'keyFilePath' => $keyFilePath,
        'projectId' => '212055223570',
    ]);

    // Get the default bucket
    $bucket = $gcpClient->bucket($bucketName);

    // Upload the file to the bucket with a unique name
    $objectName = uniqid('file_');
    $object = $bucket->upload(
        fopen($file['tmp_name'], 'r'),
        ['name' => $objectName]
    );

    // Get the public URL of the uploaded file
    $publicUrl = $object->signedUrl(new DateTime('tomorrow'));

    // Return the public URL for further use or storage in your database
    return $publicUrl;
}

// Check if a file was submitted through the form
if ($_FILES['userfile']['error'] === UPLOAD_ERR_OK) {
    // Handle the uploaded file
    $publicUrl = uploadToGCS($_FILES['userfile']);

    // Display the public URL or save it to your database
    echo "File uploaded successfully. Public URL: " . $publicUrl;
} else {
    // Handle file upload error
    echo "File upload failed with error code: " . $_FILES['userfile']['error'];
}
?>
