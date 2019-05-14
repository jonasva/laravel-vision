<?php

namespace Jonasva\Vision;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Illuminate\Support\Facades\File;

class VisionClient
{
    protected $imageAnnotatorClient;

    protected $config;

    /**
     * VisionClient constructor.
     * @param array $config
     * @throws \Google\ApiCore\ValidationException
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->imageAnnotatorClient = new ImageAnnotatorClient([
            'credentials' => base_path($this->config['google_app_credentials_path']),
        ]);
    }

    /**
     * @param string $filePath
     * @return string
     */
    protected function getFile(string $filePath): string
    {
        return File::get($filePath);
    }
}
