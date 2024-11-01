<?php

namespace Marble\JiraKpi\Application;

use Carbon\CarbonImmutable;
use Marble\JiraKpi\Domain\Model\CarbonMixinTrait;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        CarbonImmutable::mixin(CarbonMixinTrait::class);

        parent::boot();
    }
}
