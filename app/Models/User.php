<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'first_name',
        'name',
        'birthday',
        'music',
        'interests',
        'occupation',
        'email',
        'password',
        'email_verified_at',
        'two_factor_confirmed_at',
        'is_employee',
        'is_admin',
        'is_customer',
        'admin_id',
        'employee_id',
        'customer_id',
        'about',
        "xis_disabled",
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'old_password',
        'remember_token',
        "two_factor_enabled",
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'password' => 'hashed',
            'new-password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_admin' => 'boolean',
             'pub' => 'boolean',
            'is_employee' => 'boolean',
            'is_customer' => 'boolean',
            'xch_newsletter' => 'boolean',
            'xis_disabled' => 'boolean',
        ];
    }

    public function hasConfirmedTwoFactorAuthentication(): bool
    {
        return !is_null($this->two_factor_secret) && $this->two_factor_confirmed_at;
    }

    public function getFullNameAttribute()
    {
        $name = $this->first_name;

        if (!empty($this->name)) {
            if (!empty($name)) {
                $name .= ' ' . $this->name;
            } else {
                $name = $this->name;
            }
        }

        return $name;
    }

    protected function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->full_name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=0EA5E9';
    }

    public function rights()
    {
        return $this->belongsTo(UsersRight::class, 'users_rights_id');
    }

    /**
     * Prüft, ob ein eingegebener OTP-Code gültig ist.
     */
    public function twoFactorSecretKey(): ?string
    {
        if (!$this->two_factor_secret) {
            return null;
        }

        try {
            // Entschlüsseln und Unserialize
            $decrypted = Crypt::decryptString($this->two_factor_secret);

            // Jetzt sauber deserialisieren
            $secret = unserialize($decrypted);

            return $secret; // Das ist der Base32-Key für Google Authenticator
        } catch (\Exception $e) {
            return null;
        }
    }

    public function verifyTwoFactorCode(string $code): bool
    {
        $secret = $this->twoFactorSecretKey();

        if (!$secret) {
            return false;
        }

        $google2fa = new \PragmaRX\Google2FA\Google2FA();

        // ±1 Zeitintervall Toleranz (30 Sekunden)
        return $google2fa->verifyKey($secret, $code, 1);
    }

    /**
     * Prüft, ob ein eingegebener Recovery-Code gültig ist.
     */
    public function verifyRecoveryCode(string $code): bool
    {
        if (!$this->two_factor_recovery_codes) {
            return false;
        }

        $codes = json_decode(Crypt::decryptString($this->two_factor_recovery_codes), true);

        if (in_array($code, $codes, true)) {
            // Nach Verwendung entfernen
            $this->two_factor_recovery_codes = Crypt::encryptString(json_encode(array_values(array_diff($codes, [$code]))));

            $this->save();
            return true;
        }

        return false;
    }
}
