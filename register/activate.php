<?
include_once("../config/common.php");
$incadd = "../lib/";

include_once($incadd."DBManager.inc");
$dbmanager = DbManager::get_Instance();

if(isset($_REQUEST['login'])&&(isset($_REQUEST['conf']))){
        $login = $_REQUEST['login'];
        $conf = $_REQUEST['conf'];


        $login = htmlspecialchars($login,ENT_QUOTES);
        $conf = htmlspecialchars($conf,ENT_QUOTES);
        $config = Config::get_Instance();

  session_start();
        if ($dbmanager->ActivateUser($login,$conf)){


        $_SESSION['mem_cntnt'] = "Спасибо! Ваша учетная запись активирована!";
        Header('Location: '.$config->get_PublicUrl());
        }
        else
        {
        $_SESSION['mem_cntnt'] = "Ошибка в активации учётной записи. Не верный код.";
        Header('Location: '.$config->get_PublicUrl());
        }

}
?>