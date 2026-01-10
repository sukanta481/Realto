<?php

namespace App\Events;

use App\Models\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Lead $lead;

    /**
     * Create a new event instance.
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Get the data for webhook.
     */
    public function toWebhook(): array
    {
        return [
            'event' => 'lead.created',
            'timestamp' => now()->toIso8601String(),
            'data' => [
                'id' => $this->lead->id,
                'name' => $this->lead->name,
                'email' => $this->lead->email,
                'phone' => $this->lead->phone,
                'source' => $this->lead->source,
                'status' => $this->lead->status?->name,
                'priority' => $this->lead->priority,
                'budget_range' => $this->lead->budget_range,
                'location_preference' => $this->lead->location_preference,
                'property_type' => $this->lead->property_type,
                'assigned_to' => $this->lead->assignedUser?->name,
                'created_at' => $this->lead->created_at->toIso8601String(),
            ]
        ];
    }
}
