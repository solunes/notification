<?php

namespace Solunes\Notification\App\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller\Api;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AppController extends \App\Http\Controllers\Api\BaseController {
    
    public function postLogin(Request $request){
        if($request->has('email')&&$request->has('password')){

            // Autentificar con JWT, generar Token
            $credentials = $request->only('email', 'password');
            try {
                // verify the credentials and create a token for the user
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'Su usuario y contraseña no coinciden.'], 401);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'Hubo un error, vuelva a intentarlo.'], 500);
            }
            // Guardar el ID de dispositivo para enviarle Push Notifications
            $date = date('d/n/Y H:i:s', strtotime('+6 months'));
            $user = auth()->user();
            if($request->has('token')){
                $token_notification = $request->input('token');
                if($token_notification&&!\App\Device::where('token', $token_notification)->first()){
                    $device = new \App\Device;
                    $device->user_id = $user->id;
                    $device->token = $token_notification;
                    $device->save();
                }
            } else {
                \Log::info(json_encode($request->all()));
            }

            $subitem['id'] = $user->id;
            $subitem['email'] = $user->email;
            $subitem['name'] = $user->name;
            return ['token'=>$token, 'expirationDate'=>$date, 'item'=>$subitem];
        } else {
            return response()->json(['error' => 'Hubo un error.'], 401);
        }
    }

    public function getCheckLogin(){
        if(auth()->check()){
            return ['auth'=>true];
        } else {
            return response()->json(['error' => 'No se encontró su sesión.'], 401);
        }
    }
    
    public function postRegister(Request $request){
        if($request->has('email')&&$request->has('cellphone')&&$request->has('zone_id')&&$request->has('name')&&$request->has('gender')&&$request->has('birth')&&$request->has('password')&&$request->has('confirm_password')){
            if($request->input('password')!=$request->input('confirm_password')){
                return response()->json(['error' => 'Su contraseñas no coinciden.'], 401);
            }
            if(\App\User::where('email', $request->input('email'))->first()){
                return response()->json(['error' => 'Ya existe un usuario registrado con su correo: '.$request->input('email')], 401);
            }
            if(\App\User::where('cellphone', $request->input('cellphone'))->first()){
                return response()->json(['error' => 'Ya existe un usuario registrado con su teléfono: '.$request->input('cellphone')], 401);
            }
            $item = new \App\User;
            $item->email = $request->input('email');
            $item->cellphone = $request->input('cellphone');
            $item->zone_id = $request->input('zone_id');
            $item->name = $request->input('name');
            $item->gender = $request->input('gender');
            $item->birth = $request->input('birth');
            $item->password = $request->input('password');
            $item->save();
            //  Generar codigo de confirmacion aleatorio y guardar en el sistema
            $confirmationCode = rand(1000,9999);
            // Enviar un correo electronico con bienvenida
            $vars = ['@code@'=>$confirmationCode, '@name@'=>$item->name, '@email@'=>$item->email];
            \FuncNode::make_email('user_registered', [$item->email], $vars);
            // Enviar un sms con código de bienvenida
            \Notification::sendSms($item->cellphone, 'Su código de verificación es '.$confirmationCode.'. Muchas gracias por registarse.');

            // Autentificar con JWT, generar Token
            $credentials = $request->only('email', 'password');
            try {
                // verify the credentials and create a token for the user
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'Su usuario y contraseña no coinciden.'], 401);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'Hubo un error, vuelva a intentarlo.'], 500);
            }
            // Guardar el ID de dispositivo para enviarle Push Notifications
            $date = date('d/n/Y H:i:s', strtotime('+6 months'));

            return ['confirmationCode'=>$confirmationCode, 'token'=>$token, 'expirationDate'=>$date];
        } else {
            return response()->json(['error' => 'Llene todos los campos.'], 406);
        }
    }

    public function processTempImage($image, $folder){
        /*\Log::info('1:'.$image);
        \Log::info('2:'.$folder);*/
        $explode = explode('/',$image);
        $new_image = asset(\Asset::get_file($explode[0], $explode[1]));
        $uploaded_image = \Asset::upload_image($new_image, $folder);
        \Storage::delete($image);
        return $uploaded_image;
    }

    public function postUploadPendingStorage(Request $request){
        if(auth()->check()&&$request->has('uploads')&&$request->has('timestamp')){
            \Log::info('Starting:'.json_encode($request->input('uploads')));
            foreach($request->input('uploads') as $key => $upload){
                $route = NULL;
                \Log::info('Check:'.$key.' / '.json_encode($upload));
                $offline_node = NULL;
                if($key=='attendance'){
                    $route = 'postRegisterAssistance';
                } else if($key=='attendance-exit'){
                    $route = 'postRegisterExitAssistance';
                } else if($key=='attendance-floor'){
                    $route = 'postRegisterFloorAssistance';
                } else if($key=='point-request'){
                    $route = 'postRegisterNote';
                } else if($key=='register-form'){
                    $route = 'postRegisterForm';
                }
                if($route&&count($upload)>0){
                    foreach($upload as $unit){
                        \Log::info('Try to post:'.json_encode($unit));
                        $new_request = new \Illuminate\Http\Request();
                        $new_request->setMethod('POST');
                        $new_request->request->add($unit);
                        $response = $this->$route($new_request);
                        \Log::info(json_encode($response));
                    }
                }
            }
            return ['timestamp'=>$request->input('timestamp')];
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException('Hubo un error.');
        }
    }

    public function postUploadFiles(Request $request){
        \Log::info('starting process');
        if(auth()->check()){
            \Log::info('success session');
            $s3_file = '';
            \Log::info(json_encode($request->all()));
            if($request->hasFile('file')){
                \Log::info('has file');
                $file = $request->file('file');
                $s3_file = \Asset::upload_file($file, 'app-temp');
                $s3_file = 'app-temp/'.$s3_file;
                //$s3_file = asset(\Asset::get_file('app-temp', $s3_file));
                \Log::info('Finished upload:'.$s3_file);
            } else {
                \Log::info('Fail upload');
            }
            return $s3_file;
        } else {
            \Log::info('no session');
            return response()->json(['error' => 'No se encontró su sesión.'], 401);
        }
    }

}
