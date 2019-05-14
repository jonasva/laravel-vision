<?php

namespace Jonasva\Vision;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageObject;
use Illuminate\Support\Facades\File;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\GcsDestination;
use Google\Cloud\Vision\V1\GcsSource;
use Google\Cloud\Vision\V1\InputConfig;
use Google\Cloud\Vision\V1\OutputConfig;
use Google\Cloud\Vision\V1\AnnotateFileResponse;
use Google\Cloud\Vision\V1\AsyncAnnotateFileRequest;
use Google\Cloud\Vision\V1\ImageContext;

class VisionDocumentClient extends VisionClient
{
    protected $storageClient;

    protected $bucket;

    /**
     * VisionDocumentClient constructor.
     * @param array $config
     * @throws \Google\ApiCore\ValidationException
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->storageClient = new StorageClient([
            'keyFilePath' => base_path($this->config['google_app_credentials_path']),
            'projectId' => $this->config['google_cloud_project_id'],
        ]);
    }

    /**
     * @param string $filePath
     * @param string $mimeType
     * @param array $languageHints
     * @return string
     * @throws \Google\ApiCore\ApiException
     * @throws \Google\ApiCore\ValidationException
     */
    public function getFullText(string $filePath, string $mimeType, array $languageHints = []): string
    {
        $fileName = File::hash($filePath);

        $object = $this->storeToBucket($filePath, $fileName);
        $url = $object->gcsUri();

        $feature = (new Feature())->setType(Type::DOCUMENT_TEXT_DETECTION);

        $gcsSource = (new GcsSource())->setUri($url);

        $inputConfig = (new InputConfig())
            ->setGcsSource($gcsSource)
            ->setMimeType($mimeType);

        $gcsDestination = (new GcsDestination())
            ->setUri($this->getProcessedUrl($fileName));

        $outputConfig = (new OutputConfig())
            ->setGcsDestination($gcsDestination)
            ->setBatchSize($this->config['document_batch_size']);

        $request = (new AsyncAnnotateFileRequest())
            ->setFeatures([$feature])
            ->setInputConfig($inputConfig)
            ->setOutputConfig($outputConfig);

        if (!empty($languageHints)) {
            $imageContext = new ImageContext(['language_hints' => $languageHints]);
            $request->setImageContext($imageContext);
        }

        $requests = [$request];

        $operation = $this->imageAnnotatorClient->asyncBatchAnnotateFiles($requests);
        $operation->pollUntilComplete();

        $objects = $this->getBucket()->objects(['prefix' => $this->getProcessedPath($fileName)]);

        $content = '';

        foreach ($objects as $object) {
            $jsonString = $object->downloadAsString();
            $batch = new AnnotateFileResponse();
            $batch->mergeFromJsonString($jsonString);

            foreach ($batch->getResponses() as $response) {
                if ($annotation = $response->getFullTextAnnotation()) {
                    $content .= $annotation->getText();
                }
            }

            $this->imageAnnotatorClient->close();
        }

        return $content;
    }

    /**
     * @param string $filePath
     * @param string $fileName
     * @return StorageObject
     */
    protected function storeToBucket(string $filePath, string $fileName): StorageObject
    {
        $fileData = $this->getFile($filePath);

        return $this->getBucket()->upload($fileData, [
            'name' => $this->getRawPath($fileName)
        ]);
    }

    /**
     * @return Bucket
     */
    protected function getBucket(): Bucket
    {
        if (!$this->bucket) {
            $this->bucket = $this->storageClient->bucket($this->config['google_cloud_storage']['bucket']);
        }

        return $this->bucket;
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function getRawPath(string $fileName): string
    {
        return $this->config['google_cloud_storage']['raw_prefix'] . $fileName;
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function getProcessedPath(string $fileName): string
    {
        return $this->config['google_cloud_storage']['processed_prefix'] . $fileName . '/';
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function getProcessedUrl(string $fileName): string
    {
        return 'gs://' . $this->config['google_cloud_storage']['bucket'] . '/' . $this->getProcessedPath($fileName);
    }
}
