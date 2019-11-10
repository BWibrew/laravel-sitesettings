<?php

namespace BWibrew\SiteSettings\Models;

use BWibrew\SiteSettings\Interfaces\ScopeInterface;
use BWibrew\SiteSettings\Traits\ManagesSettingScopes;
use Illuminate\Database\Eloquent\Model;

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
