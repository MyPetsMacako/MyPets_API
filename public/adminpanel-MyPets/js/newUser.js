$(document).ready(function(){
    url_base = 'http://localhost:8888/laravel-ivanodp/MyPets_API/public/index.php'
    document.getElementById("button").onclick = required;
    var passMatch = false;

    $requestedView = window.localStorage.getItem("RequestedView");
    $requestedUserId = window.localStorage.getItem("RequestedUserId");

    console.log($requestedUserId);

    if($requestedView == "edit"){
        editMode($requestedUserId)
    } else {
        createMode()
    }
})



function get_vars(){
    var fullname = document.getElementById("fullname").value;
    var nickname = document.getElementById("nickname").value;
    var email = document.getElementById("email").value;
    var password1 = document.getElementById("password1").value;
    var password2 = document.getElementById("password2").value;

    var data = {
        "fullname" : fullname,
        "nickname" : nickname,
        "email" : email,
        "password1" : password1,
        "password2" : password2
    }
    return data;
}

function get_edit_vars(){
    var fullname = document.getElementById("fullname").value;
    var nickname = document.getElementById("nickname").value;
    var email = document.getElementById("email").value;

    var data = {
        "fullName" : fullname,
        "nickname" : nickname,
        "email" : email,
    }
    return data;
}

function verifyPasswords(){
    var password1 = document.getElementById("password1").value;
    var password2 = document.getElementById("password2").value;

    if(password1==password2){
        passMatch = true;
        return passMatch;
    } else {
        passMatch = false;
        document.getElementById('warning').style.display = 'block';
        document.getElementById("warning").className = "text-danger";
        document.getElementById('warning').innerHTML = "Las contraseñas no coinciden";
        return passMatch;
    }
}


function send(){
    var data = get_vars();
    $token = window.localStorage.getItem("token");
    var passMatch = verifyPasswords();

    if (passMatch == true){
        $.ajax({
            type: "POST",
            url: "http://mypetsapp.es/api/register",
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", $token);
            },
            data: data,
            success:function(response){
                document.getElementById('warning').style.display = 'block';
                document.getElementById("warning").className = "text-success";
                document.getElementById('warning').innerHTML = "Usuario registrado correctamente";
                setTimeout(function () {
                    window.location.href="mainPanel%20-%20users.html";
                 }, 3000);
            },
            error: function(result) {
                document.getElementById('warning').style.display = 'block';
                document.getElementById("warning").className = "text-danger";
                document.getElementById('warning').innerHTML = result.responseJSON.message;
            }
    
        });
    }else{
        document.getElementById('warning').style.display = 'block';
        document.getElementById("warning").className = "text-danger";
        document.getElementById('warning').innerHTML = result.responseJSON.message;
    }
    
}

function edit(){
    var data = get_edit_vars();
    $token = window.localStorage.getItem("token");
    console.log($token);
        $.ajax({
            type: "POST",
            url: "http://mypetsapp.es/api/adminUpdate/"+$requestedUserId,
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", $token);
            },
            data: data,
            success:function(response){
                document.getElementById('warning').style.display = 'block';
                document.getElementById("warning").className = "text-success";
                document.getElementById('warning').innerHTML = "Usuario actualizado correctamente";
                setTimeout(function () {
                    window.location.href="mainPanel%20-%20users.html";
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
        if (data["fullname"] == "" || data["nickname"] == "" || data["email"] == ""){
            alert("Completa todos los campos");
            return;
        } else {
            edit();
        }
    } else {
        console.log("Button Create");
        if (data["fullname"] == "" || data["nickname"] == "" || data["email"] == "" || data["password1"] == "" || data["password2"] == ""){
            alert("Completa todos los campos");
            return;
        } else {
            send();
        }
    }

    
}

function editMode($requestedUserId){
    console.log("Modo edición de usuario");
    document.getElementById('title').innerHTML = "Editar usuario";
    document.getElementById('button').innerHTML = "Guardar cambios";
    document.getElementById('passRow').style.display = "none";

    getRequestedData($requestedUserId);
}

function getRequestedData($requestedUserId){
    $token = window.localStorage.getItem("token");
    $.ajax({
        type: "GET",
        url: "http://mypetsapp.es/api/requestedUserInfo/"+$requestedUserId,
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", $token);
        },
        success:function(data){
            console.log("Datos obtenidos");
            fillTextFields(data)
        },
        error: function(result) {
            console.log("Error en la obtención de datos");
        }

    });
}

function fillTextFields(data){
    document.getElementById('fullname').value = data["fullName"];
    document.getElementById('nickname').value = data["nickname"];
    document.getElementById('email').value = data["email"];
}

function createMode(){
    console.log("Modo creación de usuario");
    document.getElementById('title').innerHTML = "Registrar nuevo usuario:";
    document.getElementById('button').innerHTML = "Crear usuario";
}