<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RessourcePedagogique extends Model
{
    use HasFactory;

    protected $table = 'ressources_pedagogiques';

    protected $fillable = ['titre', 'type', 'url_moodle', 'sequence_id', 'activite_id'];

    // Une ressource appartient à une séquence
    public function sequence()
    {
        return $this->belongsTo(SequencePedagogique::class);
    }

    // Association "porter" : une ressource est rattachée à 0 ou 1 activité
    public function activite()
    {
        return $this->belongsTo(ActivitePedagogique::class, 'activite_id');
    }
}