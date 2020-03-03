$(document).ready(function(){
    document.getElementById("logoff").onclick = logoff;
    document.getElementById("button").onclick = required;
})

//var pet_id = window.localStorage.getItem("RequestedPetId");

function get_vars(){

var pet_id = window.localStorage.getItem("RequestedPetId");
    var dateTime = document.getElementById("dateTime").value;
    console.log(dateTime)
    var description = document.getElementById("description").value;

    var data = {
        "pet_id" : pet_id,
        "date" : dateTime,
        "title" : description
    }
    return data;
}


function send (){
    var data = get_vars();
    $token = window.localStorage.getItem("token");
    $.ajax({
        type: "POST",
        url: "http://mypetsapp.es/api/createAppointment",
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", $token);
        },
        data: data,
        success:function(response){
            document.getElementById('warning').style.display = 'block';
            document.getElementById("warning").className = "text-success";
            document.getElementById('warning').innerHTML = "Cita registrada correctamente";
            setTimeout(function () {
                window.location.href="mainPanel-apointments.html";
                }, 3000);
        },
        error: function(result) {
            document.getElementById('warning').style.display = 'block';
            document.getElementById("warning").className = "text-danger";
            document.getElementById('warning').innerHTML = result.responseJSON.message;
        }

    });


}


function required(){
    var data = get_vars();
    if (data["description"] == ""){
        alert("Completa todos los campos");
        return;
    } else {
        send ()
    }
}

function logoff(){
    window.localStorage.removeItem("token");
    window.location.href="index.html";
}
