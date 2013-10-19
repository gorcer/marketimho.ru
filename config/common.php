<?
class Config
{
        function get_Instance()
        {
                static $instance = null;

                if($instance == null)
                        $instance = new Config();

                return $instance;
        }

        function Config()
        {
                // Database connection properties
                $this->projectName = "MARKETIMHO.RU";
                $this->dbInfo = new BDInfo();
/*
                $this->dbInfo->hostName = "localhost";         // Hostname or IP-address of server where database hosted
                $this->dbInfo->name = "autoimho";                // Database` name
                $this->dbInfo->userName = "user";                // User`s name
                $this->dbInfo->password = "skyowner";                // Password
*/

                $this->dbInfo->hostName = "";         // Hostname or IP-address of server where database hosted
                $this->dbInfo->name = "";                // Database` name
                $this->dbInfo->userName = "";                // User`s name
                $this->dbInfo->password = "";                // Password

			  $this->AutoImhoDomain= 'http://auto-imho.ru/'; // Parent project
              $this->mainDomain = 'marketimho.ru/';

              $this->publicUrl = 'http://'.$this->mainDomain;


				// Path to local path
               $this->mainPath = '';

                $this->usersPath = $this->mainPath.'/content/users/';
                $this->default_city_id=163;


                $this->css_file = $this->publicUrl.'template/style.css';


                $this->css_file = $this->publicUrl.'template/style.css';

                $this->max_photofilesize = 1*1024*1024;
                $this->max_photowidth = 1290;
                $this->PhotoPerRow=3;
                $this->max_photoname_length=50;
                $this->webmasterEmail = 'webmaster@marketimho.ru';

                $this->max_execution_time = 1;
                $this->execution_path_log = $this->mainPath.'\logs/';
                                $this->sms_user_id = 1897;



        }


        // Do not edit below


                function get_PublicUrl()
                {
                        if (isset($_SESSION['domain'])){
                                return 'http://'.$_SESSION['domain'].$this->mainDomain;
                        }
                        return $this->publicUrl;
                }

        var $mailHost = "";

        function getMailHost()
        {
            return $this->mailHost;
        }

        function SendMail($to,$subject, $txt, $header)
        {
//$txt = convert_cyr_string($txt, "w", "k");
//$subject = convert_cyr_string($subject, "w", "k");

        $header  = 'MIME-Version: 1.0' . "\r\n";
        $header .= 'Content-type: text/html; charset=Windows-1251' . "\r\n";
         $header .= "X-Mailer: PHP/".phpversion()."\r\n";
        $header.='From: WebMaster <'.$this->webmasterEmail.'>';

            mail($to,$subject,$txt, $header);
        }

                var $dbInfo;

        function get_BdInfo()
        {
                return $this->dbInfo;
        }


}

class BDInfo
{
        var $hostName;
        var $name;
        var $userName;
        var $password;

        function get_HostName()
        {
                return $this->hostName;
        }

        function get_Name()
        {
                return $this->name;
        }

        function get_UserName()
        {
                return $this->userName;
        }

        function get_Password()
        {
                return $this->password;
        }
}
?>
