<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('shop:make-admin {email?} {--password= : Set the password without prompting}')]
#[Description('Create an admin account for the back office (/admin), or grant admin rights and reset the password of an existing one')]
class MakeAdmin extends Command
{
    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email') ?? text('E-mail', required: true)));

        $user = User::firstOrNew(['email' => $email]);
        $user->name ??= text('Name', default: 'Admin', required: true);

        // Always (re)set the password, so the command also recovers an account whose password is unknown.
        $password = $this->option('password')
            ?? password('Password', required: true, validate: fn ($value) => strlen($value) < 8 ? 'At least 8 characters.' : null);

        if (strlen($password) < 8) {
            $this->error('The password must be at least 8 characters.');

            return self::FAILURE;
        }

        $user->password = $password;
        $user->is_admin = true;
        $user->save();

        $this->info("{$email} can now sign in at ".url('/admin'));

        return self::SUCCESS;
    }
}
