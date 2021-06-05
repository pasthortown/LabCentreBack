<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
Use Exception;
use Illuminate\Support\Facades\Mail;
use App\Models\ResultAttachment;
use App\Http\Controllers\Controller;
use App\Models\Laboratory;
use App\Models\Sample;
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

    function get_by_sample_id(Request $data) {
        $sample_id = $data['sample_id'];
        return response()->json(ResultAttachment::where('sample_id',intval($sample_id))->first(),200);
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

    function post_by_sample_id(Request $data)
    {
       try{
          $result = $data->json()->all();
          $resultattachment = ResultAttachment::where('sample_id',intval($result['sample_id']))->first();
          $resultattachment->result_attachment_file_type = $result['result_attachment_file_type'];
          $resultattachment->result_attachment_file_name = $result['result_attachment_file_name'];
          $resultattachment->result_attachment_file = $result['result_attachment_file'];
          $resultattachment->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($resultattachment,200);
    }

    function send(Request $data) {
        $result = $data->json()->all();
        $patient = $result['patient'];
        $sample = $result['sample'];
        $result_attachment_to_send = ResultAttachment::where('sample_id', intval($sample['id']))->first();
        $laboratory = Laboratory::where('id', intval($sample['laboratory_id']))->first();
        $this->send_mail($patient['email'],$patient['fullname'], 'Resultados de Laboratorio '.date("Y-m-d H:i:s"), $laboratory, $patient, $sample, env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'), $result_attachment_to_send);
        $sample = Sample::where('id',intval($sample['id']))->first();
        $sample->status = 'Entregado';
        $sample->save();
        return response()->json($sample,200);
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
             'id' => $id,
             'sample_id' => $result['sample_id'],
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
          $resultattachment->sample_id = $result['sample_id'];
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
         $exist = ResultAttachment::where('id',$result['id'])->first();
         if ($exist) {
          $resultattachment = ResultAttachment::where('id',intval($result['id']))->first();
          $resultattachment->sample_id = $result['sample_id'];
          $resultattachment->result_attachment_file_type = $result['result_attachment_file_type'];
          $resultattachment->result_attachment_file_name = $result['result_attachment_file_name'];
          $resultattachment->result_attachment_file = $result['result_attachment_file'];
          $resultattachment->save();
         } else {
          $resultattachment = ResultAttachment::create([
             'id' => $result['id'],
             'sample_id' => $result['sample_id'],
             'result_attachment_file_type' => $result['result_attachment_file_type'],
             'result_attachment_file_name' => $result['result_attachment_file_name'],
             'result_attachment_file' => $result['result_attachment_file'],
          ]);
         }
       }
    }

    protected function send_mail($to, $toAlias, $subject, $laboratory, $patient, $sample, $fromMail, $fromAlias, $attachment) {
        $data = ['name'=>$toAlias, 'laboratory'=>$laboratory, 'patient'=>$patient, 'sample'=>$sample, 'appName'=>env('MAIL_FROM_NAME')];
        Mail::send('resultados', $data, function($message) use ($to, $toAlias, $subject, $fromMail, $fromAlias, $attachment) {
          $message->to($to, $toAlias)->subject($subject);
          $message->from($fromMail,$fromAlias);
          $message->attachData(base64_decode($attachment['result_attachment_file']), $attachment['result_attachment_file_name'], ['mime' => 'application/pdf']);
        });
        return response()->json('Success!',200);
    }
}
