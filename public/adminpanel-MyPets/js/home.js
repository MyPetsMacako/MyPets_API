$(document).ready(function(){
    document.getElementById("logoff").onclick = logoff;
    var data = [];
})

getData();

function getData (){
    $token = window.localStorage.getItem("token");
    $.ajax({
        type: "GET",
        url: "http://mypetsapp.es/api/adminPanelInfo",
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", $token);
        },
        success:function(data){
            console.log("success");
            fillData(data)
        },
        error: function(result) {
            console.log("error");
        }

    });
}

function fillData (data){
    document.getElementById('wellcome').innerHTML = "Bienvenido, "+data["userName"];
    document.getElementById('users').innerHTML = +data["users"];
    document.getElementById('pets').innerHTML = data["pets"];
    document.getElementById('appointments').innerHTML = data["appointments"];
    /* document.getElementById('photos').innerHTML = data["photos"];
    document.getElementById('reports').innerHTML = data["reports"]; */
}

function logoff(){
    window.localStorage.removeItem("token");
    window.location.href="index.html";
}
