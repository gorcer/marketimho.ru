<?


class TMemoryMgr
      {

               var $Errors;
               var $Vars;


        function get_Instance()
        {
                static $instance = null;

                if($instance == null)
                if (isset($_SESSION['aimw_MM']))
                {
                 $instance =$_SESSION['aimw_MM'];
                }
                else
                $instance = new TMemoryMgr();

                return $instance;
        }

              function triggerError($error)
              {
                $this->Errors[]=$error;
              }

              function flush()
              {
                 $this->Errors = array();
                 $this->Vars = array();
              }

              function getVars()
              {
                return($this->Vars);
              }

              function setVars($values)
              {
                $this->Vars=$values;
              }

              function haveErrors()
              {
                      return(sizeof($this->Errors)>0);
              }

              function Save()
              {
                      $_SESSION['aimw_MM'] = $this;
              }

              function judje()
              {
                      if  ($this->haveErrors())
                      {
                      $this->Save();

                    Header('Location:'.$_SERVER['HTTP_REFERER']);
                      die();
                      }
              }





      }


?>
