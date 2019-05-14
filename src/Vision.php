<?php

namespace Jonasva\Vision;

use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Illuminate\Support\Facades\File;
use Jonasva\Vision\Exceptions\NotSupportedException;

class Vision
{
    protected $config;

    protected $visionImageClient;

    protected $visionDocumentClient;

    /**
     * Vision constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $filePath
     * @param array $languageHints
     * @return string
     * @throws NotSupportedException
     */
    public function getFullText(string $filePath, array $languageHints = []): string
    {
        $this->checkFileSize($filePath);

        $mimeType = File::mimeType($filePath);
        $fileType = $this->detectFileType($mimeType);

        if ($fileType == 'image') {
            return $this->getVisionImageClient()->getFullText($filePath, $languageHints);
        }
        elseif ($fileType == 'document') {
            return $this->getVisionDocumentClient()->getFullText($filePath, $mimeType, $languageHints);
        }
    }

    /**
     * @param string $filePath
     * @param array $features
     * @param array $options
     * @return AnnotateImageResponse
     * @throws NotSupportedException
     */
    public function annotateImage(string $filePath, array $features, array $options = []): AnnotateImageResponse
    {
        $this->checkFileSize($filePath);
        $this->checkIsImage($filePath);

        return $this->getVisionImageClient()->annotateImage($filePath, $features, $options);
    }

    /**
     * @param string $filePath
     * @throws NotSupportedException
     */
    protected function checkIsImage(string $filePath): void
    {
        $mimeType = File::mimeType($filePath);
        $fileType = $this->detectFileType($mimeType);

        if ($fileType !== 'image') {
            throw new NotSupportedException($mimeType . ' file type not supported.');
        }
    }

    /**
     * @param string $filePath
     * @throws NotSupportedException
     */
    protected function checkFileSize(string $filePath): void
    {
        $size = File::size($filePath);

        if ($size > $this->config['max_file_size']) {
            throw new NotSupportedException('Maximum file size of ' . $this->config['max_file_size'] . ' Bytes exceeded');
        }
    }

    /**
     * @param string $mimeType
     * @return string
     * @throws NotSupportedException
     */
    protected function detectFileType(string $mimeType): string
    {
        if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/vnd.microsoft.icon'])) {
            return 'image';
        }
        elseif (in_array($mimeType, ['application/pdf', 'image/tiff'])) {
            return 'document';
        }
        else {
            throw new NotSupportedException($mimeType . ' file type not supported.');
        }
    }

    /**
     * @return VisionImageClient
     */
    protected function getVisionImageClient(): VisionImageClient
    {
        if (!$this->visionImageClient) {
            $this->visionImageClient = new VisionImageClient($this->config);
        }

        return $this->visionImageClient;
    }

    /**
     * @return VisionDocumentClient
     */
    protected function getVisionDocumentClient(): VisionDocumentClient
    {
        if (!$this->visionDocumentClient) {
            $this->visionDocumentClient = new VisionDocumentClient($this->config);
        }

        return $this->visionDocumentClient;
    }
}
