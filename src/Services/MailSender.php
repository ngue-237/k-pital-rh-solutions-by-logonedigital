<?php
namespace App\Services;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MailSender {

    public function __construct(private ParameterBagInterface $params)
    {
        
    }

    public function send($to_email, $to_name, $content, $subject){


       $mj = new Client($this->params->get('api_key'), $this->params->get('api_key_secret'),true,['version' => 'v3.1']);

       //dd ($to_email);
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

                    'TextPart' => "My first Mailjet email",
                    'HTMLPart' => $content,
                    'CustomID' => "AppGettingStartedTest"
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
       
    }

}