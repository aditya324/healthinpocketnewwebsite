<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'body', 'reply', 'status'])]
class SupportMessage extends Model
{
    use HasPublicUuid;
}
