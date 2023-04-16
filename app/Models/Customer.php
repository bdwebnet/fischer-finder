<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $fillable = [
        'id',
        'phone_number',
        'name',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function isNew()
    {
        return $this->messages()->count() <= 1;
    }

}
