<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/front', name: 'front_')]
class FrontController extends AbstractController
{
    private string $frontDir;

    public function __construct(string $projectDir)
    {
        $this->frontDir = $projectDir . '/public/front';
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->serveFile('/index.html');
    }

    #[Route('/{path}', name: 'spa', requirements: ['path' => '.*'])]
    public function spa(string $path): Response
    {
        $filePath = $this->frontDir . '/' . $path;

        if (file_exists($filePath) && is_file($filePath)) {
            return new BinaryFileResponse($filePath);
        }

        return $this->serveFile('/index.html');
    }

    private function serveFile(string $relativePath): Response
    {
        $fullPath = $this->frontDir . $relativePath;

        if (!file_exists($fullPath)) {
            throw new NotFoundHttpException('Frontend not built yet. Run: cd frontend && npm run build');
        }

        return new BinaryFileResponse($fullPath);
    }
}
