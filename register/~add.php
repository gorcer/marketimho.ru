<?
$incadd = "";
if(!file_exists("dbmanager.php"))
{
        $incadd = "../";
}

include_once($incadd."dbmanager.php");
session_start();

$dbmanager = DbManager::get_Instance();

function IsValidLogin($name)
{
        $str = "zxcvbnmasdfghjklqwertyuiop0123456789";
        $res = true;
        for ($i=0;$i<strlen($name);$i++){
                if (!stristr($str,$name[$i])){
                        $res = false;
                        break;
                }
        }
        return $res;
}

function requestUser()
{

        $user = new User();

        $userName = htmlspecialchars($_REQUEST['user_name'],ENT_QUOTES);
        $login = htmlspecialchars($_REQUEST['login'],ENT_QUOTES);
        $pass = htmlspecialchars($_REQUEST['pwd'],ENT_QUOTES);
        $cpass = htmlspecialchars($_REQUEST['confirm_pwd'],ENT_QUOTES);
        $email = htmlspecialchars($_REQUEST['email'],ENT_QUOTES);
        $phone = htmlspecialchars($_REQUEST['phone'],ENT_QUOTES);
        $city = htmlspecialchars($_REQUEST['city'],ENT_QUOTES);
                $sendNews = intval($_REQUEST['sendNews']);
                if ($pass!=$cpass) $pass =-1;

        $user->set_Name($userName);
        $user->set_Login($login);
        $user->set_Pass($pass);
        $user->set_Email($email);
        $user->set_Phone($phone);
        $user->set_City($city);
        $user->set_SendNews($sendNews);
        return $user;
}

if (isset($_SESSION['scode'])){
        $code = $_REQUEST['code'];
        $rightCode = $_SESSION['scode'];
        unset($_SESSION['scode']);
        if ($code!=$rightCode){
                ?>
        <p>������� ������ ���.</p>
        <?
        exit;
        }
}



$addUser = requestUser();
$dupUser = $dbmanager->IsUserExists($addUser);

if ($addUser->get_Pass()==-1)
{
        ?>
        <p>������������� ������ �� ��������� � �������.</p>
        <?
        exit;
}

if (!isValidLogin($addUser->get_Login())){
        ?>
                <p>����� �������� ������������ �������!</p>
        <?
    exit;
}

if ($dupUser==null){
        $conf = $dbmanager->AddUser($addUser);
} else {
        ?>
                <p>������������ � ����� ������� ��� ����������!</p>
        <?
        exit;
}

$config = Config::get_Instance();
$project = $config->projectName;
$email = $config->webmasterEmail;
$header = 'From: WebMaster <'.$email.'> \r\n Reply-To:  WebMaster <'.$email.'> \r\n';
$msg = "������������, ".$addUser->get_Name()."! <br/> �� ������ ��� ��������� ����� ����������� �� ����� $project <br/>���� ��������������� ������:<br/>�����:  ".$addUser->get_Login()."<br/>������: ".$addUser->get_Pass()."<br/>���:    ".$addUser->get_Name()."<br/><br/><br/>��� ��������� ������ �������� ��� ���������� ����� �� ���� �� ������:<br/><a href='http://www.auto-imho.ru/register/activate.php?login=".$addUser->get_Login()."&conf=$conf'>http://www.auto-imho.ru/register/activate.php?login=".$addUser->get_Login()."&conf=$conf</a><br/><br/><br/>� ���������,<br/>������������� ������� $project";
$config->sendMail($addUser->get_Email(),$config->projectName." - ��������� ��������",$msg,$header);
?>
<p>������������, <?= $addUser->get_Name()?>! �� ��� email ������� ������ ��� ��������� ������� ������!</p>
<?
?>