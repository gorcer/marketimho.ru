<?
class UserManager
{
       var $currentUser = null;

      function getInternalUser()
      {
      $um =  UserManager::get_Instance();
//      var_dump($um->currentUser);
        return($um->currentUser);
      }

      function setInternalUser($user)
      {
      $um =  UserManager::get_Instance();
      $um->currentUser=$user;
//     var_dump($um->currentUser);
//     echo '<br/><br/>';
      }

        function GetCurrentUser()
        {

        $currentUser = UserManager::getInternalUser();



               if ($currentUser!=null){

                        return $currentUser;
                } elseif (isset($_SESSION['imho_user']))
                {
                        $currentUser=$_SESSION['imho_user'];
                        UserManager::setInternalUser($currentUser);
                        return $currentUser;
                }
                elseif (isset($_COOKIE['imho_user_live']))
                {
                        if ($_COOKIE['imho_user_live']!=-1){
                                $current = DbManager::GetUserByGUID(htmlspecialchars($_COOKIE['imho_user_live'],ENT_QUOTES));
                                if ($current!=null&&$current->isActive==1){
                                        $currentUser=$current;
                                        UserManager::setInternalUser($current);
                                        $_SESSION['imho_user']=$currentUser;
                                }
                                return $currentUser;
                        }
                }
                return null;
        }


        function get_Instance()
        {
                static $instance = null;
                if($instance == null)
                        $instance = new UserManager();
                return $instance;
        }
        function UploadImage($user, $file)
        {
                        $config = Config::get_Instance();
            $this->baseFileName = $config->usersPath.$user->get_Login()."/";
                        $out = array();
            if (!file_exists($this->baseFileName))
            {

                mkdir($this->baseFileName,0755,1);
            }
            if ($file['name'] !="")
            {
                                $ext = $file['name'];
                                preg_match('/\S+\.(\S+)$/', $ext, $out);
                                $ext = $out[1];
                                $new_filename = uniqid().uniqid().".".$ext;
                                $imgsize = getimagesize($file['tmp_name']);
                                $img_type = $imgsize[2];
                                $im_width = $imgsize[0];
                                $im_height = $imgsize[1];
                                if (intval($im_width)>0 && intval($im_height)>0 && intval($im_width)<intval($config->max_photowidth) && ($img_type == 1 or $img_type == 2))
                                {
                                        move_uploaded_file($file['tmp_name'], $this->baseFileName.'tmp_'.$new_filename);
                                        MinmizeImageFile($this->baseFileName.'tmp_'.$new_filename,$this->baseFileName.$new_filename,800,800, true);
                                        CreateTumbnail($this->baseFileName.$new_filename,$this->baseFileName.'small_'.$new_filename,256);
                                        unlink($this->baseFileName.'tmp_'.$new_filename);
                                        return $new_filename;
                }
                                return "";
                        }
            return "";
        }


                function DeleteImage($user, $file)
        {
                        $config = Config::get_Instance();
            $this->baseFileName = $config->usersPath.$user->get_Login()."/";
            if (file_exists($this->baseFileName))
            {
                unlink($this->baseFileName.$file);
            }
                        return "";
        }

        function get_CommentsPerPage()
        {
                return 10;
        }

        function SaveUser($u)
        {

          DbManager::UpdateUser($u);
          UserManager::setInternalUser($u);
          $_SESSION['imho_user']=$u;
          if (isset($_COOKIE['imho_user_live'])) $_COOKIE['imho_user_live']=$u;

        }

        function RefreshUserOptions()
        {
         $currentUser = UserManager::getInternalUser();
         $currentUser->options = DbManager::getUserParams($currentUser->id);
         UserManager::setInternalUser($currentUser);
        }


        function getCurUserOption($name)
        {
         $currentUser = UserManager::getInternalUser();

         if (!$currentUser) return('');
         if (!isset($currentUser->options[$name])) return('');

         return($currentUser->options[$name]);
        }

        function getUserCity()
        {


		if ((isset($_SESSION['def_city_id'])) && ($_SESSION['def_city_id']!=0))
		$nav_city_id = intval($_SESSION['def_city_id']);
		else
		{


        $curUser=UserManager::GetCurrentUser();
        $cfg = Config::get_Instance();

            if ($curUser)
            {
            $nav_city_id=$curUser->city_id;
            $nav_city_name = $curUser->city;
            }
            else
            {
               $nav_city_id=(isset($_SESSION['aimw_def_city']))?$_SESSION['aimw_def_city']:0;
            }



            if ( (intval($nav_city_id)==0) || (intval($nav_city_id)==-1))
            {
               $nav_city_id=$cfg->default_city_id;
            }

         }

            return($nav_city_id);

        }
}
?>