<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;  
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller as Controller;

/**
 * @OA\Info(title="API SMS ANGOLA", version="1.0")
 */
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
    return response()->json($response, 200);
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
  //Send mail
  public function sendMail($to, $cc, $subject, $content) {
    require base_path("vendor/autoload.php");
    $mail = new PHPMailer(true);   // Passing `true` enables exceptions
    $mail->CharSet = "UTF-8";
    try {
      // Email server settings
      $mail->SMTPDebug = 0;
      $mail->isSMTP();
      $mail->Host = env('MAIL_HOST');           	// smtp host
      $mail->SMTPAuth = true;
      $mail->Username = env('MAIL_USERNAME');   		// sender username
      $mail->Password = env('MAIL_PASSWORD');    // sender password
      $mail->SMTPSecure = "ssl";              // encryption - ssl/tls
      $mail->Port = env('MAIL_PORT');            // port - 587/465
      $mail->timeout = null;
      $mail->Encoding = 'base64';

      $mail->setFrom(env('MAIL_USERNAME'), env('MAIL_FROM_NAME')); // sender email and name
      $mail->addAddress($to);
      if ($cc != '') {
        foreach($cc as $email):
          $mail->AddCC($email);
        endforeach;
      }
      $mail->addReplyTo(env('MAIL_USERNAME'), env('MAIL_FROM_NAME')); // sender email and name);
      $mail->SMTPOptions = [
        'ssl' => [
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => false
        ]
      ];
      $mail->isHTML(true);                	// Set email content format to HTML
      $mail->Subject = $subject;
      $mail->Body = $content;
      if ($mail->send())
        Log::info('SendMail - Success : Email has been sent.');
      else
        Log::warning('SendMail - Failed : ' . $mail->ErrorInfo);
    } catch(Exception $e) {
      Log::warning('SendMail - Error : ' . $e->getMessage());
    }
  }
}