<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Controller;

use Elao\Bundle\Accesseo\Checker\BrokenLinkChecker;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Twig\Environment;

class BrokenLinkController
{
    private $twig;

    private $profiler;

    private $brokenLinkChecker;

    public function __construct(Environment $twig, Profiler $profiler, BrokenLinkChecker $brokenLinkChecker)
    {
        $this->twig = $twig;
        $this->profiler = $profiler;
        $this->brokenLinkChecker = $brokenLinkChecker;
    }

    public function __invoke(Request $request, $token): Response
    {
        $profile = $this->profiler->loadProfile($token);

        $links =
            [
                '200' => ['http://www.google.com', 'https://www.nasa.gov/'],
                '404' => ['http://www.google.com/error'],
                'invalid' => ['http://www.google/error'],
                'timeout' => ['http://www.google/timeout'],
            ];

        $linksFromDataCollector = $profile->getCollector('elao.accesseo.accessibility_collector')->getLinks();
        $links = $this->brokenLinkChecker->getExternalBrokenLinks($linksFromDataCollector);

        $template = $this->twig->render(
            '@ElaoAccesseo/profiler/broken_links.html.twig',
            [
                'links' => $links,
            ]);

        return new JsonResponse(['template' => $template]);
    }
}
