$(document).ready(function(){
    document.getElementById("logoff").onclick = logoff;
    document.getElementById("button").onclick = required;
    var passMatch = false;

    $requestedView = window.localStorage.getItem("RequestedView");
    $requestedPetId = window.localStorage.getItem("RequestedPetId");

    console.log($requestedPetId);
    console.log($requestedView);

    if($requestedView == "edit"){
        editMode($requestedPetId)
    } else {
        createMode()
    }
})

function get_vars(){
    var user_id = document.getElementById("user_id").value;
    var name = document.getElementById("name").value;
    var selectedOption = document.getElementById("selectedOption");
    var strSelectedOption = selectedOption.options[selectedOption.selectedIndex].value;
    var breed = document.getElementById("breed").value;
    var colour = document.getElementById("colour").value;
    var weight = document.getElementById("weight").value;
    var birth = document.getElementById("birth").value;
    var image = document.getElementById("photo").src;

    var data = {
        "user_id" : user_id,
        "name" : name,
        "species" : strSelectedOption,
        "breed" : breed,
        "color" : colour,
        "weight" : weight,
        "birth_date" : birth,
        "image" : image
    }
    console.log(data["image"]);
    return data;
}

function get_edit_vars(){
    var name = document.getElementById("name").value;
    var selectedOption = document.getElementById("selectedOption");
    var strSelectedOption = selectedOption.options[selectedOption.selectedIndex].value;
    var breed = document.getElementById("breed").value;
    var colour = document.getElementById("colour").value;
    var weight = document.getElementById("weight").value;
    var birth = document.getElementById("birth").value;

    var data = {
        "name" : name,
        "species" : strSelectedOption,
        "breed" : breed,
        "colour" : colour,
        "weight" : weight,
        "birth_date" : birth
    }
    return data;
}


function send (){
    var data = get_vars();
    console.log(data)
    $token = window.localStorage.getItem("token");
    $.ajax({
        type: "POST",
        url: "http://mypetsapp.es/api/petsRegister",
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", $token);
        },
        data: data,
        success:function(response){
            document.getElementById('warning').style.display = 'block';
            document.getElementById("warning").className = "text-success";
            document.getElementById('warning').innerHTML = "Mascota registrada correctamente";
            setTimeout(function () {
                window.location.href="mainPanel-pets.html";
                }, 3000);
        },
        error: function(result) {
            document.getElementById('warning').style.display = 'block';
            document.getElementById("warning").className = "text-danger";
            document.getElementById('warning').innerHTML = result.responseJSON.message;
        }

    });


}

function edit(){
    var data = get_edit_vars();
    $token = window.localStorage.getItem("token");
        $.ajax({
            type: "POST",
            url: "http://mypetsapp.es/api/updatePetsForAdmin/"+$requestedPetId,
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", $token);
            },
            data: data,
            success:function(response){
                document.getElementById('warning').style.display = 'block';
                document.getElementById("warning").className = "text-success";
                document.getElementById('warning').innerHTML = "Mascota actualizada correctamente";
                setTimeout(function () {
                    window.location.href="mainPanel-pets.html";
                 }, 3000);
            },
            error: function(result) {
                document.getElementById('warning').style.display = 'block';
                document.getElementById("warning").className = "text-danger";
                document.getElementById('warning').innerHTML = result.responseJSON.message;
            }

        });

        /*document.getElementById('warning').style.display = 'block';
        document.getElementById("warning").className = "text-danger";
        document.getElementById('warning').innerHTML = result.responseJSON.message;*/

}


function required(){
    var data = get_vars();

    if($requestedView == "edit"){
        console.log("Button Edit");
        if (data["name"] == "" || data["species"] == "" || data["breed"] == "" || data["color"] == "" || data["weight"] == ""){
            alert("Completa todos los campos");
            return;
        } else {
            edit()
        }
    } else {
        console.log("Button Create");
        if (data["name"] == "" || data["species"] == "" || data["breed"] == "" || data["color"] == "" || data["weight"] == ""){
            alert("Completa todos los campos");
            return;
        } else {
            send()
        }
    }
}


function editMode($requestedUserId){
    console.log("Modo edición de mascota");
    document.getElementById('title').innerHTML = "Editar mascota:";
    document.getElementById('button').innerHTML = "Guardar cambios";
    document.getElementById('idRow').style.display = "none";

    getRequestedData($requestedUserId);
}

function getRequestedData($requestedUserId){
    $token = window.localStorage.getItem("token");
    $.ajax({
        type: "GET",
        url: "http://mypetsapp.es/api/adminRequestedPetInfo/"+$requestedUserId,
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", $token);
        },
        success:function(data){
            console.log("Datos obtenidos");
            console.log(data)
            fillTextFields(data)
        },
        error: function(result) {
            console.log("Error en la obtención de datos");
        }

    });
}

function fillTextFields(data){
    document.getElementById('name').value = data["name"];
    $selectValue = "";
    switch (data["species"]) {

        case "Perro":
            $selectValue = "1";
            break;

        case "Gato":
            $selectValue = "2";
            break;

        case "Roedor":
            $selectValue = "3";
            break;

        case "Ave":
            $selectValue = "4";
            break;

        case "Reptil":
            $selectValue = "5";
            break;

        default:
            $selectValue = "1";
            break;
    }
    //Picker
    document.getElementById("selectedOption").selectedIndex = $selectValue;
    document.getElementById('breed').value = data["breed"];
    document.getElementById('colour').value = data["color"];
    document.getElementById('weight').value = data["weight"];
    document.getElementById('birth').value = data["birth_date"];

}

function createMode(){
    console.log("Modo creación de mascota");
    document.getElementById('title').innerHTML = "Registrar nueva mascota:";
    document.getElementById('button').innerHTML = "Crear mascota";
}

function logoff(){
    window.localStorage.removeItem("token");
    window.location.href="index.html";
}
