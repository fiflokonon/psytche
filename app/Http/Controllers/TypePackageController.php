<?php

namespace App\Http\Controllers;

use App\Models\TypePackage;
use Illuminate\Http\Request;

class TypePackageController extends Controller
{
    public function index()
    {
        $types = TypePackage::all();
        return view('dashboard.type_packagelist', [
           'types' => $types
        ]);
    }
}
