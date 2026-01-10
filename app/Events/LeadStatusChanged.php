<?php

namespace App\Events;

use App\Models\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Lead $lead;
    public ?string $oldStatus;
    public ?string $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Lead $lead, ?string $oldStatus, ?string $newStatus)
    {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the data for webhook.
     */
    public function toWebhook(): array
    {
        return [
            'event' => 'lead.status_changed',
            'timestamp' => now()->toIso8601String(),
            'data' => [
                'id' => $this->lead->id,
                'name' => $this->lead->name,
                'phone' => $this->lead->phone,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
                'priority' => $this->lead->priority,
                'assigned_to' => $this->lead->assignedUser?->name,
                'updated_at' => $this->lead->updated_at->toIso8601String(),
            ]
        ];
    }
}
