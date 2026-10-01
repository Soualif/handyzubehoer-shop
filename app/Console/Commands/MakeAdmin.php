<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('shop:make-admin {email?}')]
#[Description('Create an admin account for the back office (/admin)')]
class MakeAdmin extends Command
{
    public function handle(): int
    {
        $email = $this->argument('email') ?? text('E-mail', required: true);

        $user = User::firstOrNew(['email' => $email]);
        $user->name ??= text('Name', default: 'Admin', required: true);

        if (! $user->exists) {
            $user->password = password('Password', required: true, validate: fn ($value) => strlen($value) < 10 ? 'At least 10 characters.' : null);
        }

        $user->is_admin = true;
        $user->save();

        $this->info("{$email} can now sign in at ".url('/admin'));

        return self::SUCCESS;
    }
}
