<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

use App\User;

class UserController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private function UpdateValidator($data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6','regex:/[A-Z]/',
            'regex:/[@$!%*#?&]/', 'confirmed'],
        ]);
    }

    private function LoginValidator($data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6','regex:/[A-Z]/',
            'regex:/[@$!%*#?&]/', 'confirmed'],
        ]);
    }

    private function RegisterValidator($data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6','regex:/[A-Z]/',
            'regex:/[@$!%*#?&]/', 'confirmed'],
        ]);
    }

    public function update($id, ValidatesRequests $request)
    {

      

      try{
        $data = $request->all();

        if (UpdateValidator($data)){
          throw new Exception();
        }

        $userObj = User::find($id);

        $userObj->first_name  = $data['first_name'];
        $userObj->last_name   = $data['last_name'];
        $userObj->password    = $data['password'];
  
        $userObj->save();
        // return response()->json_encode($userObj);
        return json_encode($userObj);
      } catch (\Exception $e){
        return $e->message || false;
      }
      
     
    }

    public function login(ValidatesRequests $request)
    {
      try{

        $data = $request->all();

        if (LoginValidator($data)){
          throw new Exception();
        }
        
        $res = User::where([
          ['email',$data['email']],
          ['password',Hash::make($data['password'])],
        ])
        ->firstOrFail();

        return true;
      } catch(\Exception $e){
        return $e->message || false;
      }
    }

    public function register(ValidatesRequests $request)
    {
      try{

        $data = $request->all();

        if (RegisterValidator($data)){
          throw new Exception();
        }
         $userObj = User::create([
              'first_name' => $data['first_name'],
              'last_name' => $data['last_name'],
              'email' => $data['email'],
              'password' => Hash::make($data['password']),
          ]);

        $userObj->save();

        return json_encode($userObj);
        
      } catch (\Exception $e){
        return $e->message || false;
      }     
    }
}
