<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
Use Exception;
use App\Models\LaboratoryAttachment;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class LaboratoryAttachmentController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(LaboratoryAttachment::all(),200);
       } else {
          return response()->json(LaboratoryAttachment::where('id',intval($id))->first(),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = LaboratoryAttachment::count();
       $result = LaboratoryAttachment::offset($offset)->limit(intval($size))->get();
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
          $lastLaboratoryAttachment = LaboratoryAttachment::orderBy('id', 'desc')->first();
          if($lastLaboratoryAttachment) {
             $id = $lastLaboratoryAttachment->id + 1;
          } else {
             $id = 1;
          }
          $laboratoryattachment = LaboratoryAttachment::create([
             'id' => intval($id),
             'laboratory_attachment_description' => $result['laboratory_attachment_description'],
             'laboratory_id' => intval($result['laboratory_id']),
             'laboratory_attachment_file_type' => $result['laboratory_attachment_file_type'],
             'laboratory_attachment_file_name' => $result['laboratory_attachment_file_name'],
             'laboratory_attachment_file' => $result['laboratory_attachment_file'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($laboratoryattachment,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $laboratoryattachment = LaboratoryAttachment::where('id',intval($result['id']))->first();
          $laboratoryattachment->laboratory_id = intval($result['laboratory_id']);
          $laboratoryattachment->laboratory_attachment_description = $result['laboratory_attachment_description'];
          $laboratoryattachment->laboratory_attachment_file_type = $result['laboratory_attachment_file_type'];
          $laboratoryattachment->laboratory_attachment_file_name = $result['laboratory_attachment_file_name'];
          $laboratoryattachment->laboratory_attachment_file = $result['laboratory_attachment_file'];
          $laboratoryattachment->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($laboratoryattachment,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $laboratoryattachment = LaboratoryAttachment::where('id',intval($id))->first();
       return response()->json($laboratoryattachment->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = LaboratoryAttachment::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = LaboratoryAttachment::where('id',intval($result['id']))->first();
         if ($exist) {
          $laboratoryattachment = LaboratoryAttachment::where('id',intval($result['id']))->first();
          $laboratoryattachment->laboratory_id = intval($result['laboratory_id']);
          $laboratoryattachment->laboratory_attachment_description = $result['laboratory_attachment_description'];
          $laboratoryattachment->laboratory_attachment_file_type = $result['laboratory_attachment_file_type'];
          $laboratoryattachment->laboratory_attachment_file_name = $result['laboratory_attachment_file_name'];
          $laboratoryattachment->laboratory_attachment_file = $result['laboratory_attachment_file'];
          $laboratoryattachment->save();
         } else {
          $laboratoryattachment = LaboratoryAttachment::create([
             'id' => intval($result['id']),
             'laboratory_id' => intval($result['laboratory_id']),
             'laboratory_attachment_description' => $result['laboratory_attachment_description'],
             'laboratory_attachment_file_type' => $result['laboratory_attachment_file_type'],
             'laboratory_attachment_file_name' => $result['laboratory_attachment_file_name'],
             'laboratory_attachment_file' => $result['laboratory_attachment_file'],
          ]);
         }
       }
    }
}
