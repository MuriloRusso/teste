<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment_pix extends Model
{
    protected $table = 'payment_pix';

    protected $fillable = [
        'key_pix', 'participant_id',
    ];
}
