<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\User;  
use Illuminate\Support\Facades\Auth; 
use Validator;

class UserController extends Controller 
{
public $successStatus = 200;
/** 
   * login api 
   * 
   * @return \Illuminate\Http\Response 
   */ 
  public function login(Request $request){
    if(Auth::attempt(['email' =>  $request->email, 'password' =>  $request->password])){ 
      $user = Auth::user(); 
      $success['token'] =  $user->createToken('MyApp')->accessToken; 
      return response()->json(['success' => $success], $this->successStatus); 
    } 
    else{ 
      return response()->json(['error'=>'Unauthorised'], 401); 
    } 
  }
/** 
   * Register api 
   * 
   * @return \Illuminate\Http\Response 
   */ 
  public function register(Request $request) 
  { 
    $validator = Validator::make($request->all(), [ 
      'name' => 'required', 
      'email' => 'required|email', 
      'password' => 'required', 
      'c_password' => 'required|same:password', 
    ]);
    if ($validator->fails()) { 
        return response()->json(['error'=>$validator->errors()], 401);            
    }
    $input = $request->all(); 
    $input['password'] = bcrypt($input['password']); 
    $user = User::create($input); 
    $success['token'] =  $user->createToken('MyApp')->accessToken; 
    $success['name'] =  $user->name;

    return response()->json(['success'=>$success], $this->successStatus); 


  }
/** 
   * details api 
   * 
   * @return \Illuminate\Http\Response 
   */ 
  public function details() 
  { 
    $user = Auth::user(); 
    $devices = Auth::user()->devices; 
    $devices = Auth::user()->roles; 
    return response()->json(['user' => $user ], $this->successStatus); 
  } 
/** 
   * details api 
   * 
   * @return \Illuminate\Http\Response 
   */ 

  public function filterRole($roles){
    foreach($roles as $role){
      if($role->role_id == \App\Models\Role::ROLE_ADMIN){
        return true;
      }
    }
    return false;
  }
  public function users() 
  { 

    $roles = Auth::user()->roles;
    $isAdmin = $this->filterRole($roles);
    $users = [];
    if($isAdmin){
      $users = User::paginate(50);
    }

    return response()->json(['users' => $users], $this->successStatus); 
  } 

public function user(Request $request) 
  { 
    
    $request = $request->all();
    $users = User::join('user_profiles', function ($join)  use  ($request) {
        $join->on('users.id', '=', 'user_profiles.user_id')
        ->where(function ($query) use  ($request) {
          $query->where('users.id', 'LIKE', '%'.$request['id'].'%');   
        });
    })->get();
    return response()->json(['users' => $users], $this->successStatus); 
  } 
}