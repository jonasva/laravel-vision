<?php

namespace Jonasva\Vision;

use Illuminate\Support\Facades\Facade;

class VisionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'vision';
    }
}
