<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ElaoAccesseoBundle extends Bundle
{
    public function getPath()
    {
        return \dirname(__DIR__);
    }
}
