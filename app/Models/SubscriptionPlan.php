<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'max_outlets',
        'max_staff',
        'is_custom_permission',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_custom_permission' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'subscription_plan_permissions')
            ->withTimestamps();
    }

    public function subscriptions()
    {
        return $this->hasMany(TenantSubscription::class);
    }
}
