<?php

namespace AgentPlus\Controller\Cabinet;

use AgentPlus\Component\Uploader\Uploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UploadableController
{
    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * Construct
     *
     * @param Uploader $uploader
     */
    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * Upload files
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        $files = $this->uploader->upload($request);

        return new JsonResponse($files);
    }
}
