# Analyze images with Google Cloud Vision

Easy way to analyze images with Laravel and Google Cloud Vision. Check their [demo](https://cloud.google.com/vision/docs/drag-and-drop) to see what it can do. 

## Features

### Optical Character Recognition

Convert an image or PDF document to text

```php
$path = $request->file('file')->getRealPath();

$text = Vision::getFullText($path);
```

### Annotate image

Get annotations of your image for one or more Vision [features](https://cloud.google.com/vision/docs/features).

Make sure you extract the same type of annotations from the response as the feature you requested. (eg: `Type::FACE_DETECTION` -> `$response->getFaceAnnotations()`)

```php
use Google\Cloud\Vision\V1\Feature\Type;

$path = $request->file('file')->getRealPath();

$features = [Type::FACE_DETECTION];

$response = Vision::annotateImage($path, $features);

$faces = $response->getFaceAnnotations();
```

## Installation

### Laravel

This package can be installed through Composer.

```bash
composer require jonasva/laravel-vision
```

Publish config
```bash
php artisan vendor:publish --provider="Jonasva\Vision\VisionServiceProvider"
```

### Google Cloud Console

In order to use the Google Cloud Vision API, you'll need to setup a couple of things in Google Cloud Console. 

1. Go to [Cloud Console](https://console.cloud.google.com) and select a project (or create a new one). 

2. Add your project ID to your env file under `GOOGLE_CLOUD_PROJECT`

3. Go to the API library and find "Cloud Vision API". Click "Enable"

4. Create a service account + credentials file for Cloud Vision API. Place the credentials file in your project, and add the path (relative to your project root) to it in your env file under `GOOGLE_APPLICATION_CREDENTIALS`. (see `config/vision.php` file for more details.) 

5. Setup a Google Cloud Storage bucket and make sure your newly created Cloud Vision service account user has read/write permissions to it. This bucket will be used to process PDF and TIFF type files. 

6. Add the bucket name in your env under `GOOGLE_CLOUD_BUCKET`

7. I suggest setting up a lifecycle rule for your bucket to automatically remove files older than 1 day. 

## Pricing

Make sure you take a look at Cloud Vision API's [pricing](https://cloud.google.com/vision/pricing), as it's not an entirely free service. 
