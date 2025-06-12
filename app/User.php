<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Chat;

class User extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'name', 'email', 'password','phone','email','role','status','google2fa_secret',
        'google2fa_enabled'
    ];

    function status_display(){
        return $this->status ==1 ? 'Aktif' :'Tidak Aktif';
    }

    /**
     * Get chats sent by this user
     */
    public function fromChats()
    {
        return $this->hasMany(\App\Models\Chat::class, 'from_user_id');
    }

    /**
     * Get chats received by this user
     */
    public function toChats()
    {
        return $this->hasMany(\App\Models\Chat::class, 'to_user_id');
    }

    /**
     * Get the role display name
     */
    public function role_display()
    {
        switch ($this->role) {
            case 1: return 'Admin';
            case 2: return 'Pendaftaran';
            case 3: return 'Dokter';
            case 4: return 'Apotek';
            case 5: return 'Pasien';
            default: return 'User';
        }
    }
    protected $hidden = [
        'password', 'remember_token','google2fa_secret', // Hide this for security
    ];

    protected $casts = [
        'email_verified_at' => 'datetime','google2fa_enabled' => 'boolean',
    ];

    // Add this method to your User model
    public function receivedChats()
    {
        return $this->hasMany(Chat::class, 'to_user_id');
    }
}
