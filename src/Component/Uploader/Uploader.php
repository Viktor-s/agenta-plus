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
        $this->webPath = rtrim($webPath, '/');
        $this->uploadsPath = trim($uploadsPath, '/');
        $this->tmpPath = rtrim($tmpPath, '/');
    }

    /**
     * Upload files
     *
     * @param Request $request
     * @param string  $inputName
     *
     * @return array
     */
    public function upload(Request $request, $inputName = 'file')
    {
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $requestFile */
        $requestFile = $request->files->get($inputName);

        $filename = md5(uniqid(mt_rand(), true)) . '.' . $requestFile->getClientOriginalExtension();

        $requestFile->move($this->tmpPath, $filename);

        return [
            'path' => $filename,
            'name' => $requestFile->getClientOriginalName(),
            'mime' => $requestFile->getClientMimeType(),
            'size' => $requestFile->getClientSize()
        ];
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
        return $this->tmpPath . '/' . $filename;
    }

    /**
     * Move temporary path to web path
     *
     * @param string $filename
     *
     * @return string
     */
    public function moveTemporaryFileToWebPath($filename)
    {
        $tmpFilePath = $this->getTemporaryFilePath($filename);

        if (!is_file($tmpFilePath)) {
            throw new \RuntimeException(sprintf(
                'Not found temporary file "%s".',
                $filename
            ));
        }

        if (!is_readable($tmpFilePath)) {
            throw new \RuntimeException(sprintf(
                'The file "%s" is not readable.',
                $filename
            ));
        }

        $dirParts = [
            date('Y'),
            date('m'),
            date('d')
        ];

        $pathWithUploads = '/' . $this->uploadsPath . '/' . implode('/', $dirParts) . '/' . $filename;

        $webPath = $this->webPath . $pathWithUploads;
        $filesystem = new Filesystem();

        $dir = dirname($webPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $filesystem->rename($tmpFilePath, $webPath);

        return $pathWithUploads;
    }
}
