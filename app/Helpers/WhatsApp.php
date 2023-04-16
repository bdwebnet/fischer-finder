<?php

namespace App\Helpers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Netflie\WhatsAppCloudApi\WebHook;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;

class WhatsApp
{
    public static function tool ()
    {
        return new WhatsAppCloudApi( [
            'from_phone_number_id' => config( 'services.whatsapp.from-phone-number-id' ),
            'access_token' => config( 'services.whatsapp.token' ),
        ] );
    }

    public static function webhookVerify ( Request $request )
    {
        $mode = $request->get( 'hub_mode' ) ?? null;
        $token = $request->get( 'hub_verify_token' ) ?? null;
        $challenge = $request->get( 'hub_challenge' ) ?? '';

        if ( 'subscribe' !== $mode || $token !== config( 'services.whatsapp.verify-token' ) ) {
            return response( $challenge, 403 );
        }

        return response( $challenge, 200 );
    }

    public static function read ( Request $request )
    {
        $webhook = new WebHook();
        return $webhook->read( $request->toArray() );
    }

    public static function saveMessageFromCustomer ( WebHook\Notification\MessageNotification $message, string $rawMessage = '' )
    {


        $customerId = $message->customer()->id();
        $customerPhoneNumber = $message->customer()->phoneNumber();
        $customerName = $message->customer()->name();

        $customer = Customer::firstOrCreate( [
            'id' => $customerId,
            'phone_number' => $customerPhoneNumber,
            'name' => $customerName,
        ] );

        $messageId = $message->id();

        return $customer->messages()->create( [
            'message_id' => $messageId,
            'rawMessage' => $rawMessage,
        ] );
    }

    public static function sendTextMessage ( int $numMessages, string $phoneNumber )
    {
        switch ($numMessages) {
            case 1:
                WhatsApp::tool()->sendTextMessage( $phoneNumber, 'Hi! Ich helfe dir, das richtige fischer Befestigungsprodukt zu finden. Wenn du z.B. etwas an einer Wand befestigen möchtest, kannst du mir ein Bild davon schicken.' );
                break;
            case 2:
                WhatsApp::tool()->sendTextMessage( $phoneNumber, 'Ich erkenne eine Betonwand. Was möchtest du an der Wand befestigen?' );
                break;
            case 3:
                WhatsApp::tool()->sendTextMessage( $phoneNumber, 'Dafür empfehle ich dir folgenden Dübel: https://www.fischer.de/de-de/produkte/standardbefestigungen/kunststoffduebel/duopower/535210-duopower-6-x-30-k (Du brauchst voraussichtlich 2 Stück)', 'https://www.fischer.de/de-de/produkte/standardbefestigungen/kunststoffduebel/duopower/535210-duopower-6-x-30-k' );
                WhatsApp::tool()->sendTextMessage( $phoneNumber, 'Den Dübel mit einer dazu passenden Schraube findest du hier: https://www.fischer.de/de-de/produkte/standardbefestigungen/kunststoffduebel/duopower/535214-duopower-6-x-30-s-k (Du brauchst voraussichtlich je 2 Stück)', 'https://www.fischer.de/de-de/produkte/standardbefestigungen/kunststoffduebel/duopower/535214-duopower-6-x-30-s-k' );
                break;
            case 4:
                WhatsApp::tool()->sendTextMessage( $phoneNumber, 'Gerne. Wenn du weitere Fragen hast, schreibe mir einfach wieder.' );
                break;
            default:
                break;
        }
    }

    public static function getMessageContent ( int $numMessages )
    {

        switch ($numMessages) {
            case 1:
                return 'Hi! Ich helfe dir, das richtige fischer Befestigungsprodukt zu finden. Wenn du z.B. etwas an einer Wand befestigen möchtest, kannst du mir ein Bild davon schicken.';
            case 2:
                return 'Ich erkenne eine Betonwand. Was möchtest du an der Wand befestigen?';
            case 3:
                return 'Ich helfe dir gerne weiter, den passenden Dübel und Zubehör zu finden. Was möchtest du an der Wand befestigen?';
            default:
                return 'Hi, how are you?';
        }

    }

}
