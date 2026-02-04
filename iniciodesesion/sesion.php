<?php
function iniciarSesion(){
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
}
?>