<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MasterLayanan;
use App\Models\ServiceNode;
use App\Models\ServiceRequirement;

class MigrateFlatServicesToNode extends Command
{
    protected $signature = 'layanan:migrate-tree';
    protected $description = 'Migrate flat MasterLayanan text requirements into Decision Tree Nodes';

    public function handle()
    {
        $layanans = MasterLayanan::where('has_nodes', false)
            ->whereNotNull('attachment_requirements')
            ->get();

        if ($layanans->isEmpty()) {
            $this->info("Tidak ada layanan flat yang perlu dimigrasi.");
            return;
        }

        $this->info("Ditemukan {$layanans->count()} layanan untuk dimigrasi ke mode Decision Tree.");

        $bar = $this->output->createProgressBar($layanans->count());
        $bar->start();

        foreach ($layanans as $layanan) {
            $node = ServiceNode::create([
                'master_layanan_id' => $layanan->id,
                'parent_id'   => null,
                'depth'       => 0,
                'name'        => 'Pengajuan ' . $layanan->nama_layanan,
                'description' => $layanan->deskripsi_syarat ?? '',
                'ikon'        => $layanan->ikon ?? 'fa-circle-check',
                'urutan'      => 0,
                'is_leaf'     => true,
                'is_active'   => true,
            ]);

            $reqs = $layanan->attachment_requirements;
            if (is_string($reqs)) {
                $reqs = json_decode($reqs, true) ?? [];
            }

            foreach ((array) $reqs as $idx => $label) {
                ServiceRequirement::create([
                    'node_id'        => $node->id,
                    'type'           => 'file_upload',
                    'label'          => $label,
                    'description'    => '',
                    'is_required'    => true,
                    'accepted_types' => 'jpg,png,pdf',
                    'max_size_mb'    => 5,
                    'urutan'         => $idx,
                ]);
            }

            $layanan->update(['has_nodes' => true]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Sukses! Semua layanan flat berhasil di-upgrade ke Sistem Decision Tree.");
    }
}
