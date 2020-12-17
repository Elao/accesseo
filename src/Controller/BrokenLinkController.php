<?php

declare(strict_types=1);

namespace Elao\Bundle\SeoTool\Controller;

use Symfony\Component\HttpFoundation\Response;

class BrokenLinkController extends AbstractController
{
    public function getBrokenLinks(): Response
    {
        return $this->json();
    }
}
