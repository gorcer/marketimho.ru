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
$msg = "������������, ".$user->name."! <br/> �� ������ ��� ��������� ����� ����������� �� ����� $project <br/>���� ��������������� ������:<br/>�����:  ".$user->login."<br/>������: ".$user->pass."<br/>���:    ".$user->name."<br/><br/><br/>��� ��������� ������ �������� ��� ���������� ����� �� ���� �� ������:<br/><a href='".$config->get_PublicUrl()."/register/activate.php?login=".$user->login."&conf=$conf'>".$config->get_PublicUrl()."/register/activate.php?login=".$user->login."&conf=$conf</a><br/><br/><br/>� ���������,<br/>������������� ������� $project";
$config->sendMail($user->email,$config->projectName." - ��������� ��������",$msg,$header);

ob_start();
?><p class='txt'>������������, <?= $user->name?>! �� email <?=$user->email ?> ������� ������ ��� ��������� ������� ������!<br/>
���� �� �� �����-�� �������� �� �������� ������, ������� <a href='send_again.php?login=<?=$user->login ?>&conf=<?=$conf ?>'>�����</a> � ������ ����� ���������� ��� ��������.
</p><?
$_SESSION['mem_cntnt'] = ob_get_contents();
   ob_end_clean();


Header('Location: '.$config->get_PublicUrl());