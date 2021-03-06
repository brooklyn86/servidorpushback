<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\TokenPush;
class NotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $urlSite        = 'http://localhost:8888';
        $urlWebSocket   = $urlSite.'/'.$event->endpoint;
        $dataParams     = http_build_query($event->params);
        $ch             = curl_init();
        try{
            curl_setopt($ch, CURLOPT_URL, $urlWebSocket);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataParams);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            $response   = curl_exec($ch);
            $codeHttp   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            
            $codeFail = json_decode($response);

            foreach($codeFail as $code){
                $p = TokenPush::where('token', $code)->first();
                $p->status = 0;
                $p->save();
            }
            return $response;
     
        }
        catch(Exception $e){
            return $e;
        }
    }
}
