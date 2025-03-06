<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getTypes()
 * @method static string|null getTypeName(string $type)
 * @method static bool isValidType(string $type)
 * @method static array getUserCountByType(bool $useCache = true)
 * @method static array getPermissionsForType(string $type)
 * 
 * @see \App\Services\UserTypeService
 */
class UserType extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'user.type';
    }
}
