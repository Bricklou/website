<?php

namespace App\Orchid\Layouts;

use App\Models\UserSocialsLinks;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Log;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;


class UserSocialLinksLayout extends Rows
{
    /**
     * Views.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Matrix::make('user.socialLinks')
                ->title('Social Links')
                ->columns([
                    'Network' => 'social_network',
                    'Link' => 'social_link'
                ])
                ->fields([
                    'social_network' => Select::make()
                        ->options(UserSocialsLinks::$SOCIAL_LINKS)
                        ->required(),
                    'social_link' => Input::make()
                        ->type('text')
                        ->max(255)
                        ->required()
                        ->placeholder(__('Link')),
                ])
        ];
    }
}
