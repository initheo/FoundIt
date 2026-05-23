<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'id'         => 1,
                'name'       => 'muqoffin',
                'email'      => 'muqoffin@student.uisi.ac.id',
                'password'   => '$2y$12$3rqJ69Z07vipUh8YHucbce/BHygbCSJVcnTq4Oc19j0NPHfOt8sb2', // password123
                'phone'      => '081231926990',
                'prodi_unit' => 'Informatika',
                'role'       => 'admin',
                'photo_url'  => '/storage/photos/profiles/profile_1_1779494046.jpg',
                'created_at' => '2026-05-06 15:29:46',
                'updated_at' => '2026-05-06 15:37:08',
            ],
            [
                'id'         => 2,
                'name'       => 'Muhammad Muqoffin Nuha',
                'email'      => 'muhammad.nuha23@student.uisi.ac.id',
                'password'   => '$2y$12$3rqJ69Z07vipUh8YHucbce/BHygbCSJVcnTq4Oc19j0NPHfOt8sb2', // password123
                'phone'      => '081231926990',
                'prodi_unit' => 'Informatika',
                'role'       => 'admin',
                'photo_url'  => null,
                'created_at' => '2026-05-06 15:31:33',
                'updated_at' => '2026-05-06 15:31:33',
            ],
            [
                'id'         => 3,
                'name'       => 'Ari Setia Hinanda',
                'email'      => 'ari.hinanda23@student.uisi.ac.id',
                'password'   => '$2y$12$3rqJ69Z07vipUh8YHucbce/BHygbCSJVcnTq4Oc19j0NPHfOt8sb2', // password123
                'phone'      => '081234567891',
                'prodi_unit' => 'Informatika',
                'role'       => 'admin',
                'photo_url'  => null,
                'created_at' => '2026-05-06 15:31:33',
                'updated_at' => '2026-05-06 15:31:33',
            ],
            [
                'id'         => 4,
                'name'       => 'Sustri',
                'email'      => 'sustri.simamora23@student.uisi.ac.id',
                'password'   => '$2y$12$3rqJ69Z07vipUh8YHucbce/BHygbCSJVcnTq4Oc19j0NPHfOt8sb2', // password123
                'phone'      => '082179176800',
                'prodi_unit' => 'Informatika',
                'role'       => 'user',
                'photo_url'  => null,
                'created_at' => '2026-05-10 17:00:43',
                'updated_at' => '2026-05-10 17:00:43',
            ],
            [
                'id'         => 5,
                'name'       => 'Muhammad Muqoffin Nuha',
                'email'      => 'muqoffinn@student.uisi.ac.id',
                'password'   => '$2y$12$3rqJ69Z07vipUh8YHucbce/BHygbCSJVcnTq4Oc19j0NPHfOt8sb2', // password123
                'phone'      => '081231926990',
                'prodi_unit' => 'Informatika',
                'role'       => 'user',
                'photo_url'  => '/storage/photos/profiles/profile_5_1779272882.jpg',
                'created_at' => '2026-05-20 14:41:01',
                'updated_at' => '2026-05-20 17:28:02',
            ],
            [
                'id'         => 6,
                'name'       => 'Ari Setia Hinanda',
                'email'      => 'ari.hinanda233@student.uisi.ac.id',
                'password'   => '$2y$12$3rqJ69Z07vipUh8YHucbce/BHygbCSJVcnTq4Oc19j0NPHfOt8sb2', // password123
                'phone'      => '085604477442',
                'prodi_unit' => 'Informatika',
                'role'       => 'user',
                'photo_url'  => null,
                'created_at' => '2026-05-20 15:03:32',
                'updated_at' => '2026-05-20 15:03:32',
            ],
            [
                'id'         => 7,
                'name'       => 'Andi Pratama',
                'email'      => 'andi@student.uisi.ac.id',
                'password'   => '$2y$12$3rqJ69Z07vipUh8YHucbce/BHygbCSJVcnTq4Oc19j0NPHfOt8sb2', // password123
                'phone'      => '082112345678',
                'prodi_unit' => 'Informatika',
                'role'       => 'user',
                'photo_url'  => null,
                'created_at' => '2026-05-20 15:22:54',
                'updated_at' => '2026-05-20 15:22:54',
            ],
            [
                'id'         => 8,
                'name'       => 'anriu',
                'email'      => 'theo@student.uisi.ac.id',
                'password'   => '$2y$12$3rqJ69Z07vipUh8YHucbce/BHygbCSJVcnTq4Oc19j0NPHfOt8sb2', // password123
                'phone'      => '081231926990',
                'prodi_unit' => 'informatika',
                'role'       => 'user',
                'photo_url'  => null,
                'created_at' => '2026-05-20 18:11:21',
                'updated_at' => '2026-05-20 18:11:21',
            ],
        ];

        foreach ($users as $user) {
            // Use raw DB insert to bypass model hashing — password already hashed
            DB::table('users')->updateOrInsert(
                ['id' => $user['id']],
                $user
            );
        }

        // Reset auto-increment sequence
        $maxId = DB::table('users')->max('id') ?? 0;
        if (config('database.default') === 'sqlite') {
            DB::statement("UPDATE sqlite_sequence SET seq = $maxId WHERE name = 'users'");
        } elseif (config('database.default') === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('users', 'id'), $maxId)");
        }

        // Copy seed profile photos to storage
        $seedImagesPath = database_path('seeders/images/profiles');
        $storagePath = storage_path('app/public/photos/profiles');
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0775, true);
        }
        if (is_dir($seedImagesPath)) {
            foreach (glob("$seedImagesPath/*.jpg") as $file) {
                copy($file, "$storagePath/" . basename($file));
            }
            $this->command->info('Seed profile photos copied to storage.');
        }
    }
}
