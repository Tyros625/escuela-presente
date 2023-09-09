<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function index(Request $request, $type)
    {
        $params = $request->query->all();
        $query = http_build_query($params);

        switch ($type) {
            case 'success':
                return redirect(
                    "/#/payment/success?{$query}"
                );
                break;
            case 'pending':
                return redirect(
                    "/#/payment/pending?{$query}"
                );
                break;
            case 'failure':
                return redirect(
                    "/#/payment/failure?{$query}"
                );
                break;
        }
    }
}
