<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Searchable;

    protected static function booted(): void
    {
        // Пользователь сохраняется при каждом входе; не дёргаем поиск на каждое сохранение,
        // а синхронизируем только при смене публичных полей (и из Article::syncAuthorSearch()).
        static::disableSearchSyncing();

        // Перед удалением автора переносим его имя в статьи, чтобы они не остались «без автора».
        static::deleting(static function (self $user) {
            $user->articles()->get()->each(static function (Article $article) use ($user) {
                $article->forceFill([
                    'author_name' => filled($article->author_name) ? $article->author_name : $user->name,
                    'author_id' => null,
                ])->saveQuietly();
            });
        });

        static::deleted(static fn (self $user) => $user->unsearchable());

        static::saved(static function (self $user) {
            if ($user->wasChanged(['name', 'organisation'])) {
                $user->shouldBeSearchable() ? $user->searchable() : $user->unsearchable();
            }
        });
    }

    public function searchableAs(): string
    {
        return 'authors';
    }

    /** В индекс авторов попадают только авторы с опубликованными статьями. */
    public function shouldBeSearchable(): bool
    {
        return $this->publishedArticles()->exists();
    }

    /** Только публичные поля: email и пароль в индекс не попадают. */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'organisation' => $this->organisation,
        ];
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function publishedArticles(): HasMany
    {
        return $this->articles()
            ->whereNotNull('release_id')
            ->whereHas('release', static fn ($q) => $q->whereNotNull('published_at'));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'organisation',
        'country',
        'email',
        'password',
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
        ];
    }
}
