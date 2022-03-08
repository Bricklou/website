<?php

namespace App\Orchid\Presenters;

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
        return route('platform.resource.view', [
            'resource' => 'post-resources',
            'id' => $this->entity->id,
        ]);
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
        return $this->entity->search($query);
    }
}
