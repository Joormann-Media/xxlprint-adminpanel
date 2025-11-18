<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestUploadController extends AbstractController
{
    #[Route('/admin/test-upload', name: 'test_upload', methods: ['POST'])]
    public function testUpload(Request $request): JsonResponse
    {
        $contentLength = $request->headers->get('Content-Length');
        $contentType = $request->headers->get('Content-Type');
        $files = $request->files->all();

        return new JsonResponse([
            'message' => 'Upload angekommen!',
            'content_length' => $contentLength,
            'content_type' => $contentType,
            'files_received' => array_keys($files),
            'file_details' => array_map(function($file) {
                if ($file) {
                    return [
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'error' => $file->getError(),
                    ];
                }
                return null;
            }, $files),
        ]);
    }
}
