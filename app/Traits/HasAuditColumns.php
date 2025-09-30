<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait HasAuditColumns
{
    public static function bootHasAuditColumns()
    {
        static::creating(function (Model $model) {
            //$model->created_by = User::SYSTEM_USER;
            //$model->updated_by = User::SYSTEM_USER;
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function (Model $model) {
            $model->updated_by = User::SYSTEM_USER;
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


    public function getCreatedAtBrAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    public function getUpdatedAtBrAttribute()
    {
        return $this->updated_at->format('d/m/Y H:i:s');
    }
}
