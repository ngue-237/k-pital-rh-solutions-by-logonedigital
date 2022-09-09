<?php
namespace App\Services;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MailSender {

    public function __construct(private ParameterBagInterface $params)
    {
        
    }

    private $api_key = "78390f86428d32654a16bf9d946c8b6c";
    private $api_key_secret = "8ccb2454880789b17334e1b38ef09cb4";

    public function send($to_email, $to_name, $content, $subject){


       // $mj = new Client($this->params->get('api_key'), $this->params->get('api_key_secret'),true,['version' => 'v3.1']);
        $mj = new Client($this->api_key, $this->api_key_secret,true,['version' => 'v3.1']);
        //dd($content);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "tpkdmta@gmail.com",
                        'Name' => "Mintoua"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'Subject' => $subject,

                    'Variables' => [
                        'content'=>$content
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && var_dump($response->getData());
       
    }

}