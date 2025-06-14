<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    public function index()
    {
        return response()->json(["success" => true]);
    }

    public function store(Request $request)
    {
        return response()->json(["success" => true]);
    }

    public function show($id)
    {
        return response()->json(["success" => true]);
    }

    public function update(Request $request, $id)
    {
        return response()->json(["success" => true]);
    }

    public function destroy($id)
    {
        return response()->json(["success" => true]);
    }
} 