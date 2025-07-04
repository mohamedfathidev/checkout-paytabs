<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Facades\URL;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function handleReturnUrl(Request $request)
    { 
        $message = $request['respMessage'] ?? 'Unknown status';

        if ($request['respStatus'] === 'A') {
            // Success
            $signedSuccessUrl = URL::signedRoute('payment.success', ['id' => $request['cartId']]);
            return redirect($signedSuccessUrl)->with('message', $message);

        } else {
            // Failure
            $signedFailureUrl = URL::signedRoute('payment.failure');
            return redirect($signedFailureUrl)->with('message', $message);
        }

    }


    public function paymentCallback(Request $request)
    {
        $this->paymentService->handleCallback($request);
    }

    public function paymentSuccess(Request $request, ?int $id = null)
    {

        return view('payment-success', compact('id'));
    }

    public function paymentFailure()
    {
        return view('payment-failure');
    }

    public function fullRefund(int $id)
    {
        $result = $this->paymentService->refundOrder($id);
            
        if ($result['success']) {
            $signedSuccessUrl = URL::signedRoute('payment.success');
            return redirect($signedSuccessUrl)->with('message', $result['message']);
        } else {
            $signedFailureUrl = URL::signedRoute('payment.failure');
            return redirect($signedFailureUrl)->with('message', $result['error'] ?? 'Refund failed');
        }
    }
}
