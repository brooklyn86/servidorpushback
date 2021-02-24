<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\NotificationEvent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use App\Models\Message;
use App\Models\MessageItem;
use App\Models\Device;
use App\Models\TokenPush;
class NotificationController extends Controller
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

    public function sendfileUpload(Request $request){
        
        $nameFile = null;
        // Verifica se informou o arquivo e se é válido
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
             
            // Define um aleatório para o arquivo baseado no timestamps atual
            $name = uniqid(date('HisYmd'));
     
            // Recupera a extensão do arquivo
            $extension = $request->file->extension();
     
            // Define finalmente o nome
            $nameFile = "{$name}.{$extension}";
     
            // Faz o upload:
            $upload = $request->file->storeAs('public/images', $nameFile);
            // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao
            $url = Storage::url($upload);
            return Response()->json(['error'=>false,'image' => env('APP_URL').$url]);
            // Verifica se NÃO deu certo o upload (Redireciona de volta)
            if ( !$upload )
                return Response()->json(['error' => true,'image' => 'Erro no Upload']);

        }    
        return Response()->json(['error' => true,'image' => 'Não imagem']);
    }
    public function createTokenClient(Request $request){
        $hasToken = TokenPush::where('app_secret', $request->secret)->where('token', $request->token)->first();
        if(!$hasToken){
            $device = Device::where('secret', $request->secret)->first();
            $token = new TokenPush;
            $token->token = $request->token;
            $token->app_id = $device->id;
            $token->app_secret = $request->secret;
            $token->ip = $request->ip();
            $response = $token->save();

            if($response){
                return Response()->json(['error' => false, 'message' => 'Cadastro com sucesso']);
            }else{
                return Response()->json(['error' => true, 'message' => 'Erro ao cadastrar seu token']);
            }
        }
        return Response()->json(['error' => true, 'message' => 'Erro ao cadastrar seu token']);
    }
    public function create(Request $request)
    {   
        $params = [];
        $arrayAux = null;
        $ip = $request->ip();
        $parametros = $request->all();
        $key = Device::where('key', $parametros['params'][0]['value'])->where('secret', $parametros['params'][1]['value'])->first();
        $tokens = TokenPush::where('app_secret', $parametros['params'][1]['value'])->where('status', 1)->select('token')->get()->toArray();

        if($key){
            $message = new Message;
            $message->key_id = $key->id;
            $message->ip_address = $ip;
            $responseMessage = $message->save();
            foreach($parametros['params'] as $p){
                if(key($p) == 'base'){
                    foreach($p['base'] as $key => $value){
                        $messageItem = new MessageItem;
                        $messageItem->message_id = $message->id;
                        $messageItem->key = $key;
                        $messageItem->value = $value;
                        $messageItem->save();
                        $arrayAux  = Arr::add($arrayAux, $key, $value);

                    }
                }else{
                    if($p['key'] != "key" && $p['key'] != 'secret'){
                        $messageItem = new MessageItem;
                        $messageItem->message_id = $message->id;
                        $messageItem->key = $p['key'];
                        $messageItem->value = $p['value'];
                        $messageItem->save();
                        $arrayAux  = Arr::add($arrayAux, $p['key'], $p['value']);

                    }
                }

                    
            } 
            $tokensPush = [];
            foreach($tokens as $token){
               array_push($tokensPush,$token['token']);
            }

            $arrayAux  = Arr::add($arrayAux, 'tokens', $tokensPush);
            event(new NotificationEvent($arrayAux, 'send-notification'));
        }
        
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
    public function show($id)
    {
        //
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
