<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'avatar',
        'provider',
        'telefono',
        'direccion',
        'bio',
        'especialidad',
        'experiencia_anos',
        'rating',
        'instagram',
        'facebook',
        'website',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'rating' => 'decimal:2',
            'experiencia_anos' => 'integer',
        ];
    }

    // Accessors para la información del chef
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    public function getFormattedRatingAttribute()
    {
        return number_format($this->rating, 1);
    }

    public function getExperienceTextAttribute()
    {
        if (!$this->experiencia_anos) return null;
        
        return $this->experiencia_anos . ' ' . 
               ($this->experiencia_anos == 1 ? 'año' : 'años') . 
               ' de experiencia';
    }

    public function getInstagramUrlAttribute()
    {
        if (!$this->instagram) return null;
        
        // Si ya tiene https://, lo deja igual, si no, lo agrega
        if (str_starts_with($this->instagram, 'http')) {
            return $this->instagram;
        }
        
        // Si empieza con @, lo remueve
        $username = str_starts_with($this->instagram, '@') ? 
                   substr($this->instagram, 1) : $this->instagram;
        
        return 'https://instagram.com/' . $username;
    }

    public function getFacebookUrlAttribute()
    {
        if (!$this->facebook) return null;
        
        if (str_starts_with($this->facebook, 'http')) {
            return $this->facebook;
        }
        
        return 'https://facebook.com/' . $this->facebook;
    }

    // Relaciones
    public function cenas()
    {
        return $this->hasMany(Cena::class);
    }
}