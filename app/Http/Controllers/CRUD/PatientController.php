<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
Use Exception;
use App\Models\Patient;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientController extends Controller
{
    function search(Request $data)
    {
       $filter = $data['filter'];
       $toReturn = Patient::where('fullname', 'like', '%'.$filter.'%')
                                ->orWhere('identification', 'like', '%'.$filter.'%')->get();
       return response()->json($toReturn,200);
    }

    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(Patient::all(),200);
       } else {
          return response()->json(Patient::where('id',intval($id))->first(),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = Patient::count();
       $result = Patient::offset($offset)->limit(intval($size))->get();
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
          $lastPatient = Patient::orderBy('id', 'desc')->first();
          if($lastPatient) {
             $id = $lastPatient->id + 1;
          } else {
             $id = 1;
          }
          $patient = Patient::create([
             'id' => intval($id),
             'identification' => $result['identification'],
             'fullname' => $result['fullname'],
             'born_date' => $result['born_date'],
             'gender' => $result['gender'],
             'email' => $result['email'],
             'contact_number' => $result['contact_number'],
             'address' => $result['address'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($patient,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $patient = Patient::where('id',intval($result['id']))->first();
          $patient->identification = $result['identification'];
          $patient->fullname = $result['fullname'];
          $patient->born_date = $result['born_date'];
          $patient->gender = $result['gender'];
          $patient->email = $result['email'];
          $patient->contact_number = $result['contact_number'];
          $patient->address = $result['address'];
          $patient->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($patient,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $patient = Patient::where('id',intval($id))->first();
       return response()->json($patient->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = Patient::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = Patient::where('id',intval($result['id']))->first();
         if ($exist) {
          $patient = Patient::where('id',intval($result['id']))->first();
          $patient->identification = $result['identification'];
          $patient->fullname = $result['fullname'];
          $patient->born_date = $result['born_date'];
          $patient->gender = $result['gender'];
          $patient->email = $result['email'];
          $patient->contact_number = $result['contact_number'];
          $patient->address = $result['address'];
          $patient->save();
         } else {
          $patient = Patient::create([
             'id' => intval($result['id']),
             'identification' => $result['identification'],
             'fullname' => $result['fullname'],
             'born_date' => $result['born_date'],
             'gender' => $result['gender'],
             'email' => $result['email'],
             'contact_number' => $result['contact_number'],
             'address' => $result['address'],
          ]);
         }
       }
    }
}
