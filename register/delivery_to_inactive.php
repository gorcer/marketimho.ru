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

            $msg = "Здравствуйте, ".$user->get_Name()."! <br/> Вы заполнили форму регистрации на сайте $project. <br/>Однако спустя продолжительное время вы так и неактивировали аккаунт. На случай, если письмо к вам не дошло высылаем повторно ваши регистрационные данные:<br/>Логин:  ".$user->get_Login()."<br/>Пароль: ".$user->get_Pass()."<br/>Имя:    ".$user->get_Name()."<br/><br/><br/>Для активации Вашего аккаунта Вам необходимо зайти на сайт по ссылке:<br/><a href='http://www.auto-imho.ru/register/activate.php?login=".$user->get_Login()."&conf=".$user->get_GUID()."'>http://www.auto-imho.ru/register/activate.php?login=".$user->get_Login()."&conf=".$user->get_GUID()."</a><br/><br/><br/>С уважением,<br/>Администрация ресурса $project";
//            echo $msg;
//            echo '<br/>';
            $config->sendMail($user->get_Email(),$config->projectName." - активация аккаунта",$msg,$header);


            }


?>