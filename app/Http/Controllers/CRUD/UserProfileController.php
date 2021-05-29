<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
Use Exception;
use App\Models\UserProfile;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Profile\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class UserProfileController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(UserProfile::all(),200);
       } else {
          return response()->json(UserProfile::where('id',intval($id))->first(),200);
       }
    }

    function get_filtered(Request $data)
    {
       $filter = $data['filter'];
       $users = User::where('name', 'like','%'.$filter.'%')->orwhere('email', 'like','%'.$filter.'%')->get();
       $profiles = Profile::where('description', 'like','%'.$filter.'%')->get();
       $users_id = [];
       foreach($users as $user) {
           array_push($users_id, intval($user['id']));
       }
       $profiles_id = [];
       foreach($profiles as $profile) {
           array_push($profiles_id, intval($profile['id']));
       }
       return response()->json(UserProfile::wherein('user_id', $users_id)->orwherein('profile_id', $profiles_id)->get(),200);
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = UserProfile::count();
       $result = UserProfile::offset($offset)->limit(intval($size))->get();
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
          $lastUserProfile = UserProfile::orderBy('id', 'desc')->first();
          if($lastUserProfile) {
             $id = $lastUserProfile->id + 1;
          } else {
             $id = 1;
          }
          $userprofile = UserProfile::create([
             'id' => $id,
             'user_id' => intval($result['user_id']),
             'profile_id' => intval($result['profile_id']),
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($userprofile,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $userprofile = UserProfile::where('id',intval($result['id']))->first();
          $userprofile->user_id = intval($result['user_id']);
          $userprofile->profile_id = intval($result['profile_id']);
          $userprofile->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($userprofile,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $userprofile = UserProfile::where('id',intval($id))->first();
       return response()->json($userprofile->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = UserProfile::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = UserProfile::where('id',intval($result['id']))->first();
         if ($exist) {
          $userprofile = UserProfile::where('id',intval($result['id']))->first();
          $userprofile->user_id = intval($result['user_id']);
          $userprofile->profile_id = intval($result['profile_id']);
          $userprofile->save();
         } else {
          $userprofile = UserProfile::create([
             'id' => intval($result['id']),
             'user_id' => intval($result['user_id']),
             'profile_id' => intval($result['profile_id']),
          ]);
         }
       }
    }
}
