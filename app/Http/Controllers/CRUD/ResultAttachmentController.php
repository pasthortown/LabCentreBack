<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
Use Exception;
use App\Models\ResultAttachment;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ResultAttachmentController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(ResultAttachment::all(),200);
       } else {
          return response()->json(ResultAttachment::where('id',intval($id))->first(),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = ResultAttachment::count();
       $result = ResultAttachment::offset($offset)->limit(intval($size))->get();
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
          $lastResultAttachment = ResultAttachment::orderBy('id', 'desc')->first();
          if($lastResultAttachment) {
             $id = $lastResultAttachment->id + 1;
          } else {
             $id = 1;
          }
          $resultattachment = ResultAttachment::create([
             'id' => intval($id),
             'result_id' => intval($result['result_id']),
             'result_attachment_file_type' => $result['result_attachment_file_type'],
             'result_attachment_file_name' => $result['result_attachment_file_name'],
             'result_attachment_file' => $result['result_attachment_file'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($resultattachment,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $resultattachment = ResultAttachment::where('id',intval($result['id']))->first();
          $resultattachment->result_id = intval($result['result_id']);
          $resultattachment->result_attachment_file_type = $result['result_attachment_file_type'];
          $resultattachment->result_attachment_file_name = $result['result_attachment_file_name'];
          $resultattachment->result_attachment_file = $result['result_attachment_file'];
          $resultattachment->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($resultattachment,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $resultattachment = ResultAttachment::where('id',intval($id))->first();
       return response()->json($resultattachment->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = ResultAttachment::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = ResultAttachment::where('id',intval($result['id']))->first();
         if ($exist) {
          $resultattachment = ResultAttachment::where('id',intval($result['id']))->first();
          $resultattachment->result_id = intval($result['result_id']);
          $resultattachment->result_attachment_file_type = $result['result_attachment_file_type'];
          $resultattachment->result_attachment_file_name = $result['result_attachment_file_name'];
          $resultattachment->result_attachment_file = $result['result_attachment_file'];
          $resultattachment->save();
         } else {
          $resultattachment = ResultAttachment::create([
             'id' => intval($result['id']),
             'result_id' => intval($result['result_id']),
             'result_attachment_file_type' => $result['result_attachment_file_type'],
             'result_attachment_file_name' => $result['result_attachment_file_name'],
             'result_attachment_file' => $result['result_attachment_file'],
          ]);
         }
       }
    }
}
