<?php

namespace App\Http\Controllers;

use Validator;
use App\Http\Controllers\Controller;
use Exception;
use App\Models\Profile\User;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class AuthController extends Controller
{
  function passwordRecoveryRequest(Request $data) {
    $result = $data->json()->all();
    $email = $result['email'];
    $user = User::where('email', $email)->first();
    if(!$user){
      return response()->json('Ocurrió un error',400);
    }
    $enlace = env('APP_URL').'password_recovery/?r='.$user->api_token;
    $message = 'Para cambiar tu contraseña da click en el siguiente enlace: ' . $enlace;
    $subject = 'Solicitud de Cambio de Contraseña';
    return $this->send_mail($user->email, $user->name, $subject, $message, env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
  }

  function passwordRecovery(Request $data)
  {
    $token = $data['r'];
    $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
    try{
      $new_password = Str::random(10);
      $status = User::find($credentials->subject);
      $status->password = Crypt::encrypt($new_password);
      $status->save();
      if(!$status){
        return response()->json('Ocurrió un error',400);
      }
    } catch (Exception $e) {
      return response()->json('Ocurrió un error',400);
    }
    $message = 'Tu nueva contraseña es ' . $new_password;
    $subject = 'Recuperación de Contraseña';
    $user = User::where('id', $credentials->subject)->first();
    return $this->send_mail($user->email, $user->name, $subject, $message, env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
  }

  function passwordChange(Request $data)
  {
    $result = $data->json()->all();
    $token = $data->header('api_token');
    $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
    $id = $credentials->subject;
    $new_password = $result['new_password'];
    try{
      $user = User::where('id',$id)->first();
      $user->password = Crypt::encrypt($new_password);
      $user->save();
    } catch (Exception $e) {
      return response()->json([
        'error' => 'Bad Credentials'
      ], 400);
    }
    return response()->json('Password changed successfully',200);
  }

  function register(Request $data)
  {
    try{
      $new_password = Str::random(10);
      $result = $data->json()->all();
      $email = $result['email'];
      $lastUser = User::orderBy('id', 'desc')->first();
      if($lastUser) {
         $id = $lastUser->id + 1;
      } else {
         $id = 1;
      }
      $user = User::create([
          'id' => $id,
          'name' => $result['name'],
          'email' => $email,
          'password' => Crypt::encrypt($new_password),
          'api_token' => Str::random(64),
      ]);
      $message = 'Tu nueva contraseña es ' . $new_password;
      $subject = 'Te damos la bienvenida a ' . env('MAIL_FROM_NAME');
      return $this->send_mail($email, $user->name, $subject, $message, env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
    } catch (Exception $e) {
      return response()->json($e,400);
    }
    return response()->json($user,200);
  }

  function login(Request $data)
  {
      $result = $data->json()->all();
      $email = $result['email'];
      $password = $result['password'];
      $user = User::where('email', $email)->first();
      if (!$user) {
        return response()->json([
          'error' => 'Bad Credentials'
        ], 400);
      }
      if ($password === Crypt::decrypt($user->password)) {
        $token = $this->jwt($user);
        $user->api_token = $token;
        $user->save();
        return response()->json([
            'token' => $token,
            'name' => $user->name,
            'id' => $user->id
        ], 200);
      }
      return response()->json([
        'error' => 'Bad Credentials'
      ], 400);
  }

  protected function jwt(User $user) {
    $payload = [
        'subject' => $user->id,
        'creation_time' => time(),
        'expiration_time' => time() + 60*60
    ];
    return JWT::encode($payload, env('JWT_SECRET'));
  }

  protected function send_mail($to, $toAlias, $subject, $body, $fromMail,$fromAlias) {
    $data = ['name'=>$toAlias, 'body'=>$body, 'appName'=>env('MAIL_FROM_NAME')];
    Mail::send('mail', $data, function($message) use ($to, $toAlias, $subject, $fromMail,$fromAlias) {
      $message->to($to, $toAlias)->subject($subject);
      $message->from($fromMail,$fromAlias);
    });
    return response()->json('Success!',200);
  }
}
