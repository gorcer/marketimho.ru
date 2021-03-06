<?

class MailMgr
{
        function Send($txt, $subj, $email)
        {

        $config = Config::get_Instance();
        $project = $config->projectName;
        $msg = $txt.'
        <br/><br/>
        � ���������, <br/>
        ������������� �������<br/>  <a href="'.$config->get_PublicUrl().'">'.$project.'</a>   <br/>
        ';
        $config->sendMail($email,$config->projectName.": ".$subj,$msg,'');
        }

        function Notify_NewFriendship($User, $friend)
        {
        $config = Config::get_Instance();

                $msg='
                ������������, '.$friend->name.'<br/><br/>
                ������������ '.$User->login.' ('.$User->name.') ����� �������� ��� � ������.<br/>
                ��� ����, ����� �������� ������������ � ������ ��� ���������� �� ������ �������� �� ������:<br/>
                '.$config->get_PublicUrl().'myfriends
                ';

                $subj=$User->login.'('.$User->name.') ����� �������� ��� � ������.';

                MailMgr::Send($msg,$subj,$friend->email);

        }

        function Notify_ApproveFriendship($User, $friend)
        {
        $config = Config::get_Instance();

                $msg='
                ������������, '.$friend->name.'<br/><br/>
                ������������ '.$User->login.' ('.$User->name.') ���������� � ���� �������.<br/>
                ��� ����, ����� ���������� ������ ������ �������� �� ������:<br/>
                '.$config->get_PublicUrl().'myfriends
                ';

                $subj=$User->login.'('.$User->name.') ���������� � ���� �������.';

                MailMgr::Send($msg,$subj,$friend->email);

        }


          function InviteUser($email)
          {
            $config = Config::get_Instance();

            $User = UserManager::GetCurrentUser();

           $msg='
           ������������,<br/><br/>

           �� ������� ��� ������ ������, ��� ������������  '.$User->login.' ('.$User->name.')
           ������ ��� ����������� ������� ������� � ������� <a href="'.$config->get_PublicUrl().'">'.$config->projectName.'</a>.<br/>

           ���� �� ��������, ��� ��� ������ � �� ������� � ���� ������������� �� ������� ��� ���������.<br/>
           <br/>           <br/>
           ��� ���� ����� ������������������ �� �����, �������� �� ������:<br/>
           <a href="'.$config->get_PublicUrl().'service/register">'.$config->get_PublicUrl().'service/register</a>
           <br/>
           <br/>
           ---- <br/>
           � �������<br/>
           <br/>
           ��� ���������� ������ ������� ��������� ���� ������ �� ���������� �������� � ��������.<br/>
           �������� � ��������� ��� ������� �� ������� ���������� ���.<br/>
           �������� � ���� ���������� �� ���� � �� ������� ��������� ����������� �� ����� ��� �������� �������� � ����.<br/>
           ���������� ���������� �� ��������� ����� ������ �����.<br/><br/>
           �� ���� �������� ��� ��� ��������� ������� �� ����� ������������� ��� �� ����� �������� � ���������� ������ �� ����� �� ������ � ����� ������.<br/>
           ';

            $subj=$User->login.' ('.$User->name.') ���������� ��� �������������� � �������.';

            MailMgr::Send($msg,$subj,$email);

          }


        function Notify_DeclineFriendship($User, $friend)
        {
        $config = Config::get_Instance();

                $msg='
                ������������, '.$friend->name.'<br/><br/>
                ������������ '.$User->login.' ('.$User->name.') ��������� � ���� �������.<br/>
                ��� ����, ����� ���������� ������ ������ �������� �� ������:<br/>
                '.$config->get_PublicUrl().'myfriends
                ';

                $subj=$User->login.'('.$User->name.') ��������� � ���� �������.';

                MailMgr::Send($msg,$subj,$friend->email);

        }
}

?>