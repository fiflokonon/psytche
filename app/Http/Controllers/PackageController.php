<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('type_package')->get();
        return view('dashboard.packagelist', [
            'packages' => $packages
        ]);
    }
}
