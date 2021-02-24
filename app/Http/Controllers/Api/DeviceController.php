<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Message;
use App\Models\MessageItem;
use App\Models\TokenPush;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $authUserId = Auth()->user()->id;
        $authUserEmail = Auth()->user()->email;
        $userDevices = Auth()->user()->devices;
        $key = md5($authUserEmail.date('Y-m-d H:m:s'));
        $secret = md5(date('Y-m-d H:m:s'));
        $isExistName = Auth()->user()->devices()->where('name', $request->name)->first();
        if(!$isExistName){
            $device = new Device;
            $device->user_id = $authUserId;
            $device->key = $key;
            $device->name = $request->name;
            $device->secret = $secret;
            $response = $device->save(); 
    
            if($response){
                return response()->json(['error' => false, 'devices' =>  $device , 'message' => 'Novas chaves criada com sucesso!']);
            }
            return response()->json(['error' => true, 'message' => 'Falha ao criar chaves']);

        }
        
        return response()->json(['error' => true, 'message' => 'Falha ao criar device já existente!']);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $dateNow = \Carbon\Carbon::now('America/Sao_Paulo');
        $dateMonth = \Carbon\Carbon::now('America/Sao_Paulo')->add(30,'days');
        $userDevices = Auth()->user()->devices()->where('key', $request->key)->first();
        $messagestotal =  Message::where('key_id',$userDevices->id)->count();
        $messagesMes =  Message::where('key_id',$userDevices->id)->whereBetween('created_at', [$dateNow,$dateMonth])->count();
        $messages =  Message::where('key_id',$userDevices->id)->whereMonth('created_at', date('m'))->orderBy('created_at', 'desc')->paginate(5);
        foreach($messages as $m){
            $mensagemItem = MessageItem::where('message_id', $m->id)->get()->toArray();
            $m->messagem = $mensagemItem;
        }
        $messageJan = Message::where('key_id',$userDevices->id)->whereMonth('created_at', '01')->whereYear('created_at', date('Y'))->count();
        $messageFev = Message::where('key_id',$userDevices->id)->whereMonth('created_at', '02')->whereYear('created_at', date('Y'))->count();
        $messageMar = Message::where('key_id',$userDevices->id)->whereMonth('created_at', '03')->whereYear('created_at', date('Y'))->count();
        $messageAbr = Message::where('key_id',$userDevices->id)->whereMonth('created_at', '04')->whereYear('created_at', date('Y'))->count();
        $messageMai = Message::where('key_id',$userDevices->id)->whereMonth('created_at', '05')->whereYear('created_at', date('Y'))->count();
        $messageJun = Message::where('key_id',$userDevices->id)->whereMonth('created_at', '06')->whereYear('created_at', date('Y'))->count();
        $messageJul = Message::where('key_id',$userDevices->id)->whereMonth('created_at', '07')->whereYear('created_at', date('Y'))->count();
        $messageAgos = Message::where('key_id',$userDevices->id)->whereMonth('created_at', '08')->whereYear('created_at', date('Y'))->count();
        $messageSet = Message::where('key_id',$userDevices->id)->whereMonth('created_at', '09')->whereYear('created_at', date('Y'))->count();
        $messageOut = Message::where('key_id',$userDevices->id)->whereMonth('created_at', '10')->whereYear('created_at', date('Y'))->count();
        $messageNov = Message::where('key_id',$userDevices->id)->whereMonth('created_at', '11')->whereYear('created_at', date('Y'))->count();
        $messageDez = Message::where('key_id',$userDevices->id)->whereMonth('created_at', '12')->whereYear('created_at', date('Y'))->count();

        $totalClients = TokenPush::where('app_id',$userDevices->id)->count();
        $totalAtivosClients = TokenPush::where('app_id',$userDevices->id)->where('status',1)->count();
        $totalDesativadosClients = TokenPush::where('app_id',$userDevices->id)->where('status',0)->count();
        if( $userDevices){
            return Response()->json(['error' => false, 'devices' => $userDevices,'dados' => [
                'totalMensagem' => $messagestotal,
                'totalMensagemMes' => $messagesMes,
                'totalClients' => $totalClients,
                'totalAtivosClients' => $totalAtivosClients,
                'totalDesativadosClients' => $totalDesativadosClients,
                'messages' => $messages->toArray(),
                'monthCount' => [$messageJan,$messageFev,$messageMar,$messageAbr,$messageMai,$messageJun,$messageJul,$messageAgos,$messageSet,$messageOut,$messageNov, $messageDez]
                ]]);
        }
        return Response()->json(['error' => true, 'message' => 'APP não encontrado ou não pertence a esse usuario!']);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
