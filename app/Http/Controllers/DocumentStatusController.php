<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentStatus;

class DocumentStatusController extends Controller
{
    public function index(Request $request)
    {

        if($request->get('per_page') === 0) {
            $per_page = DocumentStatus::count();
        } else {
            $per_page = $request->get('per_page') ?? 20;
        }

        return DocumentStatus::paginate($per_page);
    }
}
