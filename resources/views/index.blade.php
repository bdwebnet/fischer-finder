<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>fischer Produktberater</title>

    @vite('css/app.css')

    @livewireStyles
</head>
<body class="antialiased">

<a href="https://wa.me/15550517212?text={{ urlencode('Hi, ich brauche Hilfe. Kannst du mir weiterhelfen?') }}" target="_blank">
    Nachricht schreiben
</a>

<livewire:chat-initiator />

@vite('js/app.js')

@livewireScripts

</body>
</html>
