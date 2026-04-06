<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class UploadsController extends BaseController
{
    /**
     * Serve an uploaded file securely from WRITEPATH/uploads/.
     * This is needed because public/uploads is a symlink that may not exist
     * on all environments. This controller acts as a fallback.
     */
    public function serve(string $path): ResponseInterface
    {
        // Prevent directory traversal
        $path = ltrim(str_replace(['\\', "\0", '..'], ['/', '', ''], $path), '/');

        if ($path === '' || str_contains($path, '..')) {
            return $this->response->setStatusCode(400);
        }

        $filePath = WRITEPATH . 'uploads/' . $path;

        if (!is_file($filePath)) {
            return $this->response->setStatusCode(404);
        }

        $ext  = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'gif'         => 'image/gif',
            'webp'        => 'image/webp',
            'svg'         => 'image/svg+xml',
            'mp4'         => 'video/mp4',
            'webm'        => 'video/webm',
            'pdf'         => 'application/pdf',
            default       => 'application/octet-stream',
        };

        $size    = filesize($filePath);
        $lastMod = filemtime($filePath);
        $etag    = '"' . md5($filePath . $lastMod) . '"';

        // Browser cache: 7 days for images
        $this->response
            ->setHeader('Cache-Control', 'public, max-age=604800')
            ->setHeader('ETag', $etag)
            ->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', $lastMod) . ' GMT')
            ->setHeader('Content-Length', (string) $size)
            ->setContentType($mime);

        // 304 Not Modified check
        $ifNoneMatch  = $this->request->getHeaderLine('If-None-Match');
        $ifModifiedSince = $this->request->getHeaderLine('If-Modified-Since');
        if ($ifNoneMatch === $etag || ($ifModifiedSince && strtotime($ifModifiedSince) >= $lastMod)) {
            return $this->response->setStatusCode(304)->setBody('');
        }

        return $this->response->setBody(file_get_contents($filePath));
    }
}
