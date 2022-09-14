<?php
namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class RecaptchaService{

     public function __construct(private HttpClientInterface $client)
     {
        
     }

    public function curlProcess(string $url){

        $curl = \curl_init($url);

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        return curl_exec($curl);
    }

    

    public function captchaResponse(string $url){
        return $this->client->request('POST', $url);
    }
}