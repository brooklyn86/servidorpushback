<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MessageItem;

class Message extends Model
{
    use HasFactory;

    public function messagesItem(){
        return $this->hasMany(MessageItem::class);
    }
}
