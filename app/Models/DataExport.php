<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'export_id',
        'user_id',
        'format',
        'status',
        'file_path',
        'file_size',
        'email_delivery',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'email_delivery' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user who requested the export.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the export has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get the download URL.
     */
    public function getDownloadUrl(): ?string
    {
        if ($this->status !== 'completed' || $this->isExpired() || !$this->file_path) {
            return null;
        }

        return url("api/v1/settings/exports/{$this->export_id}/download");
    }

    /**
     * Generate a unique export ID.
     */
    public static function generateExportId(): string
    {
        return 'exp_' . bin2hex(random_bytes(8));
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($export) {
            if (empty($export->export_id)) {
                $export->export_id = static::generateExportId();
            }
        });
    }

    /**
     * Format for API response.
     */
    public function toApiResponse(): array
    {
        $status = $this->isExpired() ? 'expired' : $this->status;
        
        return [
            'id' => $this->export_id,
            'format' => $this->format,
            'status' => $status,
            'file_size' => $this->file_size,
            'download_url' => $this->getDownloadUrl(),
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
        ];
    }
}

