<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'img_slug',
    'avatar_data',
    'avatar_mime',
    'fname',
    'lname',
    'minitial',
    'fullname',
    'username',
    'email',
    'email_verified_at',
    'password',
    'remember_token',
    'google_id',
    'categories',
    'roletype',
    'is_activated',
    'user_created',
    'user_updated',
    'last_login_ip'
])]
#[Hidden([
    'password',
    'remember_token',
    'google_id',
    'avatar_data',
])]
class User extends Authenticatable
{

    use HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_activated'      => 'boolean',
        ];
    }

    public function menuOverrides()
    {
        return $this->hasMany(\App\Core\Models\MenuUserOverride::class);
    }

    public function permissionOverrides()
    {
        return $this->hasMany(\App\Core\Models\PermissionUserOverride::class);
    }

    /**
     * Override-aware permission check: a per-user allow/deny beats the
     * role-based permission. Use this instead of $user->can() anywhere
     * an individual exception (like Kevin losing user.destroy) matters.
     */
    public function canAccessPermission(string $permissionName): bool
    {
        $override = $this->permissionOverrides()
            ->whereHas('permission', fn ($q) => $q->where('name', $permissionName))
            ->first();

        if ($override) {
            return $override->access === 'allow';
        }

        return $this->can($permissionName);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * The ?v= stamp busts the browser cache whenever the profile changes,
     * so a freshly uploaded avatar shows immediately.
     */
    public function avatarUrl(): string
    {
        if ($this->avatar_data) {
            return route('app.users.avatar', ['user' => $this->id, 'v' => $this->updated_at?->timestamp]);
        }

        return route('app.users.avatar.default');
    }
}

