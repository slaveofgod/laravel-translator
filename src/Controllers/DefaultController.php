<?php

namespace Translator\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class DefaultController extends BaseController
{
    public function index(Request $request)
    {
        
        return view('Translator::Default/Index', array());
    }
}