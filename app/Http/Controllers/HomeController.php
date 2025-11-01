<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.app.home');
    }

    public function cashflowDetail()
    {
        return view('pages.app.cashflows.detail');
    }
}