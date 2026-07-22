<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = [
        'logo_path',
        'favicon_path',
        'apple_touch_icon_path',
    ];

    /**
     * Obtiene la fila única de configuración, creándola si no existe.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->favicon_path ? Storage::disk('public')->url($this->favicon_path) : null;
    }

    public function getAppleTouchIconUrlAttribute(): ?string
    {
        return $this->apple_touch_icon_path ? Storage::disk('public')->url($this->apple_touch_icon_path) : null;
    }
}
