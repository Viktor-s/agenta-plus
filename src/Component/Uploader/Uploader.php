<?php

namespace AgentPlus\Component\Uploader;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class Uploader
{
    /**
     * @var string
     */
    private $webPath;

    /**
     * @var string
     */
    private $tmpPath;

    /**
     * @var string
     */
    private $uploadsPath;

    /**
     * Construct
     *
     * @param string $webPath
     * @param string $uploadsPath
     * @param string $tmpPath
     */
    public function __construct($webPath, $uploadsPath, $tmpPath)
    {
        $this->webPath = $webPath;
        $this->uploadsPath = $uploadsPath;
        $this->tmpPath = $tmpPath;
    }

    /**
     * Upload files
     *
     * @param Request $request
     * @param string  $inputName
     *
     * @return array
     */
    public function upload(Request $request, $inputName = 'attachments')
    {
        $files = [];

        $requestFiles = $request->files->get($inputName);

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        foreach ($requestFiles as $file) {
            $filename = md5(uniqid(mt_rand(), true)) . '.' . $file->getClientOriginalExtension();

            print $file->move($this->tmpPath, $filename);exit();

            $files[] = [
                'path' => $filename,
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getClientSize()
            ];
        }

        return $files;
    }

    /**
     * Get temporary file path
     *
     * @param string $filename
     *
     * @return string
     */
    public function getTemporaryFilePath($filename)
    {
        return $this->tmpPath . '/' . $path;
    }
}
