<?

class MailMgr
{
        function Send($txt, $subj, $email)
        {

        $config = Config::get_Instance();
        $project = $config->projectName;
        $msg = $txt.'
        <br/><br/>
        С уважением, <br/>
        Администрация ресурса<br/>  <a href="'.$config->get_PublicUrl().'">'.$project.'</a>   <br/>
        ';
        $config->sendMail($email,$config->projectName.": ".$subj,$msg,'');
        }

        function Notify_NewFriendship($User, $friend)
        {
        $config = Config::get_Instance();

                $msg='
                Здравствуйте, '.$friend->name.'<br/><br/>
                Пользователь '.$User->login.' ('.$User->name.') хочет добавить вас в друзья.<br/>
                Для того, чтобы добавить пользователя в друзья или отказаться от дружбы пройдите по ссылке:<br/>
                '.$config->get_PublicUrl().'myfriends
                ';

                $subj=$User->login.'('.$User->name.') хочет добавить вас в друзья.';

                MailMgr::Send($msg,$subj,$friend->email);

        }

        function Notify_ApproveFriendship($User, $friend)
        {
        $config = Config::get_Instance();

                $msg='
                Здравствуйте, '.$friend->name.'<br/><br/>
                Пользователь '.$User->login.' ('.$User->name.') согласился с вами дружить.<br/>
                Для того, чтобы посмотреть список друзей пройдите по ссылке:<br/>
                '.$config->get_PublicUrl().'myfriends
                ';

                $subj=$User->login.'('.$User->name.') согласился с вами дружить.';

                MailMgr::Send($msg,$subj,$friend->email);

        }


          function InviteUser($email)
          {
            $config = Config::get_Instance();

            $User = UserManager::GetCurrentUser();

           $msg='
           Здравствуйте,<br/><br/>

           Вы плучили это письмо потому, что пользователь  '.$User->login.' ('.$User->name.')
           послал вам приглашение принять участие в проекте <a href="'.$config->get_PublicUrl().'">'.$config->projectName.'</a>.<br/>

           Если вы считаете, что это ошибка и не знакомы с этим пользователем то удалите это сообщение.<br/>
           <br/>           <br/>
           Для того чтобы зарегистрироваться на сайте, пройдите по ссылке:<br/>
           <a href="'.$config->get_PublicUrl().'service/register">'.$config->get_PublicUrl().'service/register</a>
           <br/>
           <br/>
           ---- <br/>
           О проекте<br/>
           <br/>
           Наш социальный проект призван экономить ваши деньги на ежедневных покупках в магазине.<br/>
           Совершив в очередной раз покупку не спешите выкидывать чек.<br/>
           Занесите с него информацию на сайт и вы узнаете насколько переплатили за товар или наоборот выиграли в цене.<br/>
           Информация собирается на основании ранее вбитых чеков.<br/><br/>
           По мере развития вас как участника проекта мы будем предоставлять вам всё более полезные и интересные отчёты по ценам на товары в вашем городе.<br/>
           ';

            $subj=$User->login.' ('.$User->name.') приглашает вас присоединиться к проекту.';

            MailMgr::Send($msg,$subj,$email);

          }


        function Notify_DeclineFriendship($User, $friend)
        {
        $config = Config::get_Instance();

                $msg='
                Здравствуйте, '.$friend->name.'<br/><br/>
                Пользователь '.$User->login.' ('.$User->name.') отказался с вами дружить.<br/>
                Для того, чтобы посмотреть список друзей пройдите по ссылке:<br/>
                '.$config->get_PublicUrl().'myfriends
                ';

                $subj=$User->login.'('.$User->name.') отказался с вами дружить.';

                MailMgr::Send($msg,$subj,$friend->email);

        }
}

?>