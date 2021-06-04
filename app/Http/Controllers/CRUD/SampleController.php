<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
Use Exception;
use App\Models\Sample;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class SampleController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(Sample::all(),200);
       } else {
          return response()->json(Sample::where('id',intval($id))->first(),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = Sample::count();
       $result = Sample::offset($offset)->limit(intval($size))->get();
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
          $lastSample = Sample::orderBy('id', 'desc')->first();
          if($lastSample) {
             $id = $lastSample->id + 1;
          } else {
             $id = 1;
          }
          $sample = Sample::create([
             'id' => intval($id),
             'patient_id' => intval($result['patient_id']),
             'description' => $result['description'],
             'acquisition_date' => $result['acquisition_date'],
             'status' => $result['status'],
             'laboratory_id' => intval($result['laboratory_id']),
             'sample_param' => $result['sample_param'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($sample,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $sample = Sample::where('id',intval($result['id']))->first();
          $sample->patient_id = intval($result['patient_id']);
          $sample->description = $result['description'];
          $sample->acquisition_date = $result['acquisition_date'];
          $sample->status = $result['status'];
          $sample->laboratory_id = intval($result['laboratory_id']);
          $sample->sample_param = $result['sample_param'];
          $sample->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($sample,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $sample = Sample::where('id',intval($id))->first();
       return response()->json($sample->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = Sample::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = Sample::where('id',intval($result['id']))->first();
         if ($exist) {
          $sample = Sample::where('id',intval($result['id']))->first();
          $sample->patient_id = intval($result['patient_id']);
          $sample->description = $result['description'];
          $sample->acquisition_date = $result['acquisition_date'];
          $sample->status = $result['status'];
          $sample->laboratory_id = intval($result['laboratory_id']);
          $sample->sample_param = $result['sample_param'];
          $sample->save();
         } else {
          $sample = Sample::create([
             'id' => intval($result['id']),
             'patient_id' => intval($result['patient_id']),
             'description' => $result['description'],
             'acquisition_date' => $result['acquisition_date'],
             'status' => $result['status'],
             'laboratory_id' => intval($result['laboratory_id']),
             'sample_param' => $result['sample_param'],
          ]);
         }
       }
    }
}
