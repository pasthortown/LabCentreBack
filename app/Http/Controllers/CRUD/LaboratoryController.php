<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
Use Exception;
use App\Models\Laboratory;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class LaboratoryController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(Laboratory::all(),200);
       } else {
          return response()->json(Laboratory::where('id',intval($id))->first(),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = Laboratory::count();
       $result = Laboratory::offset($offset)->limit(intval($size))->get();
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
          $lastLaboratory = Laboratory::orderBy('id', 'desc')->first();
          if($lastLaboratory) {
             $id = $lastLaboratory->id + 1;
          } else {
             $id = 1;
          }
          $laboratory = Laboratory::create([
             'id' => intval($id),
             'ruc' => $result['ruc'],
             'register' => $result['register'],
             'responsable_name' => $result['responsable_name'],
             'main_contact_number' => $result['main_contact_number'],
             'secondary_contact_number' => $result['secondary_contact_number'],
             'description' => $result['description'],
             'address' => $result['address'],
             'geolocation' => $result['geolocation'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($laboratory,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $laboratory = Laboratory::where('id',intval($result['id']))->first();
          $laboratory->description = $result['description'];
          $laboratory->address = $result['address'];
          $laboratory->geolocation = $result['geolocation'];
          $laboratory->ruc = $result['ruc'];
          $laboratory->register = $result['register'];
          $laboratory->responsable_name = $result['responsable_name'];
          $laboratory->main_contact_number = $result['main_contact_number'];
          $laboratory->secondary_contact_number = $result['secondary_contact_number'];
          $laboratory->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($laboratory,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $laboratory = Laboratory::where('id',intval($id))->first();
       return response()->json($laboratory->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = Laboratory::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = Laboratory::where('id',intval($result['id']))->first();
         if ($exist) {
          $laboratory = Laboratory::where('id',intval($result['id']))->first();
          $laboratory->description = $result['description'];
          $laboratory->address = $result['address'];
          $laboratory->geolocation = $result['geolocation'];
          $laboratory->ruc = $result['ruc'];
          $laboratory->register = $result['register'];
          $laboratory->responsable_name = $result['responsable_name'];
          $laboratory->main_contact_number = $result['main_contact_number'];
          $laboratory->secondary_contact_number = $result['secondary_contact_number'];
          $laboratory->save();
         } else {
          $laboratory = Laboratory::create([
            'id' => intval($id),
            'ruc' => $result['ruc'],
            'register' => $result['register'],
            'responsable_name' => $result['responsable_name'],
            'main_contact_number' => $result['main_contact_number'],
            'secondary_contact_number' => $result['secondary_contact_number'],
            'description' => $result['description'],
            'address' => $result['address'],
            'geolocation' => $result['geolocation'],
          ]);
         }
       }
    }
}
