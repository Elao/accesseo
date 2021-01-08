<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class BrokenLinkController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $links =
            [
                '200' => ['http://www.google.com', 'https://www.nasa.gov/'],
                '404' => ['http://www.google.com/error'],
                'invalid' => ['http://www.google/error'],
                'timeout' => ['http://www.google/timeout'],
            ];

        $template = $this->twig->render(
            '@ElaoAccesseo/profiler/broken_links.html.twig',
            [
                'links' => $links,
            ]);

        return new JsonResponse(['template' => $template]);
    }
}
