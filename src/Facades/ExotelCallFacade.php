<?php

namespace Ellaisys\Exotel\Facades;

use Illuminate\Support\Facades\Facade;

class ExotelCallFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'exotel-call';
    }
    
} //Class ends