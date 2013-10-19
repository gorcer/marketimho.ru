<?

require_once ($cfg->mainPath.'/lib/f.inc');

class TVisiter
{

var $id;
var $ip;
var $agent;

        function TVisiter()
        {
                $this->agent = $_SERVER['HTTP_USER_AGENT'];
                $this->ip = $_SERVER['REMOTE_ADDR'];
                $row=sqr('select id from aim_visiter where ip="'.$this->ip.'"');
                if ($row!=false)
                {
                        $this->id=$row['id'];
                }
                else
                {
                $uid=md5(($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
                eqr('insert into aim_visiter(ip,uagent,uid) values("'.$this->ip.'", "'.$this->agent.'","'.$uid.'")');
                $this->id = mysql_insert_id();
                }


        }

        function get_Instance()
        {
                static $instance = null;
                if($instance == null)
                        $instance = new TVisiter();
                return $instance;
        }

      function getActivity($obj_id, $act_type)
      {
       if (isset($_COOKIE['aim_activity_'.$act_type.'_'.$obj_id])) return(true);

       $row=sqr('select id from aim_activity where obj_id='.$obj_id.' and visiter_id='.$this->id.' and type_id='.$act_type);
       if ($row!=false) return(true);

       return(false);

      }

      function CheckActivity($obj_id, $act_type)
      {
       if (isset($_COOKIE['aim_activity_'.$act_type.'_'.$obj_id])) return(true);

       $row=sqr('select id from aim_activity where obj_id='.$obj_id.' and visiter_id='.$this->id.' and type_id='.$act_type);
       if ($row!=false) return(true);

        setcookie('aim_activity'.$act_type.'_'.$obj_id,1,time()+3600*24*31);
        eqr('insert into aim_activity (obj_id, visiter_id, type_id, dtm) values('.$obj_id.','.$this->id.','.$act_type.', "'.date('Y-m-d h:i:s').'")');
        return(false);
      }

}

?>