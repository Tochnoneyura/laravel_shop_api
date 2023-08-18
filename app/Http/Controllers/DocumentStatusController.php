<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document_status;

class DocumentStatusController extends Controller
{
    public function index(Request $request)
    {

        if($request->get('per_page') === 0) {
            $per_page = Order::count();
        } else {
            $per_page = $request->get('per_page') ?? 20;
        }

        return Document_status::paginate($per_page);
    }
}
