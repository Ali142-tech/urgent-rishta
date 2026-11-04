<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * An editable page managed from Admin ▸ Content ▸ Pages: the original Terms & Conditions and Privacy Policy (system pages,
 * shown at /tandc and /privacy) and any page the admin creates (shown at /p/{slug}). The body is sanitised HTML.
 */
class CmsPage extends Model
{
    /** system page slug => its public address (these keep their old URLs) */
    public const SYSTEM_URLS = ['terms' => 'tandc', 'privacy' => 'privacy'];

    protected $table = 'cms_pages';

    protected $fillable = ['slug', 'title', 'body', 'is_active', 'is_system', 'show_in_footer', 'sort_order', 'updated_by'];

    protected $casts = ['is_active' => 'boolean', 'is_system' => 'boolean', 'show_in_footer' => 'boolean'];

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Where visitors read this page. */
    public function publicUrl(): string
    {
        return url(self::SYSTEM_URLS[$this->slug] ?? 'p/' . $this->slug);
    }
}
