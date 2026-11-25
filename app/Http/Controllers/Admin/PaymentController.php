<?php

namespace App\Http\Controllers\Admin;

class PaymentController
{
    public function index()
    {
        return view('admin.payments.index');
    }
}
