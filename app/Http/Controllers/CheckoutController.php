<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
	public function checkout()
	{
		$cart = Product::all();
		$stripe = new \Stripe\StripeClient(config("payments.stripe.secret"));

		$totalPrice = 0;
		$lineItems = [];
		foreach ($cart as $item) {
			
			$totalPrice += $item->price;
			$item->quantity = rand(1, 5);
			$lineItems[] = [
				'price_data' => [
					'currency' => 'usd',
					'product_data' => [
						'name' => $item->name,
						'images' => [ $item->image]
					],
					'unit_amount' => $item->price * 100,
				],
				'quantity' => $item->quantity,
			];
		}

		$checkoutSession = $stripe->checkout->sessions->create([
			'line_items' => $lineItems,
			'mode' => config("payments.stripe.mode"),
			'success_url' => route("checkout.success", [], true) . "?session_id={CHECKOUT_SESSION_ID}",
			'cancel_url' => route("checkout.cancel", [], true),
		]);

		$order = new Order();

		$order->status = OrderStatus::UNPAID;
		$order->total_price = $totalPrice;
		$order->session_id = $checkoutSession->id;

		$order->save();

		return redirect($checkoutSession->url);
	}

	public function success(Request $request) 
	{
        try {
            $sessionId = $request->session_id;
            $stripe = new \Stripe\StripeClient(config("payments.stripe.secret"));


            if (! $session = $stripe->checkout->sessions->retrieve($sessionId)) {
                throw new NotFoundHttpException();
            }

            $customer = $session->customer ? $stripe->customers->retrieve($session->customer) : null;
			
			if (! $order = Order::where("session_id", $sessionId)->first()) {
                throw new NotFoundHttpException();
			}

			if ($order->status ===  OrderStatus::UNPAID) {
				$order->status = OrderStatus::PAID;
				$order->save();
			}

            return view("checkout.success", compact('customer'));
        } catch (\Throwable $th) {
            Log::error($th);

            return redirect()
				->route("products.index")
				->withErrors('Something wrong, Contact Support Team');
        }
	}

	public function cancel() 
	{
		return view("checkout.cancel");
	}

	public function webhook() 
	{	
		// This is your Stripe CLI webhook secret for testing your endpoint locally.
		$endpoint_secret = config("payments.stripe.webhook_secret");

		$payload = @file_get_contents('php://input');
		$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
		$event = null;

		try {
			$event = \Stripe\Webhook::constructEvent(
				$payload, $sig_header, $endpoint_secret
			);


		} catch(\UnexpectedValueException $e) {
			// Invalid payload
			Log::error($e);
			
			return response('', 400);
		} catch(\Stripe\Exception\SignatureVerificationException $e) {
			// Invalid signature
            Log::error($e);
			
			return response('', 400);
		}

		// Handle the event
		switch ($event->type) {
			case 'checkout.session.completed':
				$checkoutSession = $event->data->object;
				$sessionId = $checkoutSession->id;
				
				$order = Order::where("session_id", $sessionId)->first();
				if ($order && $order->status ===  OrderStatus::UNPAID) {
					$order->status = OrderStatus::PAID;
					$order->save();
				}
	
				// ... handle other event types
			case 'payment_intent.succeeded':
				$paymentIntent = $event->data->object;
				// ... handle other event types
			default:
				echo 'Received unknown event type ' . $event->type;
		}

		return response('');
	}
}
