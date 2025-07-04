<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Services\ValidationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;


class CheckoutController extends Controller
{
    protected $validationService;
    protected $paymentService;

    public function __construct(ValidationService $validationService, PaymentService $paymentService)
    {
        $this->validationService = $validationService;
        $this->paymentService = $paymentService;
    }

    public function showCheckoutDetailsForm(int $id): View
    {
        return view('checkout-details', compact("id"));
    }

    public function checkout(Request $request, Order $order): RedirectResponse 
    {
        $request->validate($this->validationService->getValidationRules("billing"));

        $data = $request->all();

        if ($request->has('same_as_billing')) {

            // the shipping is the same as billing 
            $data['shipping'] = $data['billing'];
        } else {
            // if not validate the shipping fields 
            $request->validate($this->validationService->getValidationRules("shipping"));
        }

        $billing = $data['billing'];
        $shipping = $data['shipping'] ?? $billing;

        // store in text column
        $customerInfo = json_encode([
            'billing' => $billing,
            'shipping' => $shipping,
        ]);

        // Update the customer detaails 
        $order->fill([
            "customer_details" => $customerInfo,
        ])->save();


        return $this->paymentService->initiatePayment($order);
    }
}
