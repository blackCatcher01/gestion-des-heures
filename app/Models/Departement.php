<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departement extends Model
{
    use HasFactory;

    protected $table = 'departements';

    protected $fillable = ['nom', 'code'];

    // Un département a plusieurs enseignants
    public function enseignants()
    {
        return $this->hasMany(Enseignant::class);
    }
}