$(document).ready(function(){
    url_base = 'http://localhost:8888/laravel-ivanodp/MyPets_API/public/index.php'
    document.getElementById("logoff").onclick = logoff;
    var data = [];
})

getData();

function getData (){
    $token = window.localStorage.getItem("token");
    $.ajax({
        type: "GET",
        url: "http://localhost:8888/laravel-ivanodp/MyPets_API/public/index.php/api/adminPanelInfo",
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
    document.getElementById('qrs').innerHTML = data["qrs"];
    /* document.getElementById('photos').innerHTML = data["photos"];
    document.getElementById('appointments').innerHTML = data["appointments"];
    document.getElementById('reports').innerHTML = data["reports"]; */
}

function logoff(){
    window.localStorage.removeItem("token");
    window.location.href="index.html";
}