<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

class PagoController extends Controller
{
   public function reservar(Request $request)
{
    if (!auth()->check()) {
        return redirect()->route('register');
    }

    try {
        \MercadoPago\MercadoPagoConfig::setAccessToken("TEST-7710589893885438-080817-3174e23bf25e4fa03ab7383053c5b49c-90445855");

        $client = new \MercadoPago\Client\Preference\PreferenceClient();

        $preference = $client->create([
            "items" => [
                [
                    "title" => "Reserva de servicio",
                    "quantity" => 1,
                    "unit_price" => 50000,
                    "currency_id" => "COP"
                ]
            ],
            "back_urls" => [
                "success" => route('pago.exito'),
                "failure" => route('pago.error'),
                "pending" => route('pago.pendiente')
            ],
            "auto_return" => "approved"
        ]);

        return redirect()->away($preference->init_point);
    } catch (\MercadoPago\Exceptions\MPApiException $e) {
    dd([
        'error' => true,
        'exception_class' => get_class($e),
        'message' => $e->getMessage(),
        'body' => method_exists($e, 'getApiResponse') ? $e->getApiResponse()->getContent() : null
    ]);
}

}

}
