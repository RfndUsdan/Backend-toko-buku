<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Utama')
                    ->schema([
                        Forms\Components\TextInput::make('title')->required()->maxLength(255),
                        Forms\Components\TextInput::make('author')->required(),
                        Forms\Components\TextInput::make('publisher')->required(),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('published_year')->numeric()->required(),
                            Forms\Components\Select::make('language')
                                ->options([
                                    'Indonesia' => 'Indonesia',
                                    'English' => 'English',
                                    'Arabic' => 'Arabic',
                                ])->required(),
                        ]),
                    ])->columnSpan(2),

                Forms\Components\Section::make('Detail & Harga')
                    ->schema([
                        Forms\Components\TextInput::make('price')->numeric()->prefix('Rp')->required(),
                        Forms\Components\TextInput::make('pages')->numeric()->required(),
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori/Sub-Kategori')
                            ->relationship('category', 'name') // Mengambil dari tabel categories
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([ // Bonus: Bisa tambah kategori baru lewat modal di sini
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                                Forms\Components\TextInput::make('slug')->required(),
                            ]),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('books') // Sesuai dengan penyimpanan storage Anda
                    ])->columnSpan(1),

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('author')->searchable(),
                Tables\Columns\TextColumn::make('price')->money('IDR')->sortable(),
                
                // Kolom Kategori (Badge) diletakkan di sini
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                // Di sini HANYA boleh ada Filter, gunakan SelectFilter untuk dropdown
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Filter per Kategori')
                    ->relationship('category', 'name') // Mengambil data otomatis dari tabel categories
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Opsi')
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
