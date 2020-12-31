<?php

declare(strict_types=1);

namespace Elao\Bundle\SeoTool\Controller;

use Elao\Bundle\SeoTool\Checker\BrokenLinkChecker;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BrokenLinkController
{
    public function __invoke(): Response
    {
        return new JsonResponse(
            [
                '200' => ['http://www.google.com', 'https://www.nasa.gov/'],
                '404' => ['http://www.google.com/error'],
                'invalid' => ['http://www.google/error'],
                'timeout' => ['http://www.google/timeout']
            ]
        );
    }
}
