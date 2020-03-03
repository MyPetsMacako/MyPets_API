$(document).ready(function(){
    url_base = 'http://localhost:8888/laravel-ivanodp/MyPets_API/public/index.php'
    var data = [];
})

getData();

function getData (){
    $token = window.localStorage.getItem("token");
    $.ajax({
        type: "GET",
        url: "http://localhost:8888/laravel-ivanodp/MyPets_API/public/index.php/api/showAppointmentsData",
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", $token);
        },
        success:function(data){
            console.log("success");
            createTable(data)
        },
        error: function(result) {
            console.log("error");
        }

    });
}

function createTable (data){
    console.log("creando tabla...");
    for(var i=0; i<data.length; i++){
        lista = document.getElementById("tableData");
                var tr = document.createElement("tr");
                var columna1 = document.createElement("th")
                var appointmentid = data[i]["id"];
                columna1.innerHTML = data[i]["id"];
                var columna2 = document.createElement("th")
                columna2.innerHTML = data[i]["pet_id"];
                var columna3 = document.createElement("th")
                columna3.innerHTML = data[i]["date"];
                var columna4 = document.createElement("th")
                columna4.innerHTML = data[i]["title"];
                var columna5 = document.createElement("button")
                columna5.innerHTML = "•••";
                columna5.setAttribute("class", "btn btn-info dropdown-toggle");
                columna5.setAttribute("type", "button");
                columna5.setAttribute("id", "dropdownMenuButton");
                columna5.setAttribute("data-toggle", "dropdown");
                columna5.setAttribute("aria-haspopup", "true");
                columna5.setAttribute("aria-expanded", "false");

                var div = document.createElement("div");
                div.setAttribute("class", "dropdown-menu");
                div.setAttribute("aria-labelledby", "dropdownMenuButton");
                

                var a1 = document.createElement("a");
                a1.setAttribute("class", "dropdown-item text-danger");
                a1.setAttribute('onclick', 'destroy('+appointmentid+')');
                a1.innerHTML = "Eliminar";

                lista.appendChild(tr);
                tr.appendChild(columna1);
                tr.appendChild(columna2);
                tr.appendChild(columna3);
                tr.appendChild(columna4);
                tr.appendChild(columna5);
                columna5.appendChild(div);
                div.appendChild(a1);
    }
}

function destroy(appointmentid) {
    console.log(appointmentid)

    $.ajax({
        type: "DELETE",
        url: "http://localhost:8888/laravel-ivanodp/MyPets_API/public/index.php/api/deleteAppointment/"+appointmentid,
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", $token);
        },
        success:function(response){
            //alert("Usuario eliminado correctamente");
            if(alert("Cita eliminada correctamente")){}
            else    window.location.reload(); 
        },
        error: function(result) {
            console.log("error")
        }

    });
}