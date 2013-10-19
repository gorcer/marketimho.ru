<?

class NotifyMgr
{
        function notifyMail($uid, $mid)
        {
        $row=sqr_obj("select last_active from aim_users where id=".$uid);

        $tm = time()-strtotime($row->last_active);

        if ($tm<300) return;

        $dbmanager = DbManager::get_Instance();
                $user = $dbmanager->getUser($uid);
                $curUser = UserManager::GetCurrentUser();


            $config = Config::get_Instance();
            $project = $config->projectName;
            $email = $config->webmasterEmail;
            $header = 'From: WebMaster <'.$email.'> \r\n Reply-To:  WebMaster <'.$email.'> \r\n';

            $msg = "Здравствуйте, ".$user->name."! <br/>
                    <br/>
                    Вам пришло новое сообщение от пользователя <a href='".$config->publicUrl."user/".$curUser->login."'>".$curUser->login."</a>.<br/>
<br/>
                    Для того, чтобы посмотреть сообщение пройдите по ссылке<br/>
                    <a href='".$config->publicUrl."message/".$mid."'>".$config->publicUrl."message/".$mid."</a>
                    <br/>
                    <br/><br/><br/>С уважением,<br/>Администрация ресурса $project


            ";
//            echo $msg;
//            echo '<br/>';
            $config->sendMail($user->email,$config->projectName." - новое сообщение",$msg,$header);

        }
}

?>
