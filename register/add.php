<?
include_once("../config/common.php");
        $incadd = "../lib/";
include_once($incadd."DBManager.inc");
//include_once($incadd."mywaydbmanager.php");
include_once($incadd."uMemoryMgr.inc");
session_start();

$dbmanager = DbManager::get_Instance();
//$MWdbmanager = MyWayDbManager::get_Instance();
$MemoryMgr = TMemoryMgr::get_Instance();

function IsValidLogin($name)
{
        $str = "zxcvbnmasdfghjklqwertyuiop0123456789_";
        $res = true;
        for ($i=0;$i<strlen($name);$i++){
                if (!stristr($str,$name[$i])){
                        $res = false;
                        break;
                }
        }
        return $res;
}



        //$user = new ClassObject ();
        $MemoryMgr->flush();
        $MemoryMgr->setVars($_POST);

        $userName = htmlspecialchars($_REQUEST['user_name'],ENT_QUOTES);
        $login = htmlspecialchars($_REQUEST['login'],ENT_QUOTES);
        $pass = htmlspecialchars($_REQUEST['pwd'],ENT_QUOTES);
        $cpass = htmlspecialchars($_REQUEST['confirm_pwd'],ENT_QUOTES);
        $email = htmlspecialchars($_REQUEST['email'],ENT_QUOTES);
        $phone = htmlspecialchars($_REQUEST['phone'],ENT_QUOTES);
        $city = htmlspecialchars($_REQUEST['city'],ENT_QUOTES);
        $sendNews = intval($_REQUEST['SendNews']);

        if ($city =='') $MemoryMgr->triggerError('�� ������� ������������ ����: �����');
        if ($userName=='') $MemoryMgr->triggerError('�� ������� ������������ ����: ���� ���');
        if ($login=='') $MemoryMgr->triggerError('�� ������� ������������ ����: �����');
        if ($pass=='') $MemoryMgr->triggerError('�� ������� ������������ ����: ������');
        if ($cpass=='') $MemoryMgr->triggerError('�� ������� ������������ ����: ������������� ������');
        if ($email=='') $MemoryMgr->triggerError('�� ������� ������������ ����: Email');
        if (!isValidLogin($login)) $MemoryMgr->triggerError('����� �������� ������������ �������');
        if ($pass!=$cpass) $MemoryMgr->triggerError('�� ����������� ����� ������������� ������');


        if (isset($_SESSION['scode'])){
                $code = $_REQUEST['code'];
                $rightCode = $_SESSION['scode'];
                unset($_SESSION['scode']);
                if ($code!=$rightCode){
                $MemoryMgr->triggerError('�� ����� ������ ����������� ���');
                }
                }


         $MemoryMgr->judje();


                if (isset($_REQUEST["birthDate"])){
                        $birthDate = $_REQUEST["birthDate"];
                        $birthDate = htmlspecialchars($birthDate,ENT_QUOTES);
                        if ($birthDate!=""){
                                $birthDate = date("Y-m-d",strtotime($birthDate));
                        }
                        $user->birthDate=$birthDate;
                }

                if (isset($_REQUEST["gender"])){
                        $sex = $_REQUEST["gender"];
                        $sex = htmlspecialchars($sex,ENT_QUOTES);
                        $user->sex=$sex;
                }

        $user->name = $userName;
        $user->login = $login;
        $user->pass=$pass;
        $user->email=$email;
        $user->phone=$phone;

        $user->SendNews=$sendNews;

        $dupUser = $dbmanager->IsUserExists($user);
        if ($dupUser!=null){$MemoryMgr->triggerError('������������ � ������ ������� � (���) email� ��� ����������!');}

        if ($user->phone=='123456')
        $MemoryMgr->triggerError('�� ����� ������ �������!');

        $MemoryMgr->judje();


        $cityObj=$dbmanager->getCityByName($city);

        if (!$cityObj)
        {
                $cityObj->id=-1;
                $cityObj->name = $city;
        }
        $user->city=$cityObj;




        if ($cityObj->id!=-1)
        $user->RegionID=$cityObj->RegionId;



       $conf = $dbmanager->AddUser($user);




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
?>
