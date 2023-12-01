<?php

namespace App\Contracts\Models;

use App\Models\Game;
use App\Models\Prize;
use Carbon\Carbon;

/**
 * @property int id
 * @property string timezone
 * @property string name
 * @property string slug
 * @property Carbon starts_at
 * @property Carbon ends_at
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property Game[] games
 * @property Prize[] prizes
 */

interface CampaignContract
{

}
