<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Orchid\Platform\Models\Role;
use Orchid\Support\Facades\Dashboard;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin_role = new Role();
        $admin_role->name = "Admin";
        $admin_role->slug = "admin";
        $admin_role->permissions = Dashboard::getAllowAllPermission();
        $admin_role->save();

        $reviewer_role = new Role();
        $reviewer_role->name = "Reviewer";
        $reviewer_role->slug = "reviewer";
        $reviewer_role->permissions = [
            "platform.index" => 1,
            "manage_posts" => 1
        ];
        $reviewer_role->save();
    }
}
