<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\OfficeSpace;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Actions\ActionGroup;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OfficeSpaceResource\Pages;
use App\Filament\Resources\OfficeSpaceResource\RelationManagers;

class OfficeSpaceResource extends Resource
{
    protected static ?string $model = OfficeSpace::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('thumbnail')
                    ->directory('office-thumbnails')
                    ->image()
                    ->imageEditor()
                    ->required(),
                TextInput::make('duration')
                    ->required()
                    ->numeric()
                    ->prefix('Days'),
                Select::make('city_id')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('IDR'),
                Textarea::make('about')->required()->rows(10)->cols(20)->columnSpanFull(),
                Section::make('Status')->schema([
                    Toggle::make('is_open')->label("Open")->helperText('Enable or disable Office visibility')->default(true),
                    Toggle::make('is_full_booked')->label("Full Booked")->helperText('Enable or disable Capacity status'),
                ]),
                Repeater::make('photos')->relationship('photos')->schema([
                    FileUpload::make('photo')->required()->image()->imageEditor()->directory('office-photos')
                ]),
                Repeater::make('benefits')->relationship('benefits')->schema([
                    TextInput::make('name')->required()
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')->searchable(),
                ImageColumn::make('thumbnail')->searchable(),
                TextColumn::make('city.name'),
                IconColumn::make('is_full_booked')->boolean()
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->label('Available')
            ])
            ->filters([
                //
                SelectFilter::make('city_id')
                    ->label('City')
                    ->relationship('city','name')
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ListOfficeSpaces::route('/'),
            'create' => Pages\CreateOfficeSpace::route('/create'),
            'edit' => Pages\EditOfficeSpace::route('/{record}/edit'),
        ];
    }
}
