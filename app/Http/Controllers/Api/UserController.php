<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\User;  
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\Models\TokenPush;
use App\Models\Device;
use App\Models\Message;

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
    $roles = Auth::user()->roles;
    $isAdmin = $this->filterRole($roles);
    $messageTotal = [];
    $dispostivosTotal = [];
    if($isAdmin){
      $menJan = Message::whereMonth('created_at', 1)->whereYear('created_at', date('Y'))->count();
      $menFev = Message::whereMonth('created_at', 2)->whereYear('created_at', date('Y'))->count();
      $menMar = Message::whereMonth('created_at', 3)->whereYear('created_at', date('Y'))->count();
      $menAbri = Message::whereMonth('created_at', 4)->whereYear('created_at', date('Y'))->count();
      $menMai = Message::whereMonth('created_at', 5)->whereYear('created_at', date('Y'))->count();
      $menJun = Message::whereMonth('created_at', 6)->whereYear('created_at', date('Y'))->count();
      $menJul = Message::whereMonth('created_at', 7)->whereYear('created_at', date('Y'))->count();
      $menAgo = Message::whereMonth('created_at', 8)->whereYear('created_at', date('Y'))->count();
      $menSet = Message::whereMonth('created_at', 9)->whereYear('created_at', date('Y'))->count();
      $menOut = Message::whereMonth('created_at', 10)->whereYear('created_at', date('Y'))->count();
      $menNov = Message::whereMonth('created_at', 11)->whereYear('created_at', date('Y'))->count();
      $menDez = Message::whereMonth('created_at', 12)->whereYear('created_at', date('Y'))->count();
      $messageTotal = [[$menJan,$menFev,$menMar, $menAbri, $menMai, $menJun, $menJul, $menAgo, $menSet, $menOut, $menNov, $menDez]];
      $totalCliente = Device::join('token_pushes as token','devices.id','=','token.app_id');
      $totalClienteAtivo = Device::join('token_pushes as token','devices.id','=','token.app_id');
      $totalClienteInativo = Device::join('token_pushes as token','devices.id','=','token.app_id');

      $total = $totalCliente->count();
      $totalAtivo = $totalClienteAtivo->where('status',1)->count();
      $totalInativo= $totalClienteInativo->where('status',0)->count();
      $dispostivosTotal = [[$totalAtivo,$totalInativo,$total]];

    }else{
      $totalCliente = Device::join('token_pushes as token','devices.id','=','token.app_id')->where('devices.user_id',Auth::user()->id)->get();
    }

    
    return response()->json(['user' => $user,'clients' => ['total'=> $total,'totalAtivo'=>$totalAtivo, 'totalInativo'=>$totalInativo], 'messageTotal' => $messageTotal, 'dispostivosTotal' => $dispostivosTotal], $this->successStatus); 
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