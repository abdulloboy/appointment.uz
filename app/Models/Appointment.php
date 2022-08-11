<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Appointment extends Model
{
    use UUID, HasApiTokens, HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $fillable = [
        'id',
        'identifier',
        'participant',
        'performer',
        'status',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'identifier',
        'participant',
        'performer',
    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'identifier',
        'participant',
        'performer',
        'status',
    ];

    /**
     * Interact with the Appointment's identifier.
     *
     * @return  \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function identifier(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => [
                (object)[
                    'system' => $attributes['system'],
                    'type' => $attributes['type'],
                    'use' => $attributes['use'],
                    'value' => $attributes['value'],
                    'period' => (object)[
                        'start' => $attributes['period_start'],
                        'end' => $attributes['period_end'] ?? null,
                    ],
                    'assigner' => (object)[
                        'reference' => $attributes['assigner'],
                    ],
                ]
            ],
            set: function (array $identifier) {
                return [
                    'assigner' => $identifier[0]['assigner']['reference'],
                    'period_start' => $identifier[0]['period']['start'],
                    'system' => $identifier[0]['system'],
                    'type' => $identifier[0]['type'],
                    'use' => $identifier[0]['use'],
                    'value' => $identifier[0]['value'],
                ];
            },
        );
    }

    /**
     * Interact with the Appointment's performer.
     *
     * @return  \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function performer(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => (object)[
                'reference' => 'Organization/' . $attributes['performer_id'],
            ],
            set: function (array $performer) {
                $performer_id = null;
                if ($performer && $performer['reference']) {
                    $performerE = explode('/', $performer['reference']);
                    if (count($performerE) === 2) {
                        $performer_id = $performerE[1];
                    } else {
                        $performer_id = $performer['reference'];
                    }
                }
                return [
                    'performer_id' => $performer_id,
                ];
            },
        );
    }

    /**
     * Interact with the Appointment's participant.
     *
     * @return  \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function participant(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $participant_ids[] = (object)[
                    'actor' => (object)[
                        'reference' => 'Practitioner/' . $attributes['practitioner_id'],
                    ]
                ];

                $participant_ids[] = (object)[
                    'actor' => (object)[
                        'reference' => 'Patient/' . $attributes['patient_id'],
                    ]
                ];

                return $participant_ids;
            },
            set: function (array $participants) {
                $participant_ids = [];
                foreach ($participants as $participant) {
                    if ($participant['actor'] && $participant['actor']['reference']) {
                        $participantE = explode('/', $participant['actor']['reference']);
                        if (count($participantE) === 2) {
                            if (strtolower($participantE[0]) == "practitioner") {
                                $participant_ids['practitioner_id'] = $participantE[1];
                            } elseif (strtolower($participantE[0]) == "patient") {
                                $participant_ids['patient_id'] = $participantE[1];
                            }
                        }
                    }
                }
                return $participant_ids;
            },
        );
    }
}
