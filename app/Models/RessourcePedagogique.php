<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RessourcePedagogique extends Model
{
    use HasFactory;

    protected $table = 'ressources_pedagogiques';

    protected $fillable = ['titre', 'type', 'url_moodle', 'sequence_id'];

    // Une ressource appartient à une séquence
    public function sequence()
    {
        return $this->belongsTo(SequencePedagogique::class);
    }
}