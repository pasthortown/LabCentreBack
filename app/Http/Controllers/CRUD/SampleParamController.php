<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
Use Exception;
use App\Models\SampleParam;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class SampleParamController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(SampleParam::all(),200);
       } else {
          return response()->json(SampleParam::where('id',intval($id))->first(),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = SampleParam::count();
       $result = SampleParam::offset($offset)->limit(intval($size))->get();
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
          $lastSampleParam = SampleParam::orderBy('id', 'desc')->first();
          if($lastSampleParam) {
             $id = $lastSampleParam->id + 1;
          } else {
             $id = 1;
          }
          $sampleparam = SampleParam::create([
             'id' => intval($id),
             'sample_id' => intval($result['sample_id']),
             'description' => $result['description'],
             'value_text' => $result['value_text'],
             'value_double' => $result['value_double'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($sampleparam,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $sampleparam = SampleParam::where('id',intval($result['id']))->first();
          $sampleparam->sample_id = intval($result['sample_id']);
          $sampleparam->description = $result['description'];
          $sampleparam->value_text = $result['value_text'];
          $sampleparam->value_double = $result['value_double'];
          $sampleparam->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($sampleparam,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $sampleparam = SampleParam::where('id',intval($id))->first();
       return response()->json($sampleparam->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = SampleParam::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = SampleParam::where('id',intval($result['id']))->first();
         if ($exist) {
          $sampleparam = SampleParam::where('id',intval($result['id']))->first();
          $sampleparam->sample_id = intval($result['sample_id']);
          $sampleparam->description = $result['description'];
          $sampleparam->value_text = $result['value_text'];
          $sampleparam->value_double = $result['value_double'];
          $sampleparam->save();
         } else {
          $sampleparam = SampleParam::create([
             'id' => intval($result['id']),
             'sample_id' => intval($result['sample_id']),
             'description' => $result['description'],
             'value_text' => $result['value_text'],
             'value_double' => $result['value_double'],
          ]);
         }
       }
    }
}
