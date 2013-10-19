<?
include_once("config/common.php");
include_once("lib/DBManager.inc");
session_start();

//      print_r($_SESSION['imho_user']);


$dbmanager = DbManager::get_Instance();
$login = null;
$pass = null;

$action = "";
if (isset($_REQUEST["proc"])){
        $action = $_REQUEST["proc"];
}

if ($action == "forget"){
        $login = htmlspecialchars($_REQUEST["login"],ENT_QUOTES);
    $email = htmlspecialchars($_REQUEST["email"],ENT_QUOTES);
    $user = $dbmanager->GetUserByLogin($login);
        if ($user && $user->id != 0) {
                SendInformation($user);
                $_SESSION["error_login_forget"]="Информация отправлена Вам нам email!";
        } else {
                $user = $dbmanager->GetUserByEmail($email);
                if ($user && $user->id != 0) {
                        SendInformation($user);
                        $_SESSION["error_login_forget"]="Информация отправлена Вам нам email!";
                } else {
                        $_SESSION["error_login_forget"]="Пользователя с таким логином/email  не существует!";
                }
        }
        header("Location: ".$_SERVER['HTTP_REFERER']);
} else {
if (!isset($_SESSION["imho_user"])&&(isset($_REQUEST["login"])))
{
    $login = htmlspecialchars($_REQUEST["login"],ENT_QUOTES);
    $pass = htmlspecialchars($_REQUEST["pwd"],ENT_QUOTES);
    $user = $dbmanager->GetUserByLoginPass($login,$pass);

        if (($user!=null)&&($user->id!=0)){
                        if ($user->isBlocked==1){
                                $_SESSION["imho_user"]=null;
                                $_SESSION["error_login_check"]="Ваш аккаунт заблокирован!";
                                header("Location: loginpage.php");
                        } else {
                $_SESSION["imho_user"]=$user;
                if (isset($_REQUEST['remind']))
                if ($_REQUEST['remind']==1 && $user!=null)
                {
                        $_COOKIE['imho_user_live']=$user->guid;
                        setcookie('imho_user_live',$user->guid,time()+3600*24*31);
                }
                                header("Location: ".$_SERVER['HTTP_REFERER']);
                        }
        } else {
                $_SESSION["imho_user"]=null;
                                $_SESSION["error_login_check"]="Неправильное имя пользователя либо пароль!";
                                header("Location: loginpage.php");
        }

} else {
        header("Location: ".$_SERVER['HTTP_REFERER']);
}
}

function SendInformation($addUser)
{
        $config = Config::get_Instance();
        $email = $config->webmasterEmail;
        $project = $config->projectName;
        $header = 'From: WebMaster <'.$email.'> \r\n Reply-To:  WebMaster <'.$email.'> \r\n';
        $msg = "Здравствуйте, ".$addUser->name."! <br/> Ваши регистрационные данные:<br/>Логин:  ".$addUser->login."<br/>Пароль: ".$addUser->pass."<br/>Имя:    ".$addUser->name."<br/><br/><br/>С уважением,<br/>Администрация ресурса $project";
        $config->sendMail($addUser->email,$config->projectName." - напоминание",$msg,$header);

}
//include_once("index.php");
?>