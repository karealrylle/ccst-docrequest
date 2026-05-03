<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    protected $fillable = [
        'reference_number',
        'user_id',
        'student_number',
        'full_name',
        'email',
        'contact_number',
        'course_program',
        'year_level',
        'section',
        'total_fee',
        'document_types',
        'status',
        'claiming_number',
        'appointment_id',
        'processed_by',
        'remarks',
        'is_walk_in',
        'walk_in_handled_by',
        'is_printable',
        'payment_status',
        'paid_at',
        'completed_at',
    ];

    protected $casts = [
        'total_fee' => 'decimal:2',
        'is_walk_in' => 'boolean',
        'is_printable' => 'boolean',
        'completed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_READY_FOR_PICKUP = 'ready_for_pickup';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(DocumentRequestItem::class);
    }

    public function appointment()
    {
        return $this->hasOne(Appointment::class, 'document_request_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function walkInHandledBy()
    {
        return $this->belongsTo(User::class, 'walk_in_handled_by');
    }

    public function generatedDocuments()
    {
        return $this->hasMany(GeneratedDocument::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(StatusLog::class);
    }

    // Helper method to get document types as array
    public function getDocumentTypesArrayAttribute()
    {
        return $this->document_types ? explode(',', $this->document_types) : [];
    }
    
    // Helper method to set document types from array
    public function setDocumentTypesArrayAttribute($value)
    {
        $this->attributes['document_types'] = is_array($value) ? implode(',', $value) : $value;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeReadyForPickup($query)
    {
        return $query->where('status', self::STATUS_READY_FOR_PICKUP);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isReadyForPickup(): bool
    {
        return $this->status === self::STATUS_READY_FOR_PICKUP;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isPrintable(): bool
    {
        return $this->is_printable;
    }

    public function isWalkIn(): bool
    {
        return $this->is_walk_in;
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function generateClaimingNumber()
    {
        $claimingNumber = 'CLM-' . strtoupper(substr(uniqid(), 0, 6));
        $this->update(['claiming_number' => $claimingNumber]);
        return $claimingNumber;
    }

    /**
     * Log status change
     */
    public function logStatusChange($oldStatus, $newStatus, $notes = null)
    {
        return StatusLog::create([
            'document_request_id' => $this->id,
            'changed_by' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
        ]);
    }
}