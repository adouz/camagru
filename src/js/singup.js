var user = document.forms["register"]["uname"];
var pass = document.forms["register"]["passwd"];
var mail = document.forms["register"]["email"];
var submit = document.forms["register"]["submit"];
var error = document.getElementById("error");

user.addEventListener("blur", checkuser);
pass.addEventListener("blur", checkpass);
mail.addEventListener("blur", checkmail);

var remail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
var repass = /^(?=.*[a-zA-Z])(?=.*[0-9])(?=.{8,})/;
var reuser = /^[a-zA-Z0-9]{5,}$/;

function checkuser() {
    if (user.value == ""){
        user.style.border = "1px solid red";
        error.textContent = "Username is required";
        return false;
    }else if (!reuser.test(user.value)){
        user.style.border = "1px solid red";
        error.textContent = "Please Entre a valid Username(only alphanumeric characters and more than 5 characters)";
        return false;
    }else{
        user.style.border = "1px solid green";
        error.textContent = "";
        return true;
    }
}

function checkpass() {
    if (pass.value == ""){
        pass.style.border = "1px solid red";
        error.textContent = "Password is required";
        return false;
     }else if(!repass.test(pass.value)){
        pass.style.border = "1px solid red";
        error.textContent = "Your Password must contain at least: 8 characters, 1 alphabetical character, 1 numeric character.";
        return false;
    }else{
        pass.style.border = "1px solid green";
        error.textContent = "";
        return true;
    }
}

function checkmail() {
    if (mail.value == ""){
        mail.style.border = "1px solid red";
        error.textContent = "E-mail is required";
        return false;
    }else if(!remail.test(mail.value)){
        mail.style.border = "1px solid red";
        error.textContent = "Please Entre a valid E-mail";
        return false;
    }else{
        mail.style.border = "1px solid green";
        error.textContent = "";
        return true;
    }
}

