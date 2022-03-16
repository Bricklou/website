<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Orchid\Platform\Models\Role;
use Orchid\Support\Facades\Dashboard;

class OrchidAdmin extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'orchid:admin';

    /**
     * @var string
     */
    protected $signature = 'orchid:admin {name?} {email?} {password?} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user administrator [override]';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $userId = $this->option('id');

            empty($userId)
                ? $this->createNewUser()
                : $this->updateUserPermissions((string) $userId);
        } catch (Exception | QueryException $e) {
            $this->error($e->getMessage());
        }
    }

    protected function createNewUser(): void
    {
        $u = Dashboard::modelClass(User::class)
            ->createAdmin(
                $this->argument('name') ?? $this->ask('What is your name?', 'admin'),
                $this->argument('email') ?? $this->ask('What is your email?', 'admin@admin.com'),
                $this->argument('password') ?? $this->secret('What is the password?')
            );

        $r = Role::firstWhere('slug', 'admin');
        $u->addRole($r);

        $this->info('User created successfully.');
    }

    /**
     * @param string $id
     */
    protected function updateUserPermissions(string $id): void
    {
        $u = Dashboard::modelClass(User::class)
            ->findOrFail($id);

        $u->forceFill([
            'permissions' => [],
        ])
            ->save();

        $r = Role::firstWhere('slug', 'admin');
        $u->addRole($r);

        $this->info('User permissions updated.');
    }
}
