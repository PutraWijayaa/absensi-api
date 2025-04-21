<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offices extends Model
{
    protected $fillable = [
        'office_name',
        'office_phone',
        'office_address',
        'office_lat',
        'office_long',
        'is_active',
    ];

    protected $appends = ['is_active_label'];

    public function getIsActiveLabelAttribute()
    {
        switch ($this->is_active) {
            case 1:
                return 'Active';
                break;
            default:
            return 'Inactive';
            break;
            }
    }
}
