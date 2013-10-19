<?


class DbListIterator
{
        var $list = null;

        var $current = null;

        function DbListIterator($list)
        {
                if($this->list = $list)
                        $this->get_Next();
                else
                        die("argument does not be null");
        }

        function get_Length()
        {
                return mysql_num_rows($this->list);
        }

        function get_Next()
        {


                if($this->current = mysql_fetch_array($this->list))
                        return $this->current;
                return null;
        }

        function MoveNext()
        {
                $current = $this->current;

                $this->get_Next();

                return $current;
        }

        function get_Current()
        {
                return $this->current;
        }
}


class Debug
{
        var $startTime = null;
        function Debug()
        {
                $mtime = microtime();
                //Разделяем секунды и миллисекунды
                $mtime = explode(" ",$mtime);
                //Составляем одно число из секунд и миллисекунд
                $mtime = $mtime[1] + $mtime[0];
                //Записываем стартовое время в переменную
                $this->startTime = $mtime;
        }

        var $endTime = null;
        function EndDebug()
        {
                $mtime = microtime();
                $mtime = explode(" ",$mtime);
                $mtime = $mtime[1] + $mtime[0];
                //Записываем время окончания в другую переменную
                $this->endTime = $mtime;
                //Вычисляем разницу
                $totaltime = ($this->endTime - $this->startTime);
                return $totaltime;
        }
}


class DbAccessor
{
        var $connect = null;

        function DbAccessor()
        {
			$config = Config::get_Instance();
			$dbInfo = $config->get_BdInfo();
			$this->connect =
				mysql_connect($dbInfo->get_HostName(), $dbInfo->get_UserName(), $dbInfo->get_Password())
					or die("По техническим причинам сайт временно не работает.");
				mysql_query("SET NAMES 'cp1251'");
				mysql_select_db($dbInfo->get_Name())
				or die("Could not select database '".$dbInfo->get_Name()."'");
        }

        function get_Instance()
        {
                static $instance = null;
                /*
				if($instance == null)
                        $instance = new DbAccessor();*/
                return $instance;
        }

		var $debug = null;

		var $debugTime = 0;

		function startDebug()
		{
			$this->debug = new Debug();
			$this->debugTime = 0;
		}

		function endDebug()
		{
			if ($this->debug!=null){
				$this->debugTime = $this->debugTime + $this->debug->EndDebug();
			}
			return $this->debugTime;
		}

        function ExecuteList($sql)
        {
                        $start = getmicrotime();

                        if($result = mysql_query($sql) or die (mysql_error()))
                        {
                                $resList = new DbListIterator($result);

                                $totaltime = (getmicrotime() - $start);
                                $config = Config::get_Instance();
                                $duration = $config->max_execution_time;
                                if ($totaltime>=$duration){
                                        add_to_exec_log($sql.". Время выполнения:$totaltime сек.");
                                }
                return $resList;
                        }
            return null;
        }

        function ExecuteValue($sql)
        {
                $start = getmicrotime();
                                $result = new DbListIterator(mysql_query($sql));
                                $totaltime = (getmicrotime() - $start);
                                $config = Config::get_Instance();
                                $duration = $config->max_execution_time;
                                if ($totaltime>=$duration){
                                        add_to_exec_log($sql.". Время выполнения:$totaltime сек.");
                                }
                //????????????????????????????????
                // бля, ну какого здесь не выводится ошибка запроса, если она возникает????????????????????????
                //??????????????????????????????????

                if($test = $result->get_Current()){
                                        return $test[0];
                                }
                else
                                        return null;
        }

        function ExecuteNonQuery($sql)
        {
			$start = getmicrotime();
			mysql_query($sql) or die (mysql_error());
			$totaltime = (getmicrotime() - $start);
			$config = Config::get_Instance();
			$duration = $config->max_execution_time;
			if ($totaltime>=$duration){
				add_to_exec_log($sql.". Время выполнения:$totaltime сек.");
			}
        }


		function GetObjectsList($rs,$list = array())
		{
			//$list = array();
            if($rs)
			{
				while($rs->get_Current())
				{
					$row = $rs->MoveNext();
                    $list[count($list)] = $row;
				}
			}
			return $list;
		}
}

?>