<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
   return 'Web Wervice Realizado con LSCodeGenerator';
});

$router->group(['middleware' => []], function () use ($router) {
   $router->post('/login', ['uses' => 'AuthController@login']);
   $router->post('/register', ['uses' => 'AuthController@register']);
   $router->post('/password_recovery_request', ['uses' => 'AuthController@passwordRecoveryRequest']);
   $router->get('/password_recovery', ['uses' => 'AuthController@passwordRecovery']);
});

$router->group(['middleware' => ['auth']], function () use ($router) {
   $router->post('/user/password_change', ['uses' => 'AuthController@passwordChange']);
   $router->get('/show_data', ['uses' => 'AuthController@showData']);

   //LSLabCenter

   //CRUD ProfilePicture
   $router->post('/profilepicture', ['uses' => 'Profile\ProfilePictureController@post']);
   $router->get('/profilepicture', ['uses' => 'Profile\ProfilePictureController@get']);
   $router->get('/profilepicture/paginate', ['uses' => 'Profile\ProfilePictureController@paginate']);
   $router->put('/profilepicture', ['uses' => 'Profile\ProfilePictureController@put']);
   $router->delete('/profilepicture', ['uses' => 'Profile\ProfilePictureController@delete']);

   //CRUD User
   $router->post('/user', ['uses' => 'Profile\UserController@post']);
   $router->get('/user', ['uses' => 'Profile\UserController@get']);
   $router->get('/user/paginate', ['uses' => 'Profile\UserController@paginate']);
   $router->put('/user', ['uses' => 'Profile\UserController@put']);
   $router->delete('/user', ['uses' => 'Profile\UserController@delete']);

   //CRUD Patient
   $router->get('/patient/search', ['uses' => 'CRUD\PatientController@search']);
   $router->post('/patient', ['uses' => 'CRUD\PatientController@post']);
   $router->get('/patient', ['uses' => 'CRUD\PatientController@get']);
   $router->get('/patient/paginate', ['uses' => 'CRUD\PatientController@paginate']);
   $router->get('/patient/backup', ['uses' => 'CRUD\PatientController@backup']);
   $router->put('/patient', ['uses' => 'CRUD\PatientController@put']);
   $router->delete('/patient', ['uses' => 'CRUD\PatientController@delete']);
   $router->post('/patient/masive_load', ['uses' => 'CRUD\PatientController@masiveLoad']);

   //CRUD Sample
   $router->post('/sample', ['uses' => 'CRUD\SampleController@post']);
   $router->get('/sample', ['uses' => 'CRUD\SampleController@get']);
   $router->get('/sample/paginate', ['uses' => 'CRUD\SampleController@paginate']);
   $router->get('/sample/backup', ['uses' => 'CRUD\SampleController@backup']);
   $router->put('/sample', ['uses' => 'CRUD\SampleController@put']);
   $router->delete('/sample', ['uses' => 'CRUD\SampleController@delete']);
   $router->post('/sample/masive_load', ['uses' => 'CRUD\SampleController@masiveLoad']);

   //CRUD SampleParam
   $router->post('/sampleparam', ['uses' => 'CRUD\SampleParamController@post']);
   $router->get('/sampleparam', ['uses' => 'CRUD\SampleParamController@get']);
   $router->get('/sampleparam/paginate', ['uses' => 'CRUD\SampleParamController@paginate']);
   $router->get('/sampleparam/backup', ['uses' => 'CRUD\SampleParamController@backup']);
   $router->put('/sampleparam', ['uses' => 'CRUD\SampleParamController@put']);
   $router->delete('/sampleparam', ['uses' => 'CRUD\SampleParamController@delete']);
   $router->post('/sampleparam/masive_load', ['uses' => 'CRUD\SampleParamController@masiveLoad']);

   //CRUD Result
   $router->post('/result', ['uses' => 'CRUD\ResultController@post']);
   $router->get('/result', ['uses' => 'CRUD\ResultController@get']);
   $router->get('/result/paginate', ['uses' => 'CRUD\ResultController@paginate']);
   $router->get('/result/backup', ['uses' => 'CRUD\ResultController@backup']);
   $router->put('/result', ['uses' => 'CRUD\ResultController@put']);
   $router->delete('/result', ['uses' => 'CRUD\ResultController@delete']);
   $router->post('/result/masive_load', ['uses' => 'CRUD\ResultController@masiveLoad']);

   //CRUD ResultAttachment
   $router->post('/resultattachment', ['uses' => 'CRUD\ResultAttachmentController@post']);
   $router->get('/resultattachment', ['uses' => 'CRUD\ResultAttachmentController@get']);
   $router->get('/resultattachment/paginate', ['uses' => 'CRUD\ResultAttachmentController@paginate']);
   $router->get('/resultattachment/backup', ['uses' => 'CRUD\ResultAttachmentController@backup']);
   $router->put('/resultattachment', ['uses' => 'CRUD\ResultAttachmentController@put']);
   $router->delete('/resultattachment', ['uses' => 'CRUD\ResultAttachmentController@delete']);
   $router->post('/resultattachment/masive_load', ['uses' => 'CRUD\ResultAttachmentController@masiveLoad']);

   //CRUD Laboratory
   $router->post('/laboratory', ['uses' => 'CRUD\LaboratoryController@post']);
   $router->get('/laboratory', ['uses' => 'CRUD\LaboratoryController@get']);
   $router->get('/laboratory/paginate', ['uses' => 'CRUD\LaboratoryController@paginate']);
   $router->get('/laboratory/backup', ['uses' => 'CRUD\LaboratoryController@backup']);
   $router->put('/laboratory', ['uses' => 'CRUD\LaboratoryController@put']);
   $router->delete('/laboratory', ['uses' => 'CRUD\LaboratoryController@delete']);
   $router->post('/laboratory/masive_load', ['uses' => 'CRUD\LaboratoryController@masiveLoad']);

   //CRUD LaboratoryAttachment
   $router->post('/laboratoryattachment', ['uses' => 'CRUD\LaboratoryAttachmentController@post']);
   $router->get('/laboratoryattachment', ['uses' => 'CRUD\LaboratoryAttachmentController@get']);
   $router->get('/laboratoryattachment/paginate', ['uses' => 'CRUD\LaboratoryAttachmentController@paginate']);
   $router->get('/laboratoryattachment/backup', ['uses' => 'CRUD\LaboratoryAttachmentController@backup']);
   $router->put('/laboratoryattachment', ['uses' => 'CRUD\LaboratoryAttachmentController@put']);
   $router->delete('/laboratoryattachment', ['uses' => 'CRUD\LaboratoryAttachmentController@delete']);
   $router->post('/laboratoryattachment/masive_load', ['uses' => 'CRUD\LaboratoryAttachmentController@masiveLoad']);

   //CRUD LaboratoryAuthUser
   $router->get('/laboratoryauthuser/filtered', ['uses' => 'CRUD\LaboratoryAuthUserController@get_filtered']);
   $router->post('/laboratoryauthuser', ['uses' => 'CRUD\LaboratoryAuthUserController@post']);
   $router->get('/laboratoryauthuser', ['uses' => 'CRUD\LaboratoryAuthUserController@get']);
   $router->get('/laboratoryauthuser/paginate', ['uses' => 'CRUD\LaboratoryAuthUserController@paginate']);
   $router->get('/laboratoryauthuser/backup', ['uses' => 'CRUD\LaboratoryAuthUserController@backup']);
   $router->put('/laboratoryauthuser', ['uses' => 'CRUD\LaboratoryAuthUserController@put']);
   $router->delete('/laboratoryauthuser', ['uses' => 'CRUD\LaboratoryAuthUserController@delete']);
   $router->post('/laboratoryauthuser/masive_load', ['uses' => 'CRUD\LaboratoryAuthUserController@masiveLoad']);

   //CRUD UserProfile
   $router->get('/userprofile/filtered', ['uses' => 'CRUD\UserProfileController@get_filtered']);
   $router->post('/userprofile', ['uses' => 'CRUD\UserProfileController@post']);
   $router->get('/userprofile', ['uses' => 'CRUD\UserProfileController@get']);
   $router->get('/userprofile/paginate', ['uses' => 'CRUD\UserProfileController@paginate']);
   $router->get('/userprofile/backup', ['uses' => 'CRUD\UserProfileController@backup']);
   $router->put('/userprofile', ['uses' => 'CRUD\UserProfileController@put']);
   $router->delete('/userprofile', ['uses' => 'CRUD\UserProfileController@delete']);
   $router->post('/userprofile/masive_load', ['uses' => 'CRUD\UserProfileController@masiveLoad']);

   //CRUD Profile
   $router->post('/profile', ['uses' => 'CRUD\ProfileController@post']);
   $router->get('/profile', ['uses' => 'CRUD\ProfileController@get']);
   $router->get('/profile/paginate', ['uses' => 'CRUD\ProfileController@paginate']);
   $router->get('/profile/backup', ['uses' => 'CRUD\ProfileController@backup']);
   $router->put('/profile', ['uses' => 'CRUD\ProfileController@put']);
   $router->delete('/profile', ['uses' => 'CRUD\ProfileController@delete']);
   $router->post('/profile/masive_load', ['uses' => 'CRUD\ProfileController@masiveLoad']);

    //CRUD Template
    $router->get('/template/by_sample_description', ['uses' => 'CRUD\TemplateController@get_by_sample_description']);
    $router->get('/template/by_laboratory_id', ['uses' => 'CRUD\TemplateController@get_by_laboratory_id']);
    $router->post('/template', ['uses' => 'CRUD\TemplateController@post']);
    $router->get('/template', ['uses' => 'CRUD\TemplateController@get']);
    $router->get('/template/paginate', ['uses' => 'CRUD\TemplateController@paginate']);
    $router->get('/template/backup', ['uses' => 'CRUD\TemplateController@backup']);
    $router->put('/template', ['uses' => 'CRUD\TemplateController@put']);
    $router->delete('/template', ['uses' => 'CRUD\TemplateController@delete']);
    $router->post('/template/masive_load', ['uses' => 'CRUD\TemplateController@masiveLoad']);
    $router->post('/template/download', ['uses' => 'Negocio\ExporterController@pdf_template']);
});
