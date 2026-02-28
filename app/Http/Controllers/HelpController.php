<?php

namespace App\Http\Controllers;

use App\Models\Condominio;

class HelpController extends Controller
{
    public function index()
    {
        return view('ajuda.index');
    }

    public function context(Condominio $condominio)
    {
        $this->authorize('view', $condominio);

        return view('ajuda.index', compact('condominio'));
    }
}

