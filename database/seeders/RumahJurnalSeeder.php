<?php

namespace Database\Seeders;

use App\Models\RumahJurnal;
use Illuminate\Database\Seeder;

class RumahJurnalSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/data.json');
        if (!file_exists($path)) {
            $this->command->warn("File JSON tidak ditemukan: $path");
            return;
        }

        $rows = json_decode(file_get_contents($path), true);
        if (!is_array($rows)) {
            $this->command->error("Format JSON tidak valid.");
            return;
        }

        foreach ($rows as $row) {
            $nama  = trim((string)($row['Nama Jurnal'] ?? ''));
            $sinta = $this->parseSinta($row['Sinta'] ?? '');
            $tahun = $this->parseYear($row['Accredited'] ?? '');
            $edisi = $this->parseMonths($row['Jurnal Terbit'] ?? '');
            $link  = trim((string)($row['Link Jurnal'] ?? ''));

            if ($nama === '' || $link === '') {
                continue;
            }

            RumahJurnal::updateOrCreate(
                ['link' => $link],
                [
                    'nama'             => $nama,
                    'sinta'            => $sinta,
                    'tahun_akreditasi' => $tahun,
                    'edisi'            => $edisi,
                ]
            );
        }
    }

    private function parseSinta(string $raw): int
    {
        if (preg_match('/(\d)/', $raw, $m)) {
            return (int)$m[1];
        }
        return 0;
    }

    private function parseYear(string $raw): ?int
    {
        if (preg_match('/\d{4}/', $raw, $m)) {
            return (int)$m[0];
        }
        return null;
    }

    private function parseMonths(string $text): array
    {
        $s = mb_strtolower($text);
        $s = str_replace(['&','/','\\','|',',',';'], ' ', $s);
        $s = str_replace([' dan ', ' and '], ' ', $s);
        $s = preg_replace('/\s+/', ' ', $s);

        $map = [
            'januari'=>'jan','jan'=>'jan',
            'februari'=>'feb','feb'=>'feb',
            'maret'=>'mar','mar'=>'mar',
            'april'=>'apr','apr'=>'apr',
            'mei'=>'mei',
            'juni'=>'jun','jun'=>'jun',
            'juli'=>'jul','jul'=>'jul',
            'agustus'=>'agu','agu'=>'agu',
            'september'=>'sep','sep'=>'sep',
            'oktober'=>'okt','okt'=>'okt',
            'november'=>'nov','nov'=>'nov',
            'desember'=>'des','des'=>'des',
        ];

        $found = [];
        foreach ($map as $k => $v) {
            if (str_contains($s, $k)) {
                $found[] = $v;
            }
        }

        return array_values(array_unique($found));
    }
}
