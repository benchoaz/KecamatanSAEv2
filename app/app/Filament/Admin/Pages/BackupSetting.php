<?php

namespace App\Filament\Admin\Pages;

use App\Models\ModuleSetting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class BackupSetting extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';

    protected static string $view = 'filament.admin.pages.backup-setting';

    protected static ?string $navigationGroup = 'Pengaturan Sistem';
    
    protected static ?string $navigationLabel = 'Pengaturan Backup';
    
    protected static ?string $title = 'Pengaturan Backup Google Drive';

    public ?array $data = [];

    // Hanya super admin yang boleh melihat menu ini
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public function mount(): void
    {
        $this->form->fill([
            'gdrive_path' => ModuleSetting::getValue('backup', 'gdrive_path', 'gdrive:backup/kecamatan-files/'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Konfigurasi Rclone (Google Drive)')
                    ->description('Tentukan lokasi folder Google Drive tempat file backup akan disimpan setiap malam secara otomatis.')
                    ->schema([
                        TextInput::make('gdrive_path')
                            ->label('Alamat Folder (Rclone Path)')
                            ->required()
                            ->hint('Contoh format: gdrive:nama-folder/')
                            ->helperText('Pastikan remote "gdrive" sudah terhubung di VPS Anda via perintah "rclone config".')
                            ->prefixIcon('heroicon-m-folder-open'),
                    ])
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // Simpan nilai ke database
        ModuleSetting::setValue('backup', 'gdrive_path', $data['gdrive_path'], 'string', 'Lokasi folder Google Drive untuk backup cronjob (Rclone)');

        Notification::make()
            ->title('Pengaturan Berhasil Disimpan!')
            ->success()
            ->body('Alamat Google Drive yang baru akan otomatis digunakan pada backup malam ini.')
            ->send();
    }
}
