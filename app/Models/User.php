<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'workos_id',
        'avatar',
        'password',
        'keycloak_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'workos_id',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    
    /**
     * The permissions that belong to the user.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
    
    /**
     * Get the Keycloak roles for this user.
     */
    public function keycloakRoles()
    {
        return $this->hasMany(UserKeycloakRole::class);
    }
    
    /**
     * Check if the user has a specific role.
     */
    public function hasRole($role)
    {
        // Check local roles
        if ($this->roles->contains('name', $role)) {
            return true;
        }
        
        // Check Keycloak roles
        if ($this->keycloakRoles->contains('role_name', $role)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole($roles)
    {
        $roles = is_array($roles) ? $roles : [$roles];
        
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if the user has all of the given roles.
     */
    public function hasAllRoles($roles)
    {
        $roles = is_array($roles) ? $roles : [$roles];
        
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if the user has a permission.
     */
    public function hasPermission($permission)
    {
        // Check direct permissions
        if ($this->permissions->contains('name', $permission)) {
            return true;
        }
        
        // Check permissions via roles
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permission)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get all permissions the user has via roles.
     */
    public function getAllPermissions(): Collection
    {
        $permissions = $this->permissions;
        
        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }
        
        return $permissions->unique('id');
    }
}
