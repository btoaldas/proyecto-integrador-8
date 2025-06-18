<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'document_type',
        'status',
        'audio_file_path',
        'transcription_text',
        'created_by',
        'reviewed_by',
        'approved_by',
        'session_date',
        'document_hash',
        'is_public',
    ];

    protected $casts = [
        'session_date' => 'date',
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Document statuses
    const STATUS_DRAFT = 'draft';
    const STATUS_REVIEW = 'review';
    const STATUS_APPROVED = 'approved';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    // Document types
    const TYPE_ACTA = 'acta';
    const TYPE_RESOLUTION = 'resolution';
    const TYPE_ORDINANCE = 'ordinance';

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(DigitalSignature::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(DocumentRevision::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function canBeEditedBy(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($this->status === self::STATUS_DRAFT && $this->created_by === $user->id) {
            return true;
        }

        if ($this->status === self::STATUS_REVIEW && $user->isReviewer()) {
            return true;
        }

        return false;
    }

    public function canBeApprovedBy(User $user): bool
    {
        return $user->isAdmin() || ($user->isReviewer() && $this->status === self::STATUS_REVIEW);
    }

    public function canBePublishedBy(User $user): bool
    {
        return ($user->isAdmin() || $user->isSecretary()) && $this->status === self::STATUS_APPROVED;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && $this->is_public;
    }

    public function generateHash(): string
    {
        return hash('sha256', $this->title . $this->content . $this->created_at);
    }
}