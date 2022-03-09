<?php

declare(strict_types=1);

use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Posts\PostEditScreen;
use App\Orchid\Screens\Posts\PostListScreen;
use App\Orchid\Screens\Posts\PostViewScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use App\Orchid\Screens\User\UserViewScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Profile'), route('platform.profile'));
    });

// Platform > System > Users
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(function (Trail $trail, $user) {
        return $trail
            ->parent('platform.systems.users')
            ->push(__('User'), route('platform.systems.users.edit', $user));
    });

// Platform > Systems > Users > View
Route::screen("user/{user}", UserViewScreen::class)
    ->name('platform.systems.users.view')
    ->breadcrumbs(function (Trail $trail, $user) {
        return $trail
            ->parent('platform.systems.users')
            ->push(__('User'), route('platform.systems.users.view', $user));
    });

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.systems.users')
            ->push(__('Create'), route('platform.systems.users.create'));
    });

// Platform > System > Users > User
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Users'), route('platform.systems.users'));
    });

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(function (Trail $trail, $role) {
        return $trail
            ->parent('platform.systems.roles')
            ->push(__('Role'), route('platform.systems.roles.edit', $role));
    });

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.systems.roles')
            ->push(__('Create'), route('platform.systems.roles.create'));
    });

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Roles'), route('platform.systems.roles'));
    });


// Plateform > Posts
Route::screen('posts', PostListScreen::class)
    ->name('platform.systems.posts')
    ->breadcrumbs(function (Trail $trail) {
        return $trail->parent('platform.index')
            ->push(__('Posts'), route('platform.systems.posts'));
    });

// Platform > Posts > Create
Route::screen('post/create', PostEditScreen::class)
    ->name('platform.systems.posts.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail->parent('platform.systems.posts')
            ->push(__('Create'), route('platform.systems.posts.create'));
    });

// Platform > Posts > Edit
Route::screen('post/{post}/edit', PostEditScreen::class)
    ->name('platform.systems.posts.edit')
    ->breadcrumbs(function (Trail $trail, $post) {
        return $trail->parent('platform.systems.posts')
            ->push(__('Edit'), route('platform.systems.posts.edit', $post));
    });

// Platform > Posts > View
Route::screen('post/{post}', PostViewScreen::class)
    ->name('platform.systems.posts.view')
    ->breadcrumbs(function (Trail $trail, $post) {
        return $trail->parent('platform.systems.posts')
            ->push(__('View'), route('platform.systems.posts.view', $post));
    });
