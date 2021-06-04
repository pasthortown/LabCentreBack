<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
Use Exception;
use App\Models\Template;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class TemplateController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(Template::all(),200);
       } else {
          return response()->json(Template::where('id',intval($id))->first(),200);
       }
    }

    function get_by_sample_description(Request $data)
    {
       $sample_description = $data['sample_description'];
       $laboratory_id = $data['laboratory_id'];
       return response()->json(Template::where('laboratory_id',intval($laboratory_id))->where('sample_description',$sample_description)->get(),200);
    }

    function get_by_laboratory_id(Request $data)
    {
       $laboratory_id = $data['laboratory_id'];
       return response()->json(Template::where('laboratory_id',intval($laboratory_id))->get(),200);
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $laboratory_id = $data['laboratory_id'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = Template::count();
       $result = Template::offset($offset)->limit(intval($size))->where('laboratory_id',intval($laboratory_id))->get();
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
          $lastTemplate = Template::orderBy('id', 'desc')->first();
          if($lastTemplate) {
             $id = $lastTemplate->id + 1;
          } else {
             $id = 1;
          }
          $template = Template::create([
             'id' => $id,
             'body' => $result['body'],
             'orientation' => $result['orientation'],
             'title' => $result['title'],
             'laboratory_id' => intval($result['laboratory_id']),
             'sample_description' => $result['sample_description'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($template,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $template = Template::where('id',intval($result['id']))->first();
          $template->body = $result['body'];
          $template->orientation = $result['orientation'];
          $template->title = $result['title'];
          $template->laboratory_id = intval($result['laboratory_id']);
          $template->sample_description = $result['sample_description'];
          $template->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($template,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $template = Template::where('id',intval($id))->first();
       return response()->json($template->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = Template::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = Template::where('id',$result['id'])->first();
         if ($exist) {
          $template = Template::where('id',intval($result['id']))->first();
          $template->body = $result['body'];
          $template->orientation = $result['orientation'];
          $template->title = $result['title'];
          $template->sample_description = $result['sample_description'];
          $template->save();
         } else {
          $template = Template::create([
             'id' => $result['id'],
             'body' => $result['body'],
             'orientation' => $result['orientation'],
             'title' => $result['title'],
             'laboratory_id' => intval($result['laboratory_id']),
             'sample_description' => $result['sample_description'],
          ]);
         }
       }
    }
}
