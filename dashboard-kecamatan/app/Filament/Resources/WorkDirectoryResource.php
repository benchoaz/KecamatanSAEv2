<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkDirectoryResource\Pages;
use App\Models\WorkDirectory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkDirectoryResource extends Resource
{
    protected static ?string $model = WorkDirectory::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Direktori Kerja & Jasa';

    protected static ?string $modelLabel = 'Direktori Kerja & Jasa Warga';

    protected static ?string $pluralModelLabel = 'Direktori Kerja & Jasa Warga';

    protected static ?string $navigationGroup = 'Layanan Publik';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pekerja / Jasa')
                    ->schema([
                        Forms\Components\TextInput::make('display_name')
                            ->label('Nama Panggilan / Inisial')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Pak Roni, Bu Siti, Mas Budi')
                            ->helperText('Gunakan nama panggilan, bukan nama lengkap'),

                        Forms\Components\Select::make('job_category')
                            ->label('Kategori Pekerjaan')
                            ->required()
                            ->options([
                                'Jasa & Pekerjaan Harian' => 'Jasa & Pekerjaan Harian',
                                'Transportasi Rakyat' => 'Transportasi Rakyat',
                                'Jasa & Pangan Keliling' => 'Jasa & Pangan Keliling',
                            ])
                            ->reactive(),

                        Forms\Components\Select::make('job_type')
                            ->label('Tipe Pekerjaan')
                            ->required()
                            ->options([
                                'harian' => 'Harian',
                                'jasa' => 'Jasa',
                                'keliling' => 'Keliling',
                                'transportasi' => 'Transportasi',
                            ]),

                        Forms\Components\TextInput::make('job_title')
                            ->label('Jenis Pekerjaan / Jasa')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Tukang Sayur Keliling, Ojek, Tukang Bangunan')
                            ->helperText('Sebutkan jenis pekerjaan secara spesifik'),
                    ])->columns(2),

                Forms\Components\Section::make('Area & Waktu Layanan')
                    ->schema([
                        Forms\Components\TextInput::make('service_area')
                            ->label('Wilayah Layanan')
                            ->maxLength(255)
                            ->placeholder('Contoh: Desa Ketompen & sekitar, Seluruh Kecamatan Besuk')
                            ->helperText('Opsional: Sebutkan desa atau wilayah yang dilayani'),

                        Forms\Components\TextInput::make('service_time')
                            ->label('Jam Layanan')
                            ->maxLength(255)
                            ->placeholder('Contoh: Pagi (06.00-09.00), Setiap hari')
                            ->helperText('Opsional: Jam atau waktu layanan'),
                    ])->columns(2),

                Forms\Components\Section::make('Kontak & Deskripsi')
                    ->schema([
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Nomor Telepon / WhatsApp')
                            ->required()
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Contoh: 081234567890')
                            ->helperText('Nomor yang bisa dihubungi warga'),

                        Forms\Components\Textarea::make('short_description')
                            ->label('Keterangan Singkat')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Contoh: Berpengalaman 10 tahun, harga terjangkau')
                            ->helperText('Opsional: Informasi tambahan yang relevan'),
                    ])->columns(1),

                Forms\Components\Section::make('Data Administratif')
                    ->schema([
                        Forms\Components\Select::make('data_source')
                            ->label('Sumber Data')
                            ->required()
                            ->options([
                                'kecamatan' => 'Pendataan Kecamatan',
                                'desa' => 'Laporan Desa / RT / RW',
                                'warga' => 'Permintaan Warga',
                            ])
                            ->default('kecamatan')
                            ->helperText('Dari mana data ini diperoleh?'),

                        Forms\Components\Toggle::make('consent_public')
                            ->label('Izin Tampil Publik')
                            ->default(true)
                            ->helperText('Apakah data ini boleh ditampilkan ke publik?'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'active' => 'Aktif (Tampil di Website)',
                                'inactive' => 'Tidak Aktif (Disembunyikan)',
                            ])
                            ->default('active')
                            ->helperText('Hanya data aktif yang tampil di website'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('job_title')
                    ->label('Jenis Pekerjaan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('job_category')
                    ->label('Kategori')
                    ->colors([
                        'primary' => 'Jasa & Pekerjaan Harian',
                        'success' => 'Transportasi Rakyat',
                        'warning' => 'Jasa & Pangan Keliling',
                    ]),

                Tables\Columns\TextColumn::make('service_area')
                    ->label('Wilayah')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('contact_phone')
                    ->label('Kontak')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('consent_public')
                    ->label('Izin Publik')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('job_category')
                    ->label('Kategori')
                    ->options([
                        'Jasa & Pekerjaan Harian' => 'Jasa & Pekerjaan Harian',
                        'Transportasi Rakyat' => 'Transportasi Rakyat',
                        'Jasa & Pangan Keliling' => 'Jasa & Pangan Keliling',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkDirectories::route('/'),
            'create' => Pages\CreateWorkDirectory::route('/create'),
            'edit' => Pages\EditWorkDirectory::route('/{record}/edit'),
        ];
    }
}
