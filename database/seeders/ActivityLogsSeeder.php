<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->pluck('id')->all();
        $modules = ['booking', 'approval', 'vehicle', 'fuel', 'service', 'usage', 'auth'];
        $actions = ['create', 'update', 'approve', 'reject', 'complete', 'login'];

        for ($i = 0; $i < 30; $i++) {
            ActivityLog::query()->create([
                'user_id' => $users ? $users[array_rand($users)] : null,
                'module' => $modules[array_rand($modules)],
                'action' => $actions[array_rand($actions)],
                'description' => 'Activity log generated for demo data.',
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
            ]);
        }
    }
}
