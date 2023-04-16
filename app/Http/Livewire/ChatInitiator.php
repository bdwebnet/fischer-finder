<?php

namespace App\Http\Livewire;

use App\Helpers\WhatsApp;
use Livewire\Component;

class ChatInitiator extends Component
{
    public function render ()
    {
        return view( 'livewire.chat-initiator' );
    }

    public function sendInitialMessage() {
        WhatsApp::tool()->sendTemplate('4915752100006', 'test2', 'de');
    }
}
