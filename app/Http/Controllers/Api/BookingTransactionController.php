<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingTransactionRequest;
use App\Http\Resources\Api\BookingTransactionResource;
use App\Http\Resources\Api\ViewBookingResource;
use App\Models\BookingTransaction;
use App\Models\OfficeSpace;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class BookingTransactionController extends Controller
{
    public function store(StoreBookingTransactionRequest $request)
    {
        $validateData = $request->validated();

        $officeSpace = OfficeSpace::find($validateData['office_space_id']);

        $validateData['is_paid'] = false;
        $validateData['booking_trx_id'] = BookingTransaction::generateUniqueTrxId();
        $validateData['duration'] = $officeSpace->duration;
        $validateData['ended_at'] = (new \DateTime($validateData['started_at']))->modify("+{$officeSpace->duration} days")->format('Y-m-d');

        $bookingTransaction = BookingTransaction::create($validateData);

        // kirim sms
        $sid = getenv('TWILIO_ACCOUNT_SID');
        $token = getenv('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid, $token);

        $messageBody = "Hi {$bookingTransaction->name}, Terima kasih telah booking kantor di FirstOffice. \n\n";
        $messageBody .= "Pesanan kantor {$bookingTransaction->officeSpace->name} Anda sedang kami proses dengan Booking TRX ID : {$bookingTransaction->booking_trx_id}.\n\n";
        $messageBody .= "Kami akan menginformasikan kembali status pemesanan Anda secepat mungkin.";

        // send sms
        $message = $twilio->messages->create(
            "+6289629657237", // to
            [
                "body" => $messageBody,
                "from" => getenv("TWILIO_PHONE_NUMBER"),
            ]
        );

        // return response
        $bookingTransaction->load('officeSpace');
        return new BookingTransactionResource($bookingTransaction);
    }

    public function booking_details(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'booking_trx_id' => 'required|string',
        ]);

        $booking = BookingTransaction::where('phone_number', $request->phone_number)
            ->where('booking_trx_id', $request->booking_trx_id)
            ->with(['officeSpace', 'officeSpace.city'])
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        return new ViewBookingResource($booking);
    }
}
