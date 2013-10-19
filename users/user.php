<?
class User
{
        /// id
        var $id = null;
        var $city = null;

        function User()
        {
                $this->city = new City();
        }

        function get_ID()
        {
                return intval($this->id);
        }

        function set_ID($id)
        {
                $this->id = intval($id);
        }


                /// guid
        var $guid = null;

        function get_GUID()
        {
                return htmlspecialchars($this->guid, ENT_QUOTES);
        }

        function set_GUID($guid)
        {
                $this->guid = htmlspecialchars($guid, ENT_QUOTES);
        }
                /// name
        var $name = null;

        function get_Name()
        {
                return $this->name;
        }

        function set_Name($name)
        {
                $this->name = $name;
        }


                /// login
        var $login = null;

        function get_Login()
        {
                return $this->login;
        }

        function set_Login($login)
        {
                $this->login = $login;
        }


        /// pass
        var $pass = null;

        function get_Pass()
        {
                return $this->pass;
        }

        function set_Pass($pass)
        {
                $this->pass = $pass;
        }


        var $isAdmin = null;

        function get_IsAdmin()
        {
                return intval($this->isAdmin);
        }

        function set_IsAdmin($isAdmin)
        {
                $this->isAdmin = $isAdmin;
        }


        var $isEditor = null;

        function get_IsEditor()
        {
                return intval($this->isEditor);
        }

        function set_IsEditor($isEditor)
        {
                $this->isEditor = $isEditor;
        }

                var $isActive = null;

        function get_IsActive()
        {
                return intval($this->isActive);
        }

        function set_IsActive($isActive)
        {
                $this->isActive = $isActive;
        }


                var $isBlocked = null;

        function get_IsBlocked()
        {
                return intval($this->isBlocked);
        }

        function set_IsBlocked($isBlocked)
        {
                $this->isBlocked = $isBlocked;
        }



        function get_City()
        {
                return $this->city;
        }


        function set_City($city)
        {
                $this->city = $city;
        }


                var $region = null;

        function get_Region()
        {
                return $this->region;
        }

        function set_Region($region)
        {
                $this->region = $region;
        }

                var $regionID = null;

        function get_RegionID()
        {
                return $this->regionID;
        }

        function set_RegionID($regionID)
        {
                $this->regionID = $regionID;
        }

        var $sendNews = null;

        function get_SendNews()
        {
                return $this->sendNews;
        }

        function set_SendNews($sendNews)
        {
                $this->sendNews = $sendNews;
        }

                var $phone = null;

        function get_Phone()
        {
                return $this->phone;
        }

        function set_Phone($phone)
        {
                $this->phone = $phone;
        }

                var $email = null;

        function get_Email()
        {
                return $this->email;
        }

        function set_Email($email)
        {
                $this->email = $email;
        }

                var $filename = null;

        function get_Filename()
        {
                return $this->filename;
        }

        function set_Filename($filename)
        {
                $this->filename = $filename;
        }

                var $sex = null;

        function get_Sex()
        {
                return $this->sex;
        }

        function set_Sex($sex)
        {
                $this->sex = $sex;
        }

                var $birthDate = null;

        function get_BirthDate()
        {
                return $this->birthDate;
        }

        function set_BirthDate($birthDate)
        {
                $this->birthDate = $birthDate;
        }

                 var $mercyCnt = null;

        function get_mercyCnt()
        {
                return $this->mercyCnt;
        }

        function set_mercyCnt($mercyCnt)
        {
                $this->mercyCnt = $mercyCnt;
        }

                         var $complaintCnt = null;

        function get_complaintCnt()
        {
                return $this->complaintCnt;
        }

        function set_complaintCnt($complaintCnt)
        {
                $this->complaintCnt = $complaintCnt;
        }


}

?>