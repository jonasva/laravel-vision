<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Cloud project ID
    |--------------------------------------------------------------------------
    |
    | Your API project's ID from Google Cloud Console
    | https://console.cloud.google.com
    |
    */

    'google_cloud_project_id' => env('GOOGLE_CLOUD_PROJECT'),

    /*
    |--------------------------------------------------------------------------
    | Google application credentials path
    |--------------------------------------------------------------------------
    |
    | Path of your the credentials file used to access the API
    | (path is relative to your project root eg: credentials/vision/demo.json)
    |
    */

    'google_app_credentials_path' => env('GOOGLE_APPLICATION_CREDENTIALS'),

    /*
    |--------------------------------------------------------------------------
    | Google Cloud Storage bucket
    |--------------------------------------------------------------------------
    |
    | Storage bucket used to upload and batch process document type files.
    | Make sure your API user has read and write access to this bucket
    |
    | Tip: setup a lifecycle rule for this bucket to automatically delete files older
    | than one day. That way you'll save on storage costs of useless files.
    |
    */

    'google_cloud_storage' => [
        'bucket' => env('GOOGLE_CLOUD_BUCKET'),
        'raw_prefix' => 'raw/',
        'processed_prefix' => 'processed/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Document batch size
    |--------------------------------------------------------------------------
    |
    | Amount of pages per batch for text detection on documents
    |
    */
    'document_batch_size' => 3,

    /*
    |--------------------------------------------------------------------------
    | Maximum file size
    |--------------------------------------------------------------------------
    |
    | Maximum file size in Bytes of a single image or document
    |
    */
    'max_file_size' => 10 * 1000 * 1000, // 10 MB

];
