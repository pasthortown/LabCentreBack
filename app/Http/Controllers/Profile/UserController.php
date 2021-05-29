<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\Request;
Use Exception;
use App\Http\Controllers\Controller;
use App\Models\Profile\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(User::all(),200);
       } else {
          return response()->json(User::where('id',intval($id))->first(),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = User::count();
       $result = User::offset($offset)->limit(intval($size))->get();
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
          $lastUser = User::orderBy('id', 'desc')->first();
          if($lastUser) {
             $id = $lastUser->id + 1;
          } else {
             $id = 1;
          }
          $user = User::create([
             'id' => $id,
             'name'=>$result['name'],
             'email'=>$result['email'],
             'password'=>Crypt::encrypt(Str::random(32)),
             'api_token'=>Str::random(32),
          ]);
          return response()->json($user,200);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $user = User::where('id',intval($result['id']))->first();
          $user->name = $result['name'];
          $user->email = $result['email'];
          $user->save();
          return response()->json($user,200);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
    }

    function delete(Request $data)
    {
       $result = $data->json()->all();
       $id = $result['id'];
       $user = User::where('id',intval($id))->first();
       return response()->json($user->delete(),200);
    }
}
