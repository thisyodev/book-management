<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class BookWebController extends Controller
{
    public function index()
    {
        return view('books.index');
    }
}
