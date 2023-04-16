<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up (): void
    {
        Schema::create( 'messages', function ( Blueprint $table ) {
            $table->id();

            $table->string('message_id');

            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->longText( 'rawMessage' )->nullable();
            $table->timestamps();
        } );
    }

    public function down (): void
    {
        Schema::dropIfExists( 'messages' );
    }
};
