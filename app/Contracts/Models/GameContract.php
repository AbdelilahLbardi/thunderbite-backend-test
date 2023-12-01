<?php

namespace App\Contracts\Models;

use App\Models\Campaign;
use App\Models\Prize;
use Carbon\Carbon;

/**
 * @property int id
 * @property int campaign_id
 * @property int prize_id
 * @property string account
 * @property array|null revealed_tiles
 * @property Carbon|null revealed_at
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property Campaign|null campaign
 * @property Prize|null prize
 */

interface GameContract
{

}
