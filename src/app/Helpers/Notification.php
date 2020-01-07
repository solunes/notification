<?php 

namespace Solunes\Notification\App\Helpers;

use Validator;

class Notification {
    
    public static function sendEmail($email_title, $to_array, $message_title, $message_content, $link = NULL, $icon = 'Mail-Open') {
      foreach($to_array as $to_data){
        $array = ['title'=>$message_title, 'content'=>$message_content, 'link'=>$link, 'icon'=>$icon];
        if(isset($to_data['name'])){
          $array['name'] = $to_data['name'];
        } else {
          $array['name'] = 'Usuario';
        }
        if(isset($to_data['email'])){
          $array['email'] = $to_data['email'];
        } else {
          $array['email'] = $to_data;
        }
        \Mail::send('notification::emails.styled', $array, function($m) use($array, $email_title) {
          $m->to($array['email'], $array['name'])->subject($email_title);
        });
      }
    }

    public static function sendSms($number, $message, $sender = NULL, $transactional = false, $country_code = '+591') {
      \Log::info('Trying to send SMS');
      $params = array(
        'credentials' => array(
          'key' => config('notification.aws.key'),
          'secret' => config('notification.aws.secret'),
        ),
       'region' => config('notification.aws.region'), // < your aws from SNS Topic region
       'version' => 'latest'
      );
      $sns = new \Aws\Sns\SnsClient($params);
      $number = $country_code.$number;
      if(!$sender){
        $sender = config('app.name');
      }
      $type = 'Promotional';
      if($transactional){
        $type = 'Transactional';
      }
      $message = str_replace(
      array('á','é','í','ó','ú','ñ'),
      array('a','e','i','o','u','n'),
      $message);
      $message = iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$message);
      $args = array(
        "Message" => $message,
        "PhoneNumber" => $number,
        "MessageAttributes" => [
          'AWS.SNS.SMS.SMSType'=>['DataType'=>'String','StringValue'=>$type],
          'AWS.SNS.SMS.SenderID'=>['DataType'=>'String','StringValue'=>$sender]
        ]
      );
      $result = $sns->publish($args)->get('MessageId');
      \Log::info('SMS Published Result: '.json_encode($result));
      return $result;
    }

    public static function sendNotificationToUser($user_id, $message, $url = NULL, $payload = NULL, $buttons = NULL, $schedule = NULL, $headings = NULL, $subtitle = NULL) {
        $device_tokens = \Solunes\Notification\App\UserDevice::where('user_id',$user_id)->lists('token')->toArray();
        if($message&&count($device_tokens)>0){
          \Notification::sendPusherNotification($message, $device_tokens, $url, $payload, $buttons, $schedule, $headings, $subtitle);
          return true;
        } else {
          return false;
        }
    }

    public static function sendPusherNotification($message, $device_tokens, $url = NULL, $payload = NULL, $buttons = NULL, $schedule = NULL, $headings = NULL, $subtitle = NULL) {
      if(!$headings){
        $headings = config('solunes.app_name');
      }
      if(!$subtitle){
        $subtitle = config('solunes.app_name');
      }
      foreach($device_tokens as $device_token){
          if($device_token&&$device_token!='null'){
              \OneSignal::sendNotificationToUser($message, $device_token, $url, $payload, $buttons, $schedule, $headings, $subtitle);
          }
      }
      //\OneSignal::sendNotificationToAll($notification['message'], NULL, $data = $notification['payload'], NULL, NULL);
      /*$full_api_path = 'https://onesignal.com/api/v1/notifications';
      $notification['title'] = 'Blitz Delivery';
      $profile = 'dev';
      $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJkZTQ0Mjk5MS1hMjQwLTQ1ZGItOGY0NC0wYjA2N2IzYTA5NTgifQ.qYuWWjaPKNkNN9GV7bfUll_CqriU_2_ZsHhddDsjsQY';
      $array = ['tokens'=>$devices, 'notification'=>$notification, 'profile'=>$profile];
      $client = new \GuzzleHttp\Client();
      $client->setDefaultOption('headers', array('Content-Type'=>'application/json', 'Authorization'=>'Bearer '.$token));
      $res = $client->post($full_api_path, ['body' => json_encode($array) ]);
      \Log::info($res->json());
      return true;
      if($res->getStatusCode()==200||$res->getStatusCode()==201){
          return true;
      } else {
          \Log::info($res->json());
          //\Func::send_ionic_push($devices, $notification);
      }*/
      return true;
    }

    public static function saveAppUserDevice($user_id, $token, $expiration_date) {
      if($user_id&&$token){
        if($device = \Solunes\Notification\App\UserDevice::where('token', $token)->where('user_id', $user_id)->first()){
        } else {
          $device = new \Solunes\Notification\App\UserDevice;
          $device->user_id = $user_id;
          $device->token = $token;
        }
        $device->expiration_date = $expiration_date;
        $device->save();
      } else {
        \Log::info('Error en registrar push token: Usuario: '.$user_id.' - Token: '.$token);
      }
    }

    public static function generateAudio($message, $sex = 'female', $file = 'audio', $extension = 'mp3', $folder = 'audio') {
        $params = array(
          'credentials' => array(
            'key' => config('notification.aws.key'),
            'secret' => config('notification.aws.secret'),
          ),
         'region' => config('notification.aws.region'), // < your aws from SNS Topic region
         'version' => 'latest'
        );
        $polly = new \Aws\Polly\PollyClient($params);
        $voiceId = 'Penelope';
        if($sex=='male'){
          $voiceId = 'Miguel';
        } else if($sex=='en'||$sex=='female-en'){
          $voiceId = 'Joanna';
        } else if($sex=='male-en'){
          $voiceId = 'Matthew';
        }
        $args = array(
          "OutputFormat" => $extension,
          "Text" => $message,
          "TextType" => 'text',
          "VoiceId" => $voiceId
        );
        $result = $polly->synthesizeSpeech($args);
        $result = $result->get('AudioStream')->getContents();
        $file .= '-'.rand(100000,999999).'-'.time().'-'.gmdate("Y_m_d-H_i_s");
        $filename = $folder.'/'.$file.'.'.$extension;
        \Storage::put($filename, $result);
        return $filename;
    }

}