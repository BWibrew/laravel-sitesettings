<?php

namespace BWibrew\SiteSettings\Tests\Models;

use BWibrew\SiteSettings\Traits\ManagesSettingScopes;
use Illuminate\Database\Eloquent\Model;
use BWibrew\SiteSettings\Interfaces\Scope as ScopeInterface;

class Scope extends Model implements ScopeInterface
{
    use ManagesSettingScopes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
