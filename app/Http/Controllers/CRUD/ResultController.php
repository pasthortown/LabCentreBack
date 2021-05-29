<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
Use Exception;
use App\Models\Result;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ResultController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(Result::all(),200);
       } else {
          return response()->json(Result::where('id',intval($id))->first(),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = Result::count();
       $result = Result::offset($offset)->limit(intval($size))->get();
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
          $lastResult = Result::orderBy('id', 'desc')->first();
          if($lastResult) {
             $id = $lastResult->id + 1;
          } else {
             $id = 1;
          }
          $result = Result::create([
             'id' => intval($id),
             'sample_id' => intval($result['sample_id']),
             'description' => $result['description'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($result,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $result = Result::where('id',intval($result['id']))->first();
          $result->sample_id = intval($result['sample_id']);
          $result->description = $result['description'];
          $result->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($result,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $result = Result::where('id',intval($id))->first();
       return response()->json($result->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = Result::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = Result::where('id',intval($result['id']))->first();
         if ($exist) {
          $result = Result::where('id',intval($result['id']))->first();
          $result->sample_id = intval($result['sample_id']);
          $result->description = $result['description'];
          $result->save();
         } else {
          $result = Result::create([
             'id' => intval($result['id']),
             'sample_id' => intval($result['sample_id']),
             'description' => $result['description'],
          ]);
         }
       }
    }
}
