<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;

class DocumentsController extends BaseController
{
    public function index()
    {
        
        return view('customer/documents/index');
    }
}