<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OnlinePackage extends Model
{
    protected $table = 'online_packages';

    protected $fillable = [
        'dataid',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    /**
     * If `description` is JSON, returns decoded array; otherwise empty array.
     */
    public function meta(): array
    {
        $desc = $this->description;
        if (empty($desc) || !is_string($desc)) {
            return [];
        }

        $decoded = json_decode($desc, true);
        return is_array($decoded) ? $decoded : [];
    }
}

