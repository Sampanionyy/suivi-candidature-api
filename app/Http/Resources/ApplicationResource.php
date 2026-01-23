<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position' => $this->position,
            'company' => $this->company,
            'job_url' => $this->job_url,
            'applied_date' => $this->applied_date->format('Y-m-d'),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'interview_date' => $this->interview_date?->format('Y-m-d H:i'),
            'needs_follow_up' => $this->needs_follow_up,
            'last_follow_up_date' => $this->last_follow_up_date?->format('Y-m-d'),
            'follow_ups_count' => $this->follow_ups_count,
            'notes' => $this->notes,
            'cv_path' => $this->cv_path,
            'cv_url' => $this->cv_url,
            'cover_letter_path' => $this->cover_letter_path,
            'cover_letter_url' => $this->cover_letter_url,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
