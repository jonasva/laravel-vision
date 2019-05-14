<?php

namespace Jonasva\Vision;

use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\ImageContext;
use Google\Cloud\Vision\V1\Feature\Type;

class VisionImageClient extends VisionClient
{
    /**
     * @param string $filePath
     * @param array $languageHints
     * @return string
     */
    public function getFullText(string $filePath, array $languageHints = []): string
    {
        $features = [Type::DOCUMENT_TEXT_DETECTION];

        $options = [];

        if (!empty($languageHints)) {
            $imageContext = new ImageContext(['language_hints' => $languageHints]);
            $options = [$imageContext];
        }

        $response = $this->annotateImage($filePath, $features, $options);

        if ($fullTextAnnotation = $response->getFullTextAnnotation()) {
            return $fullTextAnnotation->getText();
        }

        return '';
    }

    /**
     * @param string $filePath
     * @param array $features
     * @param array $options
     * @return AnnotateImageResponse
     * @throws \Google\ApiCore\ApiException
     */
    public function annotateImage(string $filePath, array $features, array $options = []): AnnotateImageResponse
    {
        $fileData = $this->getFile($filePath);

        return $this->imageAnnotatorClient->annotateImage($fileData, $features, $options);
    }
}
