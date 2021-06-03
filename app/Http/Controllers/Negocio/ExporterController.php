<?php

namespace App\Http\Controllers\Negocio;

use App\Exports\DataExporter;
use Validator;
use Exception;
use App\Models\Template;
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
    $template = Template::where('id', $request['template_id'])->first();
    $html_content = $template['body'];
    try {
      $qr = $request['qr'];
    } catch (Exception $e) {
      $qr = false;
    }
    try {
      $qr_content = $request['qr_content'];
    } catch (Exception $e) {
      $qr_content = '';
    }
    try {
      $params = $request['params'];
    } catch (Exception $e) {
      $params = [];
    }
    if (!$params) {
      $params = [];
    }
    $title = $this->build_title($template['title'], $params);
    $pdf_content = $this->build_content($html_content, $params, $qr_content);
    if ($qr){
      $pdf_content = str_ireplace('##qr_doc_code##', $this->qrcode($request['qr_content']), $pdf_content);
    }
    $html = $this->mintur_style($pdf_content, $title, $qr, $qr_content);
    $orientation = $template['orientation'];
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
    foreach ($params as $param) {
      foreach($param as $key => $value) {
         $toReturn = str_ireplace('##'.$key.'##', $value, $toReturn);
      }
    }
    return $toReturn;
  }

  protected function qrcode($content) {
    return base64_encode(QrCode::format('png')
      ->size(140)->margin(0)->backgroundColor(255,255,255)->color(0, 0, 0)
      ->generate($content));
  }

  function pdf_file(Request $data) {
    $request = $data->json()->all();
    $html_content = $request['html'];
    try {
      $qr = $request['qr'];
    } catch (Exception $e) {
      $qr = false;
    }
    try {
      $qr_content = $request['qr_content'];
    } catch (Exception $e) {
      $qr_content = '';
    }
    try {
      $params = $request['params'];
    } catch (Exception $e) {
      $params = [];
    }
    if (!$params) {
      $params = [];
    }
    $title = $request['title'];
    $pdf_content = $this->build_content($html_content, $params, $qr_content);
    $html = $this->mintur_style($pdf_content, $title, $qr, $qr_content);
    $orientation = $request['orientation'];
    $pdf = App::make('dompdf.wrapper');
    $pdf->setPaper('A4', $orientation);
    $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'courier']);
    $pdf->loadHTML($html);
    $bytes = $pdf->output();
    $toReturn = base64_encode($bytes);
    return response()->json($toReturn, 200);
  }

  protected function build_content($content, $params, $qr_content) {
    $toReturn = $content;
    foreach ($params as $param) {
      foreach($param as $key => $value) {
         $toReturn = str_ireplace('##'.$key.'##', $value, $toReturn);
      }
    }
    $toReturn = str_ireplace('**main_qr_code**', 'data:image/png;base64,'. $this->qrcode($qr_content), $toReturn);
    return $toReturn;
  }

  protected function mintur_style ($content, $title, $qr, $qr_content) {
    $html = '<html>';
    $html .= '   <head>';
    $html .= '      <style>';
    // Define the margins of your page URLD
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
      $html .= '      <img style="position:fixed; left:50px; top:150px;" src="data:image/png;base64,'.$this->qrcode($qr_content).'"/>';
    }
    $html .= '      <header>';
    $html .= '         <img style="position:fixed; height:auto; width:870px; top: 50px; right: 50px;" src="'.$this->get_background_image().'"/>';
    $html .= '         <h2 style="position: fixed; left:0px; right:0px; top:250px; font-family: Arial, Helvetica, sans-serif; text-align:center;">'. $title .'</h2>';
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

  function get_background_image() {
    return 1;
  }
}