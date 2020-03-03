$(document).ready(function(){
    $urlParams = new URLSearchParams(window.location.search);
    $param = $urlParams.toString();
    $petid = $param.split("id=").pop();
    send($petid);
})

function send (){
    $.ajax({
        type: "GET",
        url: "http://localhost:8888/laravel-ivanodp/MyPets_API/public/index.php/api/getQRData/"+$petid,
        success:function(data){
            console.log(data);
            fillData(data);
        },
        error: function(result) {
            console.log("error");
        }

    });
}

function fillData(data)
{
    document.getElementById('petname').innerHTML = data[0]["name"];
    document.getElementById('ownername').innerHTML = data[0]["fullName"];
    document.getElementById('tel').innerHTML = data[0]["tel_Number"];
    document.getElementById('mail').innerHTML = data[0]["email"];
    document.getElementById('avatar').src = data[0]["photo"]
    document.getElementById("telhref").href="tel:"+data[0]["tel_Number"]; 
    document.getElementById("mailhref").href="mailto:"+data[0]["email"]+"?subject=He%20encontrado%20a%20tu%20mascota"; 
}
