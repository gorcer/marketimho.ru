<?


$incadd = "";
if(!file_exists("dbmanager.php"))
{
        $incadd = "../";
}

include_once($incadd."dbmanager.php");

            $dbmanager = DbManager::get_Instance();
            $ulist=$dbmanager->GetUserList_notActive();

            $config = Config::get_Instance();
            $project = $config->projectName;
            $email = $config->webmasterEmail;
            $header = 'From: WebMaster <'.$email.'> \r\n Reply-To:  WebMaster <'.$email.'> \r\n';


            foreach($ulist as  $user)
            {

            $msg = "������������, ".$user->get_Name()."! <br/> �� ��������� ����� ����������� �� ����� $project. <br/>������ ������ ��������������� ����� �� ��� � �������������� �������. �� ������, ���� ������ � ��� �� ����� �������� �������� ���� ��������������� ������:<br/>�����:  ".$user->get_Login()."<br/>������: ".$user->get_Pass()."<br/>���:    ".$user->get_Name()."<br/><br/><br/>��� ��������� ������ �������� ��� ���������� ����� �� ���� �� ������:<br/><a href='http://www.auto-imho.ru/register/activate.php?login=".$user->get_Login()."&conf=".$user->get_GUID()."'>http://www.auto-imho.ru/register/activate.php?login=".$user->get_Login()."&conf=".$user->get_GUID()."</a><br/><br/><br/>� ���������,<br/>������������� ������� $project";
//            echo $msg;
//            echo '<br/>';
            $config->sendMail($user->get_Email(),$config->projectName." - ��������� ��������",$msg,$header);


            }


?>