<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
Use Exception;
use App\Models\Profile;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ProfileController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(Profile::all(),200);
       } else {
          return response()->json(Profile::where('id',intval($id))->first(),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = Profile::count();
       $result = Profile::offset($offset)->limit(intval($size))->get();
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
          $lastProfile = Profile::orderBy('id', 'desc')->first();
          if($lastProfile) {
             $id = $lastProfile->id + 1;
          } else {
             $id = 1;
          }
          $profile = Profile::create([
             'id' => intval($id),
             'description' => $result['description'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($profile,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $profile = Profile::where('id',intval($result['id']))->first();
          $profile->description = $result['description'];
          $profile->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($profile,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $profile = Profile::where('id',intval($id))->first();
       return response()->json($profile->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = Profile::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = Profile::where('id',intval($result['id']))->first();
         if ($exist) {
          $profile = Profile::where('id',intval($result['id']))->first();
          $profile->description = $result['description'];
          $profile->save();
         } else {
          $profile = Profile::create([
             'id' => intval($result['id']),
             'description' => $result['description'],
          ]);
         }
       }
    }
}
