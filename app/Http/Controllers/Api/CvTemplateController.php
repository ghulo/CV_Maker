<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class CvTemplateController extends Controller
{
    public function index() { return response()->json(['success' => true, 'templates' => []]); }
    public function show($id) { return response()->json(['success' => true, 'template' => ['id' => $id]]); }
}
