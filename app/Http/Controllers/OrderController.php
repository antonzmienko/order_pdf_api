<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;

class OrderController extends Controller
{
   public function handle(OrderRequest $request){
        return $request->validated();
   }
}
