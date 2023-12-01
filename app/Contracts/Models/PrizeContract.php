<?php

namespace App\Contracts\Models;

use App\Models\Campaign;
use App\Models\Game;
use App\Models\Prize;
use Carbon\Carbon;

/**
 * @property int id
 * @property int campaign_id
 * @property int tile_image
 * @property string|null description
 * @property string level
 * @property int weight
 * @property int daily_volume
 *
 * @property Carbon starts_at
 * @property Carbon ends_at
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property Campaign|null campaign
 * @property Game[]|null games
 */
interface PrizeContract
{

}
