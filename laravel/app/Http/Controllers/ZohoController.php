<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class ZohoController extends Controller
{
    public function auth()
    {   $clientid = '1000.BG2CQW3UUOM34RYN0SUCJR86SKMXVM';
        $uri = route('zohocrm');
        $scope =  'ZohoCRM.modules.deals.CREATE';
        $accestype = 'offline';
        $redirectTo = 'https://accounts.zoho.com/oauth/v2/auth' . '?' . http_build_query(
        [
        'client_id' => $clientid,
        'redirect_uri' => $uri,
        'scope' =>'ZohoCRM.modules.deals.CREATE',
        'response_type' => 'code',
        ]);

        return redirect($redirectTo);
    }

    public function store()
    {
        $client_id = '1000.BG2CQW3UUOM34RYN0SUCJR86SKMXVM';
        $client_secret = 'cb9560795cd52558f8c087a5be5049139cdd62c0f4';
       

        $tokenUrl = 'https://accounts.zoho.com/oauth/v2/token?code=&client_id='.$client_id.'&client_secret='.$client_secret.'&redirect_uri='.route('zohocrm').'&grant_type=authorization_code';

        $tokenData = [

        ];

        $curl = curl_init();     
        curl_setopt($curl, CURLOPT_VERBOSE, 0);     
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);     
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
        curl_setopt($curl, CURLOPT_POST, TRUE);//Regular post  
        curl_setopt($curl, CURLOPT_URL, $tokenUrl);     
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);     
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($tokenData));

        $tResult = curl_exec($curl);
        curl_close($curl);
        $tokenResult = json_decode($tResult);
        if(isset($tokenResult->access_token) && $tokenResult->access_token != '') {
      
            $jsonData = '{
                "Deal_Name": Task,
                "Subject":Deskription
            }';

            $curl = curl_init('https://www.zohoapis.com/crm/v2/Deals');
            curl_setopt($curl, CURLOPT_VERBOSE, 0);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);     
            curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);     
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Zoho-oauthtoken ".$tokenResult->access_token,
            ) );
            curl_setopt($curl, CURLOPT_POSTFIELDS,'JSONString='.$jsonData);
            
        
            $cResponse = curl_exec($curl);
            curl_close($curl);

            $contactResponse = json_decode($cResponse);
         
        } 
    }
}