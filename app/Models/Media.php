<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\FilterQueries\MediaFilterQuery;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

final class Media extends BaseMedia
{
    use MediaFilterQuery;
}
