<?
include_once("../config/common.php");     
  $incadd = "../lib/";


include_once($incadd."DBManager.inc");
session_start();


if (isset($_REQUEST['conf']))
$conf = htmlspecialchars($_REQUEST['conf'], ENT_QUOTES);
else
return;

if (!isset($_REQUEST['conf'])) return;

$login = htmlspecialchars($_REQUEST['login']);

$addUser = $dbmanager->GetUserByLogin($login);

if ($addUser->guid!=$_REQUEST['conf']) return;


$config = Config::get_Instance();
$project = $config->projectName;
$email = $config->webmasterEmail;
$header = 'From: WebMaster <'.$email.'> \r\n Reply-To:  WebMaster <'.$email.'> \r\n';
$msg = "Здравствуйте, ".$user->name."! <br/> Вы только что заполнили форму регистрации на сайте $project <br/>Ваши регистрационные данные:<br/>Логин:  ".$user->login."<br/>Пароль: ".$user->pass."<br/>Имя:    ".$user->name."<br/><br/><br/>Для активации Вашего аккаунта Вам необходимо зайти на сайт по ссылке:<br/><a href='".$config->get_PublicUrl()."/register/activate.php?login=".$user->login."&conf=$conf'>".$config->get_PublicUrl()."/register/activate.php?login=".$user->login."&conf=$conf</a><br/><br/><br/>С уважением,<br/>Администрация ресурса $project";
$config->sendMail($user->email,$config->projectName." - активация аккаунта",$msg,$header);

ob_start();
?><p class='txt'>Здравствуйте, <?= $user->name?>! На email <?=$user->email ?> выслано письмо для активации учетной записи!<br/>
Если вы по каким-то причинам не получили письмо, нажмите <a href='send_again.php?login=<?=$user->login ?>&conf=<?=$conf ?>'>здесь</a> и письмо будет отправлено вам повторно.
</p><?
$_SESSION['mem_cntnt'] = ob_get_contents();
   ob_end_clean();


Header('Location: '.$config->get_PublicUrl());