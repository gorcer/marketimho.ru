<?


class TLogMgr
{
        function get_Instance()
        {
                static $instance = null;
                if($instance == null)
                        $instance = new MyWayDbManager();
                return $instance;
        }

        function add($id, $action_name, $obj_name, $desc='')
        {
                $u = UserManager::GetCurrentUser();
                if ($u==false)
                $u->id=-1;
                $dt = date('Y-m-d H:i:s');
                $ip=$_SERVER['REMOTE_ADDR'];

                eqr('insert into aimw_log(description, user_id, obj_id, obj_name, action_name, create_dtm, ip)
                     value ("'.$desc.'",'.$u->id.', '.$id.', "'.$obj_name.'", "'.$action_name.'", "'.$dt.'", "'.$ip.'")
                ');


        }


}

?>
