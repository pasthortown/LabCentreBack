<?php

namespace App\Http\Controllers\Negocio;

use App\Exports\DataExporter;
use Validator;
use Exception;
use App\Models\Laboratory;
use App\Models\LaboratoryAttachment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;

class ExporterController extends Controller
{

  function pdf_template(Request $data) {
    $request = $data->json()->all();
    $html_content = $request['html'];
    try {
      $qr = $request['qr'];
    } catch (Exception $e) {
      $qr = false;
    }
    try {
      $qr_content = json_encode($request['qr_content']);
    } catch (Exception $e) {
      $qr_content = '';
    }
    try {
      $params = json_decode(json_encode($request['params']),true);
    } catch (Exception $e) {
      $params = [];
    }
    if (!$params) {
      $params = [];
    }
    $laboratory = Laboratory::where('id', intval($params['laboratory_id']))->first();
    $title = $this->build_title($request['title'], $params);
    $pdf_content = $this->build_content($html_content, $params);
    $html = $this->LSstyle($pdf_content, $title, $qr, $qr_content, $laboratory);
    $orientation = $request['orientation'];
    $pdf = App::make('dompdf.wrapper');
    $pdf->setPaper('A4', $orientation);
    $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'courier']);
    $pdf->loadHTML($html);
    $bytes = $pdf->output();
    $toReturn = base64_encode($bytes);
    return response()->json($toReturn, 200);
  }

  protected function build_title($title, $params) {
    $toReturn = $title;
    foreach($params as $key => $value) {
      $toReturn = str_ireplace('##'.$key.'##', $value, $toReturn);
    }
    return $toReturn;
  }

  protected function qrcode($content) {
    return base64_encode(QrCode::format('png')
      ->size(150)->margin(0)->backgroundColor(255,255,255)->color(0, 0, 0)
      ->generate($content));
  }

  protected function build_content($content, $params) {
    $toReturn = $content;
    foreach($params as $key => $value) {
      $toReturn = str_ireplace('##'.$key.'##', $value, $toReturn);
   }
    return $toReturn;
  }

  protected function LSstyle($content, $title, $qr, $qr_content, $laboratory) {
    $html = '<html>';
    $html .= '   <head>';
    $html .= '      <style>';
    $html .= '         @page { margin: 0px 0px 0px 0px}';
    $html .= '         header { position: fixed; top: 0px; left: 0px; right: 0px; height: 300px; z-index: -1; }';
    $html .= '         footer { position: fixed; bottom: 0px; left: 0px; right: 0px; text-align: center; height: 175px; z-index: -1; }';
    $html .= '         p { word-spacing: 5px; width:100%; text-align:justify; }';
    $html .= '         pagina { page-break-after:always; z-index:1; }';
    $html .= '         pagina:last-child(page-break-after:never; z-index:1; }';
    $html .= '      </style>';
    $html .= '   </head>';
    $html .= '   <body>';
    if ($qr) {
      $html .= '      <img style="position:fixed; right:200px; top:150px;" src="data:image/png;base64,'.$this->qrcode($qr_content).'"/>';
    }
    $html .= '      <header>';
    $html .= '         <img style="position:fixed; height:auto; width:150px; left: 150px; top: 150px;" src="'.$this->get_logo_image($laboratory['id']).'"/>';
    $html .= '         <h3 style="position: fixed; left:0px; right:0px; top:100px; font-family: Arial, Helvetica, sans-serif; text-align:center;">'. $laboratory['description'].'</h3>';
    $html .= '         <h5 style="position: fixed; left:0px; right:0px; top:125px; font-family: Arial, Helvetica, sans-serif; text-align:center;">RUC: '.$laboratory['ruc'].'</h5>';
    $html .= '         <h4 style="position: fixed; left:300px; right:0px; top:160px; font-family: Arial, Helvetica, sans-serif; text-align:center;">'.$laboratory['responsable_name'].'</h4>';
    $html .= '         <h6 style="position: fixed; left:300px; right:0px; top:225px; font-family: Arial, Helvetica, sans-serif; text-align:center;">Dirección: '.$laboratory['address'].'</h6>';
    $html .= '         <h6 style="position: fixed; left:300px; right:0px; top:250px; font-family: Arial, Helvetica, sans-serif; text-align:center;">Teléfonos: '.$laboratory['main_contact_number'].' / '.$laboratory['secondary_contact_number'].'</h6>';
    $html .= '      </header>';
    $html .= '      <footer>';
    $html .= '      </footer>';
    $html .= '      <main>';
    $html .= $content;
    $html .= '      </main>';
    $html .= '   </body>';
    $html .= '</html>';
    return $html;
  }

  function excel_file(Request $data) {
    /*$request = $data->json()->all();
    $header = $request['header'];
    $body = $request['body'];
    $export = new DataExporter([
      $header, $body
    ]);
    $uniqueId = uniqid();
    Excel::store($export, $uniqueId.'.xlsx', 'local');
    return response()->json($uniqueId.'.xlsx',200);*/
  }

  function get_logo_image($laboratory_id) {
    $logo = LaboratoryAttachment::where('laboratory_id', $laboratory_id)->where('laboratory_attachment_description', 'Logo')->first();
    return 'data:' . $logo['laboratory_attachment_file_type'] . ';base64,'.$logo['laboratory_attachment_file'];
  }
}
