<?php

namespace Database\Seeders;

use App\Models\Claim;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClaimSeeder extends Seeder
{
    public function run(): void
    {
        $claims = [
            [
                'id'               => 1,
                'item_id'          => 13,
                'claimer_id'       => 2,
                'reason'           => 'Ini laptop saya yang hilang minggu lalu. Ada sticker kucing maine coon di belakangnya.',
                'status'           => 'pending',
                'rejection_reason' => null,
                'reviewed_at'      => null,
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 2,
                'item_id'          => 15,
                'claimer_id'       => 2,
                'reason'           => 'Power bank saya hilang kemarin di musholla. Warnanya putih.',
                'status'           => 'pending',
                'rejection_reason' => null,
                'reviewed_at'      => null,
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 3,
                'item_id'          => 17,
                'claimer_id'       => 2,
                'reason'           => 'Kunci gembok loker saya nomor 15.',
                'status'           => 'pending',
                'rejection_reason' => null,
                'reviewed_at'      => null,
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 4,
                'item_id'          => 19,
                'claimer_id'       => 2,
                'reason'           => 'Botol minum saya yang ketinggalan di kelas.',
                'status'           => 'pending',
                'rejection_reason' => null,
                'reviewed_at'      => null,
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 5,
                'item_id'          => 21,
                'claimer_id'       => 1,
                'reason'           => 'Ini jaket saya yang ketinggalan saat bermain basket. Ukurannya L.',
                'status'           => 'pending',
                'rejection_reason' => null,
                'reviewed_at'      => null,
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 6,
                'item_id'          => 23,
                'claimer_id'       => 1,
                'reason'           => 'Dompet saya yang hilang di kantin. Berisi kartu ATM BNI.',
                'status'           => 'pending',
                'rejection_reason' => null,
                'reviewed_at'      => null,
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 7,
                'item_id'          => 24,
                'claimer_id'       => 1,
                'reason'           => 'Mi Band saya yang hilang kemarin di toilet.',
                'status'           => 'pending',
                'rejection_reason' => null,
                'reviewed_at'      => null,
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 8,
                'item_id'          => 20,
                'claimer_id'       => 1,
                'reason'           => 'Ini KTM teman sekelas saya, Rina Wijaya. Sudah konfirmasi dengan dia.',
                'status'           => 'approved',
                'rejection_reason' => null,
                'reviewed_at'      => '2026-05-06 13:32:48',
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 9,
                'item_id'          => 22,
                'claimer_id'       => 1,
                'reason'           => 'Ini kunci kamar kos teman saya. Gantungannya berbentuk hati pink.',
                'status'           => 'approved',
                'rejection_reason' => null,
                'reviewed_at'      => '2026-05-06 10:32:48',
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 10,
                'item_id'          => 16,
                'claimer_id'       => 2,
                'reason'           => 'Flashdisk saya yang ketinggalan di ruang dosen.',
                'status'           => 'approved',
                'rejection_reason' => null,
                'reviewed_at'      => '2026-05-05 15:32:48',
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 11,
                'item_id'          => 25,
                'claimer_id'       => 1,
                'reason'           => 'Gelang teman saya yang hilang di musholla.',
                'status'           => 'approved',
                'rejection_reason' => null,
                'reviewed_at'      => '2026-05-04 15:32:48',
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 12,
                'item_id'          => 26,
                'claimer_id'       => 1,
                'reason'           => 'Ini buku skripsi teman saya yang ketinggalan setelah sidang.',
                'status'           => 'approved',
                'rejection_reason' => null,
                'reviewed_at'      => '2026-05-03 15:32:48',
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 13,
                'item_id'          => 14,
                'claimer_id'       => 2,
                'reason'           => 'Sepertinya ini kacamata saya yang hilang.',
                'status'           => 'rejected',
                'rejection_reason' => null,
                'reviewed_at'      => '2026-05-06 14:32:48',
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 14,
                'item_id'          => 18,
                'claimer_id'       => 2,
                'reason'           => 'Topi saya yang hilang di lapangan.',
                'status'           => 'rejected',
                'rejection_reason' => null,
                'reviewed_at'      => '2026-05-04 15:32:48',
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 15,
                'item_id'          => 13,
                'claimer_id'       => 1,
                'reason'           => 'Saya rasa ini laptop adik saya.',
                'status'           => 'rejected',
                'rejection_reason' => null,
                'reviewed_at'      => '2026-05-02 15:32:48',
                'created_at'       => '2026-05-06 15:32:48',
                'updated_at'       => '2026-05-06 15:32:48',
            ],
            [
                'id'               => 16,
                'item_id'          => 37,
                'claimer_id'       => 7,
                'reason'           => 'Ini laptop saya yang hilang minggu lalu di lab komputer, warna putih dengan case transparan',
                'status'           => 'rejected',
                'rejection_reason' => 'gajelas',
                'reviewed_at'      => '2026-05-20 17:43:34',
                'created_at'       => '2026-05-20 17:41:36',
                'updated_at'       => '2026-05-20 17:43:34',
            ],
            [
                'id'               => 17,
                'item_id'          => 37,
                'claimer_id'       => 7,
                'reason'           => 'ibhjthrgdbrhrhfbfhrhrb',
                'status'           => 'approved',
                'rejection_reason' => null,
                'reviewed_at'      => '2026-05-20 17:51:24',
                'created_at'       => '2026-05-20 17:47:20',
                'updated_at'       => '2026-05-20 17:51:24',
            ],
            [
                'id'               => 18,
                'item_id'          => 38,
                'claimer_id'       => 7,
                'reason'           => 'ini barang saya!!!
balikin gak',
                'status'           => 'rejected',
                'rejection_reason' => 'Deskripsi tidak sesuai dengan barang yang saya temukan',
                'reviewed_at'      => '2026-05-20 17:55:44',
                'created_at'       => '2026-05-20 17:54:27',
                'updated_at'       => '2026-05-20 17:55:44',
            ],
            [
                'id'               => 19,
                'item_id'          => 36,
                'claimer_id'       => 7,
                'reason'           => 'saya menemukan ini di kampus a',
                'status'           => 'approved',
                'rejection_reason' => null,
                'reviewed_at'      => '2026-05-20 18:15:53',
                'created_at'       => '2026-05-20 18:15:19',
                'updated_at'       => '2026-05-20 18:15:53',
            ],
        ];

        foreach ($claims as $claim) {
            DB::table('claims')->updateOrInsert(
                ['id' => $claim['id']],
                $claim
            );
        }

        // Reset auto-increment sequence
        $maxId = DB::table('claims')->max('id') ?? 0;
        if (config('database.default') === 'sqlite') {
            DB::statement("UPDATE sqlite_sequence SET seq = $maxId WHERE name = 'claims'");
        } elseif (config('database.default') === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('claims', 'id'), $maxId)");
        }
    }
}
