<?php

namespace App\Http\Controllers;

use App\Helpers\WhatsApp;
use App\Models\Customer;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Netflie\WhatsAppCloudApi\Message\Media\MediaObjectID;

class WhatsAppWebhookController extends Controller
{

    public function get ( Request $request )
    {
        WhatsApp::webhookVerify( $request );
    }

    public function post ( Request $request )
    {
        //Log::info( 'New request from ' . $request->getClientIp() );

        if (
            !( $request->hasHeader( 'X-Hub-Signature-256' ) &&
                Str::startsWith( $request->header( 'X-Hub-Signature-256' ), 'sha256=' ) &&
                hash( 'sha256', $request->getContent(), true ) !== substr( $request->header( 'X-Hub-Signature-256' ), 6 ) )
        ) {
            abort( 401 );
        }

        $message = WhatsApp::read( $request );

        //Log::info(print_r($message, true));

        switch (get_class( $message )) {

            case 'Netflie\WhatsAppCloudApi\WebHook\Notification\Text':
                Log::info($request->getContent());
                $dbMessage = WhatsApp::saveMessageFromCustomer( $message, $request->getContent() );

                if ($message->message() === "delete") {
                    $dbMessage->customer()->first()->delete();
                    WhatsApp::tool()->sendTextMessage( $message->customer()->phoneNumber(), "All messages deleted" );
                    break;
                } else {
                    WhatsApp::sendTextMessage( Message::count(), $message->customer()->phoneNumber() );
                }

                break;

            case 'Netflie\WhatsAppCloudApi\WebHook\Notification\Media':
                WhatsApp::saveMessageFromCustomer( $message, $request->getContent() );
                $messageConent = WhatsApp::getMessageContent(Message::count());
                WhatsApp::tool()->sendTextMessage( $message->customer()->phoneNumber(), $messageConent );
                break;

            case 'Netflie\WhatsAppCloudApi\WebHook\Notification\StatusNotification':
                // Handle status notification
                //Log::info( $request );
                break;

            default:
                // Handle unknown message
                Log::info( 'no' );
                break;

        }

        return response( 'OK', 200 );
    }

}
