<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
Use Exception;
use App\Models\LaboratoryAuthUser;
use App\Models\Laboratory;
use App\Models\Profile\User;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class LaboratoryAuthUserController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(LaboratoryAuthUser::all(),200);
       } else {
          return response()->json(LaboratoryAuthUser::where('id',intval($id))->first(),200);
       }
    }

    function get_filtered(Request $data)
    {
       $filter = $data['filter'];
       $users = User::where('name', 'like','%'.$filter.'%')->orwhere('email', 'like','%'.$filter.'%')->get();
       $laboratories = Laboratory::where('description', 'like','%'.$filter.'%')->orwhere('ruc', 'like','%'.$filter.'%')->get();
       $users_id = [];
       foreach($users as $user) {
           array_push($users_id, intval($user['id']));
       }
       $laboratories_id = [];
       foreach($laboratories as $laboratory) {
           array_push($laboratories_id, intval($laboratory['id']));
       }
       return response()->json(LaboratoryAuthUser::wherein('user_id', $users_id)->orwherein('laboratory_id', $laboratories_id)->get(),200);
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = LaboratoryAuthUser::count();
       $result = LaboratoryAuthUser::offset($offset)->limit(intval($size))->get();
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
          $lastLaboratoryAuthUser = LaboratoryAuthUser::orderBy('id', 'desc')->first();
          if($lastLaboratoryAuthUser) {
             $id = $lastLaboratoryAuthUser->id + 1;
          } else {
             $id = 1;
          }
          $laboratoryauthuser = LaboratoryAuthUser::create([
             'id' => intval($id),
             'laboratory_id' => intval($result['laboratory_id']),
             'user_id' => intval($result['user_id']),
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($laboratoryauthuser,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $laboratoryauthuser = LaboratoryAuthUser::where('id',intval($result['id']))->first();
          $laboratoryauthuser->laboratory_id = intval($result['laboratory_id']);
          $laboratoryauthuser->user_id = intval($result['user_id']);
          $laboratoryauthuser->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($laboratoryauthuser,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $laboratoryauthuser = LaboratoryAuthUser::where('id',intval($id))->first();
       return response()->json($laboratoryauthuser->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = LaboratoryAuthUser::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = LaboratoryAuthUser::where('id',intval($result['id']))->first();
         if ($exist) {
          $laboratoryauthuser = LaboratoryAuthUser::where('id',intval($result['id']))->first();
          $laboratoryauthuser->laboratory_id = intval($result['laboratory_id']);
          $laboratoryauthuser->user_id = intval($result['user_id']);
          $laboratoryauthuser->save();
         } else {
          $laboratoryauthuser = LaboratoryAuthUser::create([
             'id' => intval($result['id']),
             'laboratory_id' => intval($result['laboratory_id']),
             'user_id' => intval($result['user_id']),
          ]);
         }
       }
    }
}
