<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:create-admin {name} {email} {password}', function (string $name, string $email, string $password) {
    $user = User::query()->firstOrNew(['email' => $email]);
    $user->name = $name;
    $user->password = $password;
    $user->email_verified_at ??= now();
    $user->save();

    $this->info("Admin pronto: {$user->email}");
})->purpose('Create or update a basic admin user for web login');
