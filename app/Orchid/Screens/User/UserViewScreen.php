<?php

namespace App\Orchid\Screens\User;

use Orchid\Screen\Screen;
use App\Models\User;
use App\Models\UserSocialsLinks;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Sight;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\TD;

class UserViewScreen extends Screen
{

    /**
     * @var User
     */
    public $user;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(User $user): iterable
    {
        $user->load(['roles', 'socialLinks']);
        return [
            'user' => $user,
            'socialLinks' => $user->socialLinks,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'User profile';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Edit'))
                ->icon('pencil')
                ->route('platform.systems.users.edit', [
                    'user' => $this->user,
                ]),
            Button::make(__('Remove'))
                ->icon('trash')
                ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                ->method('remove')
                ->canSee($this->user->exists),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::legend('user', [
                Sight::make('id')->popover('Identifier, a symbol which uniquely identifies an object or record'),
                Sight::make('name'),
                Sight::make('email'),
                Sight::make('email_verified_at', 'Email Verified')->render(function (User $user) {
                    return $user->email_verified_at === null
                        ? '<i class="text-danger">●</i> False'
                        : '<i class="text-success">●</i> True';
                }),
                Sight::make('created_at', 'Created'),
                Sight::make('updated_at', 'Updated'),
                Sight::make('socialLinks', 'Linked socials networks')->render(function (User $user) {
                    return view('orchid.socialItemsList', [
                        'socialLinks' => $user->socialLinks->map(function ($item) {
                            $item->social_network = UserSocialsLinks::$SOCIAL_LINKS[$item->social_network];
                            return $item;
                        })
                    ]);
                }),
            ])->title('User'),

        ];
    }
}
