<?php

namespace App\Models\Integrations\Hubspot;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Integrations\Hubspot\HubspotClient
 *
 * @property int $id
 * @property int $block_id
 * @property int $client_id
 * @property int $hs_contact_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
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

class HubspotClient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['block_id', 'client_id', 'hs_contact_id'];
}
