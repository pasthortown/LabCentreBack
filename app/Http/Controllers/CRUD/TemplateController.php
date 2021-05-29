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

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = Template::count();
       $result = Template::offset($offset)->limit(intval($size))->get();
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
             'variables' => $result['variables'],
             'body' => $result['body'],
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
          $template->variables = $result['variables'];
          $template->body = $result['body'];
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
          $template->variables = $result['variables'];
          $template->body = $result['body'];
          $template->save();
         } else {
          $template = Template::create([
             'id' => $result['id'],
             'variables' => $result['variables'],
             'body' => $result['body'],
          ]);
         }
       }
    }
}