<?php


namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Request;


class PaymentService
{
    public function initiatePayment($order)
    {
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found');
        }

        $customerDetails = json_decode($order->customer_details);
        $serverKey = config('paytabs.server_key');
        $profileId = config('paytabs.profile_id');

        if (!$serverKey || !$profileId) {
            return [
                'success' => false,
                'error' => 'Server key or Profile ID is not configured properly.',
                'status' => 500
            ];
        }

        $payload = [
            "profile_id" => $profileId,
            "tran_type" => "sale",
            "tran_class" => "ecom",
            "cart_id" => (string)$order->id,
            "cart_currency" => $order->currency,
            "cart_amount" => $order->total_amount,
            "cart_description" => $order->description,
            "customer_details" => [
                "name" => $customerDetails->billing->name,
                "email" => $customerDetails->billing->email,
                "phone" => $customerDetails->billing->phone,
                "street1" => $customerDetails->billing->street1,
                "city" => $customerDetails->billing->city,
                "state" => $customerDetails->billing->state,
                "country" => $customerDetails->billing->country,
                "zip" => $customerDetails->billing->zip,
            ],
            "shipping_details" => [
                "name" => $customerDetails->billing->name,
                "email" => $customerDetails->billing->email,
                "phone" => $customerDetails->billing->phone,
                "street1" => $customerDetails->billing->street1,
                "city" => $customerDetails->billing->city,
                "state" => $customerDetails->billing->state,
                "country" => $customerDetails->billing->country,
                "zip" => $customerDetails->billing->zip,
            ],
            "return" => env('NGROK_URL') . "/payment/return",
            "callback" => env('NGROK_URL') . "/payment/callback",
            "ip" => request()->ip,
        ];


        // API Request 
        $response = Http::withHeaders([
            'Authorization' => $serverKey,
            'Content-Type' => 'application/json',
        ])->post('https://secure-egypt.paytabs.com/payment/request', $payload);


        $data = $response->json();

        return redirect($response['redirect_url']);
    }

    public function handleCallback(Request $request)
    {
        file_put_contents(storage_path('logs/paytabs-callback.log'), json_encode($request->all(), JSON_PRETTY_PRINT));
        try {
            return DB::transaction(function () use ($request) {
                // $tranRef = $request->input('tranRef') ?? $request->input('tran_ref');
                // $transaction = paypage::queryTransaction($tranRef);

                $order = Order::where('id', $request['cart_id'])->first();
                if (!$order) {
                    Log::warning('Order not found with order id : ' . $request['cart_id']);
                    // throw new \Exception('Order not found');
                }

                if ($request['payment_result']['response_status'] == "E") {
                    Log::warning('Error with payment maybe declined:');
                    throw new \Exception('payment failed try again with different card');
                }

                // Update Order Status => paid 
                $order->status = 'paid';
                $order->save();
                // Store or update the Payment details in payments table in DB:
                $payment = Payment::where('tran_ref', $request['tran_ref'])->first();
                if (!$payment) {
                    $payment = new Payment();
                    $payment->tran_ref = $request['tran_ref'];
                }
                $payment->order_id = $order ? $order->id : null;
                $payment->customer_name = $request['customer_details']['name'] ?? null;
                $payment->tran_type = $request['tran_type'] ?? null;
                $payment->amount = $request['cart_amount'] ?? null;
                $payment->currency = $request['cart_currency'] ?? null;
                $payment->payment_method = $request['payment_info']['card_scheme'] ?? null;
                $payment->status = $request['payment_result']['response_status'] ?? null;
                $payment->save();

                return response()->json(['status' => 'received'], 201);
            });
        } catch (\Exception $e) {
            Log::error('Payment callback failed', ['error' => $e->getMessage()]);
            throw new \Exception('Order not found, cannot update or store transaction ' . $e->getMessage());
        }
    }

    public function refundOrder(int $id)
    {
        $order = Order::find($id);
        $payment = Payment::where('order_id', $id)->first();
        $paymentTranTypes = $order->payments->pluck('tran_type')->toArray();
        $customerDetails = json_decode($order->customer_details);

        // order or payment Not Found
        if (!$order || !$payment) {
            return [
                'success' => false,
                'error' => 'Order or Payment not found',
                'status' => 404
            ];
        }

        // if this order already refunded 
        if (in_array("Refund", $paymentTranTypes)) {
            return [
                "success" => false,
                "error" => "This Order already fully refunded before",
                "status" => 404
            ];
        }


        // Creedinitials 
        $serverKey = config('paytabs.server_key');
        $profileId = config('paytabs.profile_id');

        if (!$serverKey || !$profileId) {
            return [
                'success' => false,
                'error' => 'Server key or Profile ID is not configured properly.',
                'status' => 500
            ];
        }


        $payload = [
            "profile_id" => $profileId,
            "tran_type" => "refund",
            "tran_class" => "ecom",
            "cart_id" => (string)$id, // Field cart_id must be type string
            "cart_currency" => $order->currency,
            "cart_amount" => $order->total_amount,
            "cart_description" => "Refund for order #{$id}",
            "tran_ref" => $payment->tran_ref,
            "customer_details" => [
                "name" => $customerDetails->billing->name ?? null,
                "email" => $customerDetails->billing->email ?? null,
                "phone" => $customerDetails->billing->phone ?? null,
                "street1" => $customerDetails->billing->street1 ?? null,
                "city" => $customerDetails->billing->city ?? null,
                "state" => $customerDetails->billing->state ?? null,
                "country" => $customerDetails->billing->country ?? null,
                "zip" => $customerDetails->billing->zip ?? null,
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => $serverKey,
            'Content-Type' => 'application/json',
        ])->post('https://secure-egypt.paytabs.com/payment/request', $payload);

        $data = $response->json(); // associative array 

        if (isset($data['payment_result']['response_status']) && $data['payment_result']['response_status'] === 'A') {
            // Create a new Payment record for the refund
            Payment::create([
                'order_id' => $order->id,
                'tran_ref' => $data['tran_ref'] ?? $payment->tran_ref,
                'customer_name' => $payment->customer_name ?? null,
                'tran_type' => 'Refund',
                'amount' => $order->total_amount,
                'currency' => $order->currency,
                'payment_method' => $payment->payment_method ?? null,
                'status' => $data['payment_result']['response_status'],
            ]);

            return [
                'success' => true,
                'message' => 'Refund successful',
                'data' => $data
            ];
        } else {
            $errorMessage = $data['payment_result']['response_message'] ?? 'Refund failed';
            $responseCode = $data['payment_result']['response_code'] ?? null;
            $errorDetails = match ($responseCode) {
                '320' => 'Unable to refund - Transaction may not be eligible for refund (too old, already refunded, or card issuer declined)',
                '321' => 'Refund amount exceeds original transaction amount',
                '322' => 'Transaction not found or invalid transaction reference',
                '323' => 'Refund not allowed for this transaction type',
                default => $errorMessage . " this",
            };

            return [
                'success' => false,
                'message' => 'Refund failed',
                'error' => $errorDetails,
                'response_code' => $responseCode,
                'data' => $data,
                'status' => 400
            ];
        }
    }
}
