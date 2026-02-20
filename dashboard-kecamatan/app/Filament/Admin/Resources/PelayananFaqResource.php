<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PelayananFaqResource\Pages;
use App\Filament\Admin\Resources\PelayananFaqResource\RelationManagers;
use App\Models\PelayananFaq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PelayananFaqResource extends Resource
{
    protected static ?string $model = PelayananFaq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    public static function canViewAny(): bool
    {
        return auth()->user()->isSuperAdmin() || auth()->user()->isOperatorKecamatan();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail FAQ')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->options([
                                'Layanan' => 'Layanan',
                                'Dokumen' => 'Dokumen',
                                'Umum' => 'Umum',
                                'Emergency' => 'Emergency',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                        Forms\Components\TextInput::make('keywords')
                            ->placeholder('e.g. kk, ktp, pindah')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('question')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('answer')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->badge(),
                Tables\Columns\TextColumn::make('question')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Layanan' => 'Layanan',
                        'Dokumen' => 'Dokumen',
                        'Umum' => 'Umum',
                        'Emergency' => 'Emergency',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPelayananFaqs::route('/'),
            'create' => Pages\CreatePelayananFaq::route('/create'),
            'edit' => Pages\EditPelayananFaq::route('/{record}/edit'),
        ];
    }
}
