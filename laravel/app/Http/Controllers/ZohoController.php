<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class ZohoController extends Controller
{
    public function auth(Request $request)
    {
        $uri = route('zohocrm');
        $scope =  'ZohoInvoice.deals.Create';
        $clientid = 'here your zoho clientid';
        $accestype = 'offline';

        $redirectTo = 'https://accounts.zoho.com/oauth/v2/auth' . '?' . http_build_query(
        [
        'client_id' => $clientid,
        'redirect_uri' => $uri,
        'scope' => 'ZohoInvoice.contacts.Create',
        'response_type' => 'code',
        ]);

        return redirect($redirectTo);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $client_id = '1000.BG2CQW3UUOM34RYN0SUCJR86SKMXVM';
        $client_secret = 'cb9560795cd52558f8c087a5be5049139cdd62c0f4';
       

        // Get ZohoCRM Token
        $tokenUrl = 'https://accounts.zoho.com/oauth/v2/token?code='.$input["code"].'&client_id='.$client_id.'&client_secret='.$client_secret.'&redirect_uri='.route('zohocrm').'&grant_type=authorization_code';

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
        // dd($tokenResult->);
        if(isset($tokenResult->access_token) && $tokenResult->access_token != '') {
      

            // Add Contact in ZohoCRM
            $jsonData = '{
                "Deal_Name": Task,
                "Subject":Deskription
            }';

            $curl = curl_init('https://www.zohoapis.com/crm/v2/Deals');
            curl_setopt($curl, CURLOPT_VERBOSE, 0);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);     
            curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
            curl_setopt($curl, CURLOPT_POST, TRUE);//Regular post  
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);     
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Zoho-oauthtoken ".$tokenResult->access_token,
                "X-com-zoho-invoice-organizationid: 688931512"
            ) );
            curl_setopt($curl, CURLOPT_POSTFIELDS,'JSONString='.$jsonData);
            
            //Execute cUrl session
            $cResponse = curl_exec($curl);
            curl_close($curl);

            $contactResponse = json_decode($cResponse);
         
        } 
    }
}