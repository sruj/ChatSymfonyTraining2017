// {#TODO: ZAMIENIĆ JS NA JQUERY#}
// {#TODO: FAJNIE GDYBY HOST I PORT BYŁY KONFIGUROWALNE W config.yml#}
var conn = new WebSocket('ws://localhost:8080');

$(window).on('load', function() {
    var username = document.getElementById('usrname').innerHTML;
    var message = {"message": {"flag": "newUsername", "data": username}};
    var strmsg = JSON.stringify(message);

    conn.onopen = function() {
        conn.send(strmsg);
    };

    console.log("Wysłałem do centrali wiadomość o treści:" + strmsg);
});



conn.onopen = function (e) {
    console.log("Connection start!");
};



conn.onmessage = function (e) {
    console.log("new message on JavaScript onmessage: " + e.data);

    var message = JSON.parse(e.data);
    var flag = message.message.flag;
    if(flag === "userlist"){
        var userlist = message.message.data; //'{"message":{ "flag":"userlist", "data": {"68":"Dantuta","13":"Tadeusz"}}}';
        displayUserList(userlist);
    }
    if(flag === "chatMessage"){
        var div = document.getElementById('chatArea');
        console.log(message);
        displayChatMessage(div, message);
    }

};


function displayUserList(userlist) {
    var usersListDIV = document.getElementById('usersList');
    usersListDIV.innerHTML = '';
    $.each(userlist,function(k,username){
        usersListDIV.innerHTML = usersListDIV.innerHTML + username + '<br />';
    })
}

function displayChatMessage(div, message) {
    div.innerHTML = div.innerHTML + '<br />' + '<span class="username">' + message.message.data.username +
        ': </span><span class="chatMessage" >' + message.message.data.chatMessage + '</span>';
}


function sendChatMessage(e) {
    console.log("jeste");
    if (e.keyCode == 13) {
        var username = document.getElementById('usrname').innerHTML;
        var chatMessage = document.getElementById('chatFormInput').value;
        $("input[name=txt]").val('');
        var message = {"message": {"flag": "chatMessage", "data": {"username":username,"chatMessage":chatMessage}}};
        var strmsg = JSON.stringify(message);
        conn.send(strmsg);
        return false;
    }
    return true;
}
