<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
  /**
   * success response method.
   *
   * @return \Illuminate\Http\Response
   */
  public function sendSuccess($message, $data = [], $status = 200){
    $response = [
          'status' => $status,
          'message' => $message,
          'data' => $data,
      ];
      return response()->json($response, $status);
  }
  /**
   * return error response.
   *
   * @return \Illuminate\Http\Response
   */
  public function sendError($message, $data = [], $status = 400){
    $response = [
          'status' => $status,
          'message' => $message,
      ];
      if(!empty($data)){
          $response['data'] = $data;
      }
      return response()->json($response, $status);
  }
}