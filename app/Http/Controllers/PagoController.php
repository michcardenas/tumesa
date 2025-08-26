<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PagoController extends Controller
{
  public function reservar(Request $request)
    {
        // ✅ Si NO está logueado, redirigir al registro
        if (!Auth::check()) {
            return redirect()->route('register');
        }

        // ✅ Si está logueado, crear preferencia de pago en Mercado Pago
        // SDK MercadoPago
        \MercadoPago\MercadoPagoConfig::setAccessToken("TEST-7710589893885438-080817-3174e23bf25e4fa03ab7383053c5b49c-90445855");

        $preference = new \MercadoPago\Resources\Preference();

        $item = new \MercadoPago\Resources\Item();
        $item->title = 'Reserva de servicio';
        $item->quantity = 1;
        $item->unit_price = 50000; // 💲 Ajusta el precio aquí (COP)
        $item->currency_id = 'COP';

        $preference->items = [$item];

        // Redirección después del pago
        $preference->back_urls = [
            "success" => route('pago.exito'),
            "failure" => route('pago.error'),
            "pending" => route('pago.pendiente'),
        ];
        $preference->auto_return = "approved";

        $preference->save();

        // Redirige al usuario a Mercado Pago
        return redirect($preference->init_point);
    }
}
