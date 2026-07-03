<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Ajouter 'role' et 'actif' dans $fillable
    protected $fillable = [
        'name', 'email', 'password', 'role', 'actif',
    ];

    // Ajouter en bas de la classe, avant la dernière accolade

    // Un user peut avoir un profil enseignant
    public function enseignant()
    {
        return $this->hasOne(Enseignant::class);
    }

    // Vérifications de rôle
    public function estAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function estSecretaire(): bool
    {
        return $this->role === 'secretaire';
    }

    public function estEnseignant(): bool
    {
        return $this->role === 'enseignant';
    }

    // Activités qu'il a validées (en tant que secrétaire/admin)
    public function activitesValidees()
    {
        return $this->hasMany(ActivitePedagogique::class, 'validateur_id');
    }
}
