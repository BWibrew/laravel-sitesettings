<?php

namespace BWibrew\SiteSettings\Models;

use Illuminate\Database\Eloquent\Model;
use BWibrew\SiteSettings\Interfaces\ScopeInterface;
use BWibrew\SiteSettings\Traits\ManagesSettingScopes;

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
