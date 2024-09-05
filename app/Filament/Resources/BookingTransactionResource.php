<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\BookingTransaction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BookingTransactionResource\Pages;
use App\Filament\Resources\BookingTransactionResource\RelationManagers;
use Filament\Notifications\Notification;
use Twilio\Rest\Client;

class BookingTransactionResource extends Resource
{
    protected static ?string $model = BookingTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_paid', true)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('booking_trx_id')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone_number')
                    ->required()
                    ->maxLength(255),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->prefix('IDR'),
                TextInput::make('duration')
                    ->required()
                    ->numeric()
                    ->prefix('Days'),
                DatePicker::make('started_at')
                    ->required(),
                DatePicker::make('ended_at')
                    ->required(),
                Toggle::make('is_paid')
                    ->label("Paid")
                    ->helperText('Payment Status')
                    ->default(true),
                Select::make('office_space_id')
                    ->relationship('officeSpace', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('booking_trx_id')->searchable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('officeSpace.name')->searchable(),
                TextColumn::make('started_at')->searchable()->date(),
                IconColumn::make('is_paid')->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueIcon('heroicon-o-check-circle')
                    ->label('Payment Status')
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->action(function (BookingTransaction $record) {
                            $record->is_paid = true;
                            $record->save();

                            // Trigger the custom notification Notification::make()
                            Notification::make()
                                ->title('Booking Approved')
                                ->success()
                                ->body('The booking has been successfully approved.')
                                ->send();

                            // Find your Account SID and Auth Token at twilio.com/console
                            // and set the environment variables. See http://twil.io/secure
                            $sid = getenv("TWILIO_ACCOUNT_SID");
                            $token = getenv("TWILIO_AUTH_TOKEN");
                            $twilio = new Client($sid, $token);

                            // Create the message with line breaks
                            $messageBody = "Hi {$record->name}, pemesanan Anda dengan kode {$record->booking_trx_id} sudah terbayar penuh.\n\n";
                            $messageBody .= "Silahkan datang kepada lokasi kantor {$record->officeSpace->name} untuk mulai menggunakan ruangan kerja tersebut.\n\n";
                            $messageBody .= "Jika Anda memiliki pertanyaan silahkan menghubungi CS kami di buildwithangga.com/contact-us.";

                            // send sms
                            // $message = $twilio->messages->create(
                            //     "+6289629657237", // to
                            //     [
                            //         "body" => $messageBody,
                            //         "from" => getenv("TWILIO_PHONE_NUMBER"),
                            //     ]
                            // );

                            // send wa
                            $message = $twilio->messages->create(
                                "whatsapp:+6289629657237", // to
                                [
                                    "from" => "whatsapp:" . getenv("TWILIO_PHONE_NUMBER_WA"),
                                    "body" => $messageBody,
                                ]
                            );
                        })
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn(BookingTransaction $record) => !$record->is_paid),
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
            'index' => Pages\ListBookingTransactions::route('/'),
            'create' => Pages\CreateBookingTransaction::route('/create'),
            'edit' => Pages\EditBookingTransaction::route('/{record}/edit'),
        ];
    }
}
