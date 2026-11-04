<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** One question + answer on the public FAQ page, managed from Admin ▸ Content ▸ FAQs. */
class Faq extends Model
{
    protected $table = 'faqs';

    protected $fillable = ['question', 'answer', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeVisible($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
