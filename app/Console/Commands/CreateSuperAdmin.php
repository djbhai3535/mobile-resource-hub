<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-super-admin
                            {--name= : The full name of the Super Administrator}
                            {--email= : The email address of the Super Administrator}
                            {--password= : The password (optional, will be prompted securely if omitted)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Securely create a new Super Administrator account without hardcoding credentials';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('====================================================');
        $this->info(' Mobile Resource Hub — Super Admin Account Provision');
        $this->info('====================================================');

        // 1. Ensure the super-admin role exists
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super-admin'],
            [
                'display_name' => 'Super Administrator',
                'description' => 'Unrestricted root-level access to all platform resources and settings.',
            ]
        );

        // 2. Obtain Name
        $name = $this->option('name') ?: $this->ask('Enter Administrator Full Name');
        while (empty(trim($name))) {
            $this->error('Name is required.');
            $name = $this->ask('Enter Administrator Full Name');
        }

        // 3. Obtain Email
        $email = $this->option('email') ?: $this->ask('Enter Administrator Email Address');
        $emailValidator = Validator::make(['email' => $email], [
            'email' => 'required|email|max:191',
        ]);

        while ($emailValidator->fails()) {
            $this->error($emailValidator->errors()->first('email'));
            $email = $this->ask('Enter Administrator Email Address');
            $emailValidator = Validator::make(['email' => $email], [
                'email' => 'required|email|max:191',
            ]);
        }

        // 4. Check if account already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->warn("A user with email [{$email}] already exists in the system.");
            if ($existingUser->hasRole('super-admin')) {
                $this->info("This user already holds the [super-admin] role. No changes made.");
                return Command::SUCCESS;
            }

            if ($this->confirm("Assign the Super Administrator role to existing user [{$existingUser->name}]?", true)) {
                $existingUser->roles()->syncWithoutDetaching([$superAdminRole->id]);
                $this->info("Super Administrator role successfully granted to [{$email}].");
                return Command::SUCCESS;
            }

            $this->warn('Operation cancelled by operator.');
            return Command::FAILURE;
        }

        // 5. Obtain Password
        $password = $this->option('password');
        if (empty($password)) {
            $password = $this->secret('Enter Password (minimum 8 characters, will not echo)');
            $passwordConfirmation = $this->secret('Confirm Password');

            while (strlen($password) < 8 || $password !== $passwordConfirmation) {
                if (strlen($password) < 8) {
                    $this->error('Password must be at least 8 characters in length.');
                } elseif ($password !== $passwordConfirmation) {
                    $this->error('Passwords do not match. Please try again.');
                }
                $password = $this->secret('Enter Password (minimum 8 characters, will not echo)');
                $passwordConfirmation = $this->secret('Confirm Password');
            }
        } else {
            if (strlen($password) < 8) {
                $this->error('The provided --password must be at least 8 characters in length.');
                return Command::FAILURE;
            }
        }

        // 6. Create User securely
        $user = User::create([
            'name' => trim($name),
            'email' => strtolower(trim($email)),
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        // 7. Attach Super Admin Role
        $user->roles()->syncWithoutDetaching([$superAdminRole->id]);

        $this->newLine();
        $this->info("✓ Super Administrator account [{$user->email}] successfully created!");
        $this->line("  User ID: {$user->id}");
        $this->line("  Name:    {$user->name}");
        $this->line("  Role:    {$superAdminRole->display_name}");
        $this->newLine();
        $this->line("You may now log in at the admin portal URL:");
        $this->comment("  " . url('/admin/login'));
        $this->newLine();

        return Command::SUCCESS;
    }
}
