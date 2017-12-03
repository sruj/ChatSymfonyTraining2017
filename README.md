# Symfony Chat (WebSocket Ratchet)

Prosty projekt chatu wykonany we frameworku Symfony z użyciem technologii WebSocket, biblioteki Ratchet.

#### Wymagania
1. Postaw nowy projekt z uzyciem symfony 2.8
2. Uruchom serwer Ratchet wywołując skrypt komendą: `chat:server`
3. W przeglądarce otwórz stronę projektu `/chat`
 
#### Funkcjonalność
Chat dostępny po wprowadzeniu nicku. W oknie głównym chatu wyświetlany tekst. W prawym oknie lista użytkowników chatu.    

#### Kluczowe miejsca
- https://github.com/sruj/ChatSymfonyTraining2017/blob/master/src/AppBundle/Resources/public/js/chat.js
- https://github.com/sruj/ChatSymfonyTraining2017/blob/master/src/AppBundle/Controller/ChatController.php
- https://github.com/sruj/ChatSymfonyTraining2017/blob/master/src/AppBundle/Websocket/Chat.php
