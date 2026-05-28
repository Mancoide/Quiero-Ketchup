<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserStatus;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable, HasRoles, HasPanelShield, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
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
            'status' => UserStatus::class,
        ];
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(100)
                    ->height(100)
                    ->sharpen(10)
                    ->format('webp')
                    ->nonQueued();

                $this->addMediaConversion('medium')
                    ->width(300)
                    ->height(300)
                    ->sharpen(10)
                    ->format('webp')
                    ->queued();

                $this->addMediaConversion('large')
                    ->width(600)
                    ->height(600)
                    ->sharpen(10)
                    ->format('webp')
                    ->queued();
            });
    }

    /**
     * Get avatar URL with conversion
     */
    public function getAvatarUrl(string $conversion = 'thumb'): string
    {
        return $this->getFirstMediaUrl('avatar', $conversion)
            ?: asset('images/default-avatar.png');
    }

    /**
     * Check if user has avatar
     */
    public function hasAvatar(): bool
    {
        return $this->hasMedia('avatar');
    }

    /**
     * Scope: Active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', UserStatus::ACTIVE);
    }

    /**
     * Scope: Inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('status', UserStatus::INACTIVE);
    }

    /**
     * Scope: Suspended users
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', UserStatus::SUSPENDED);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function reconciliations(): HasMany
    {
        return $this->hasMany(Reconciliation::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole((string) config('filament-shield.super_admin.name', 'super_admin'));
    }

    public function isSubdirectora(): bool
    {
        return $this->hasRole('subdirectora');
    }

    public function isContabilidad(): bool
    {
        return $this->hasRole('contabilidad');
    }

    public function canAccessStandardReconciliations(): bool
    {
        return $this->isSuperAdmin() || $this->isContabilidad();
    }

    public function canAccessCampuzanoReconciliations(): bool
    {
        return $this->isSuperAdmin() || $this->isSubdirectora();
    }
}
