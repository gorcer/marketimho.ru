<?
session_start();

unset($_SESSION["imho_user"]);
 setcookie('imho_user_live',-1,-1);

 Header("Location: ".$_SERVER['HTTP_REFERER']);

?>