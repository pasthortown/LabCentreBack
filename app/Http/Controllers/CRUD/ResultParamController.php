<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
Use Exception;
use App\Models\ResultParam;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ResultParamController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(ResultParam::all(),200);
       } else {
          return response()->json(ResultParam::where('id',intval($id))->first(),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = ResultParam::count();
       $result = ResultParam::offset($offset)->limit(intval($size))->get();
       $toReturn = new LengthAwarePaginator($result, $total, $size, $currentPage, [
          'path' => Paginator::resolveCurrentPath(),
          'pageName' => 'page'
       ]);
       return response()->json($toReturn,200);
    }

    function post(Request $data)
    {
       try{
          $result = $data->json()->all();
          $lastResultParam = ResultParam::orderBy('id', 'desc')->first();
          if($lastResultParam) {
             $id = $lastResultParam->id + 1;
          } else {
             $id = 1;
          }
          $resultparam = ResultParam::create([
             'id' => intval($id),
             'description' => $result['description'],
             'value_text' => $result['value_text'],
             'value_double' => $result['value_double'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($resultparam,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $resultparam = ResultParam::where('id',intval($result['id']))->first();
          $resultparam->description = $result['description'];
          $resultparam->value_text = $result['value_text'];
          $resultparam->value_double = $result['value_double'];
          $resultparam->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($resultparam,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $resultparam = ResultParam::where('id',intval($id))->first();
       return response()->json($resultparam->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = ResultParam::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = ResultParam::where('id',intval($result['id']))->first();
         if ($exist) {
          $resultparam = ResultParam::where('id',intval($result['id']))->first();
          $resultparam->description = $result['description'];
          $resultparam->value_text = $result['value_text'];
          $resultparam->value_double = $result['value_double'];
          $resultparam->save();
         } else {
          $resultparam = ResultParam::create([
             'id' => intval($result['id']),
             'description' => $result['description'],
             'value_text' => $result['value_text'],
             'value_double' => $result['value_double'],
          ]);
         }
       }
    }
}
