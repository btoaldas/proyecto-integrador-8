<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'digital_signature_key',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'digital_signature_key',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // User roles
    const ROLE_ADMIN = 'admin';
    const ROLE_SECRETARY = 'secretary';
    const ROLE_REVIEWER = 'reviewer';
    const ROLE_VIEWER = 'viewer';

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isAdmin()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isSecretary()
    {
        return $this->hasRole(self::ROLE_SECRETARY);
    }

    public function isReviewer()
    {
        return $this->hasRole(self::ROLE_REVIEWER);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'created_by');
    }

    public function reviewedDocuments()
    {
        return $this->hasMany(Document::class, 'reviewed_by');
    }

    public function approvedDocuments()
    {
        return $this->hasMany(Document::class, 'approved_by');
    }

    public function signatures()
    {
        return $this->hasMany(DigitalSignature::class);
    }
}