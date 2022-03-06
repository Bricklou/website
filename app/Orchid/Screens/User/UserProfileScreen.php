<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Models\UserSocialsLinks;
use App\Orchid\Layouts\User\ProfilePasswordLayout;
use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\UserSocialLinksLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Orchid\Platform\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserProfileScreen extends Screen
{
    /**
     * Query data.
     *
     * @param Request $request
     *
     * @return array
     */
    public function query(Request $request): iterable
    {
        $user = $request->user();
        $user->load(['roles', 'socialLinks']);
        return [
            'user' => $user,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'My account';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Update your account details such as name, email address and password';
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::block(UserEditLayout::class)
                ->title(__('Profile Information'))
                ->description(__("Update your account's profile information and email address."))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->method('save')
                ),

            Layout::block(UserSocialLinksLayout::class)
                ->title(__('Social Links'))
                ->description(__("Update your account's social links."))
                ->commands(
                    [
                        Button::make(__('Save'))
                            ->type(Color::DEFAULT())
                            ->icon('check')
                            ->method('updateSocialLinks'),
                    ]
                ),

            Layout::block(ProfilePasswordLayout::class)
                ->title(__('Update Password'))
                ->description(__('Ensure your account is using a long, random password to stay secure.'))
                ->commands(
                    Button::make(__('Update password'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->method('changePassword')
                ),
        ];
    }

    /**
     * @param Request $request
     */
    public function save(Request $request): void
    {
        $request->validate([
            'user.name'  => 'required|string',
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($request->user()),
            ],
        ]);

        $request->user()
            ->fill($request->get('user'))
            ->save();

        Toast::info(__('Profile updated.'));
    }

    /**
     * @param Request $request
     */
    public function changePassword(Request $request): void
    {
        $request->validate([
            'old_password' => 'required|password:web',
            'password'     => 'required|confirmed',
        ]);

        tap($request->user(), function ($user) use ($request) {
            $user->password = Hash::make($request->get('password'));
        })->save();

        Toast::info(__('Password changed.'));
    }

    /**
     * @param Request $request
     */
    public function updateSocialLinks(Request $request): void
    {
        Log::debug($request->all());
        $request->validate([
            'user.socialLinks' => 'required|array',
            'user.socialLinks.*.social_network' => [
                'required',
                Rule::in(array_keys(UserSocialsLinks::$SOCIAL_LINKS)),
            ],
            'user.socialLinks.*.social_link' => 'required|url',
        ]);

        $keyToNotDelete = [];
        $objs = [];

        foreach ($request->get('user')['socialLinks'] as $item) {
            if (isset($item['social_link']) && $item['social_link'] !== '') {
                $objs[] = [
                    'user_id' => $request->user()->id,
                    'social_network' => $item['social_network'],
                    'social_link' => $item['social_link'],
                ];
            } else {
                $keyToNotDelete[] = $item['social_network'];
            }
        }

        $request->user()->socialLinks()->whereNotIn('social_network', $keyToNotDelete)->delete();
        $request->user()->socialLinks()
            ->upsert($objs, ["social_network", "user_id"], ['social_link']);

        Toast::info("Social Links updated");
    }
}
