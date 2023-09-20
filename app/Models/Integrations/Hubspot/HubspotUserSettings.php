<?php

namespace App\Models\Integrations\Hubspot;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Integrations\Hubspot\HubspotUserSettings
 *
 * @property int $id
 * @property int $user_id
 * @property int $block_id
 * @property array|null $deal_settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static Builder|HubspotClient newModelQuery()
 * @method static Builder|HubspotClient newQuery()
 * @method static \Illuminate\Database\Query\Builder|HubspotClient onlyTrashed()
 * @method static Builder|HubspotClient query()
 * @method static Builder|HubspotClient whereClientId($value)
 * @method static Builder|HubspotClient whereCreatedAt($value)
 * @method static Builder|HubspotClient whereDeletedAt($value)
 * @method static Builder|HubspotClient whereHsContactId($value)
 * @method static Builder|HubspotClient whereId($value)
 * @method static Builder|HubspotClient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|HubspotClient withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HubspotClient withoutTrashed()
 */

class HubspotUserSettings extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id', 'block_id', 'deal_settings'];

    /**
     * Получение/запись массива настроек (deal_settings)
     *
     * @return Attribute
     */
    protected function dealSettings(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }
}
