<?php

namespace App\Orchid\Presenters;

use Illuminate\Support\Facades\Log;
use Laravel\Scout\Builder;
use Orchid\Screen\Contracts\Searchable;
use Orchid\Support\Presenter;

class PostPresenter extends Presenter implements Searchable
{
    /**
     * @return string
     */
    public function label(): string
    {
        return "Posts";
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->entity->title;
    }

    /**
     * @return string
     */
    public function subTitle(): string
    {
        return $this->entity->user->name;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return route('platform.systems.posts.view', $this->entity);
    }

    /**
     * @return string
     */
    public function image(): ?string
    {
        return null;
    }

    /**
     * The number of models to return for show compact search result.
     *
     * @return int
     */
    public function perSearchShow(): int
    {
        return 5;
    }

    /**
     * @return array
     */
    public function searchQuery(?string $query = null): Builder
    {
        $this->entity->load(['user']);
        return $this->entity->search($query);
    }
}
