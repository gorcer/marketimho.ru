<?

function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function is_cached ($filename, $time) {
         $cfg =Config::get_Instance();
        $file = $cfg->mainPath."/cache/".$filename;
        if (file_exists ($file)) {

                if (filemtime ($file) > (time() - $time)) return true;
                else return false;
        }
        else {
                        return false;
                }
}


//
// РЎРѕР·РґР°РЅРёРµ С„Р°Р№Р»Р° РІ РєР°С‚Р°Р»РѕРіРµ include/cache РїРѕРґ РёРјРµРЅРµРј $filename Рё РєРѕРЅС‚РµРЅС‚РѕРј $content

function cache_file ($filename, $content) {
         $cfg =Config::get_Instance();
        $file = $cfg->mainPath."/cache/".$filename;
        if (file_exists ($file)) unlink ($file);

        $cache_file = fopen ($file, 'w');
        flock ($cache_file, LOCK_EX);
        fwrite ($cache_file, $content);
        flock ($cache_file, LOCK_UN);
        fclose ($cache_file);
}

//
// Р¤СѓРЅРєС†РёСЏ РѕС‡РёС‰РµРЅРёСЏ РєРµС€Р°

function clear_cache () {
         $cfg =Config::get_Instance();
        $dir = opendir ($cfg->mainPath.'/cache');
        chdir ($cfg->mainPath.'/cache');

        while ($f = readdir ($dir)) {
                if (is_file ($f) && basename ($f) != '.htaccess') unlink ($f);
        }

        closedir ($dir);

        return true;

}

/// Маска удаления файлов
function clear_cache_by_mask ($mask) {
        $cfg =Config::get_Instance();
        $dir = opendir ($cfg->mainPath.'/cache');
        chdir ($cfg->mainPath.'/cache');
    while ($f = readdir ($dir)) {
                if (is_file ($f) && (stristr(basename($f),$mask) == true)) {
                        unlink ($f);
                }
        }
    closedir ($dir);
    return true;
}


function add_to_exec_log($msg){
        $cfg =Config::get_Instance();
        $filename = date("Y_m_d").".txt";
    $file = $cfg->execution_path_log.$filename;
    $cache_file = fopen ($file, 'a');
    flock ($cache_file, LOCK_EX);
    fwrite ($cache_file, "[".date("d.m.Y H:i:s")."] - ".$msg."\r\n");
    flock ($cache_file, LOCK_UN);
    fclose ($cache_file);
}


function convertCyrToLat($cyr) {
/*
        $replace = array("a","b","d","e","k","m","h","o","p","c","t","y","x");
        $search2 = array("Р°","РІ","Рґ","Рµ","Рє","Рј","РЅ","Рѕ","СЂ","СЃ","С‚","Сѓ","С…");
        $search3 = array("Рђ","Р’","Р”","Р•","Рљ","Рњ","Рќ","Рћ","Р ","РЎ","Рў","РЈ","РҐ");


        $res = str_replace($search2,$replace,$cyr);
        $res = str_replace($search3,$replace,$res);

        $cyr = strtolower($res);
        $search = array("а","в","д","е","к","м","н","о","р","с","т","у","х");


        $res = str_replace($search,$replace,$cyr);

        return $res;     */
         $cyr = strtolower($cyr);
         $replace = array("a","b","d","e","k","m","h","o","p","c","t","y","x");
         $search = array("а","в","д","е","к","м","н","о","р","с","т","у","х");
         $name = str_replace($search,$replace,$cyr);

         return($name);


}

Function DateDiff ($interval,$date1,$date2) {
    // получает количество секунд между двумя датами
    $timedifference = $date2 - $date1;

    switch ($interval) {
        case 'w':
            $retval = bcdiv($timedifference,604800);
            break;
        case 'd':
            $retval = bcdiv($timedifference,86400);
            break;
        case 'h':
            $retval =bcdiv($timedifference,3600);
            break;
        case 'n':
            $retval = bcdiv($timedifference,60);
            break;
        case 's':
            $retval = $timedifference;
            break;

    }
    return $retval;

}

function isValidNum($number,$region) {
        if (!isValidRegion($region)) {
                return false;
        }
        //if (isValidMentosNumber($number)||isValidPublicNumber($number)) {
        if (isValidBusNumber($number)){
                        return true;
                } elseif (isValidTransitNumber($number)){
                        return true;
                } elseif (isValidMentosNumber($number)) {
                        return true;
        } elseif (isValidPublicNumber($number)) {
                        return true;
                }
        return false;
}

function isValidRegion($region){
        if (strlen($region)>1 && strlen($region)<4){
                return true;
        }
        return false;
}

function isValidBusNumber($number){
        $number=strtolower($number);
                if (preg_match('/^[a-ehkmoptxy]{2}[0-9]{3}$/', $number)){
                //if (ereg("([a-e,h,k,m,o,p,t,x,y]{2})([0-9]{3})", $number)){
                        return true;
        }
        return false;
}

function isValidTransitNumber($number){
        $number=strtolower($number);
                if (preg_match('/^[a-ehkmoptxy]{2}[0-9]{4}$/', $number)){
                        return true;
        }
        return false;
}

function isValidMentosNumber($number){
                $number=strtolower($number);
                if (preg_match('/^[a-ehkmoptxy]{1}[0-9]{4}$/', $number)){
        //if (ereg("([a-e,h,k,m,o,p,t,x,y]{1})([0-9]{4})", $number)){
                return true;
        }
        return false;
}

function isValidVladGaiNumber($number){

           $regnum = $number->get_Number();
           $region = $number->get_RegionID();

                $regnum=strtolower($regnum);
        if (ereg("m([0-9]{3})bk", $regnum) && ($region==25 || $region==125) ){
                return true;
        }
        return false;
}

function isValidPublicNumber($number){
                $number=strtolower($number);
                if (preg_match('/^[a-ehkmoptxy]{1}[0-9]{3}[a-ehkmoptxy]{2}$/', $number)){
        //if (ereg("([a-e,h,k,m,o,p,t,x,y]{1})([0-9]{3})([a-e,h,k,m,o,p,t,x,y]{2})", $number)){
                return true;
        }
        return false;
}

function generateNumber(){
                $numLength=6;
                $result = "";
                for ($i=1;$i<$numLength+1;$i++){
                        $result .= rand(0,9);
                }
        return $result;
}

function getDateTime($date, $format = "d.m.y H:i:s")
{
        if ($date!=null){
                return date($format,strtotime($date));
        }
        return $date;
}

//$date в формате "d.m.Y H:i:s"
//$interval по умолчанию в часах
function compareTimeStampWithNow($date,$interval = "h")
{
        $date_time_string = $date;
        // Разбиение строки в 3 части - date, time
        $dt_elements = explode(' ',$date_time_string);
        // Разбиение даты
        $date_elements = explode('.',$dt_elements[0]);
        // Разбиение времени
        $time_elements =  explode(':',$dt_elements[1]);
        $createTimeStamp = mktime($time_elements[0], $time_elements[1],$time_elements[2], $date_elements[1],$date_elements[0], $date_elements[2]);

        $date_time_string = date("d.m.Y H:i:s");
        // Разбиение строки в 3 части - date, time
        $dt_elements = explode(' ',$date_time_string);
        // Разбиение даты
        $date_elements = explode('.',$dt_elements[0]);
        // Разбиение времени
        $time_elements =  explode(':',$dt_elements[1]);

        $nowTimeStamp = mktime($time_elements[0], $time_elements[1],$time_elements[2], $date_elements[1],$date_elements[0], $date_elements[2]);
        //print_r(date("d.m.Y h:i:s",$createTimeStamp)."-".date("d.m.Y h:i:s",$nowTimeStamp));

        $diffInHour = DateDiff($interval,$createTimeStamp,$nowTimeStamp);
        return $diffInHour;
}


function SavePhoto($n)
{
    /*
        $photo_id=false;
    if (is_uploaded_file($_FILES['foto']['tmp_name'][$n]))
    if ($_FILES['foto']['size'][$n]<1)
        {
                $_SESSION['im_error']['foto']='Недопустимый размер фото.';
        }
    else
        {
        eqr('insert into im_photo(person_id, owner_id, create_dtm) values ('.$this->person_id.', '.$this->user_id.', "'.date('Y-m-d h:i:s').'")');
        $photo_id=mysql_insert_id();
                $fotoArt=$photo_id;
                $imginfo = getimagesize($_FILES['foto']['name'][$n]);
                $rasch=strtolower(substr($imageinfo['mime'] ,strpos($imageinfo['mime'] ,"/")+1));
                if (($rasch!='jpeg') and ($rasch!='gif')) return;
                $tmp =getcwd()."/photo/".$this->person_uin."/tmp_".$fotoArt.".".$rasch;
        if (move_uploaded_file($_FILES['foto']['tmp_name'][$n],$tmp)!=true) {
                        $this->err_list['foto']='Ошибка копирования файла!';
                }
        else
        {
                        if ($imginfo[0]>1290)
            {
                unlink(getcwd()."/photo/".$this->person_uin."/tmp_".$fotoArt.".".$rasch);
                eqr('delete from im_photo where id='.$photo_id);
                return(false);
            }

            @ MinmizeImageFile($tmp,getcwd()."/photo/".$this->person_uin."/".$fotoArt.".jpg",640,640, '');
            @ CreateTumbnail($tmp,getcwd()."/photo/".$this->person_uin."/small_".$fotoArt.".jpg",169);
                        unlink(getcwd()."/photo/".$this->person_uin."/tmp_".$fotoArt.".".$rasch);
                        if (!(file_exists(getcwd()."/photo/".$this->person_uin."/".$fotoArt.".jpg")or
                        file_exists(getcwd()."/photo/".$this->person_uin."/small_".$fotoArt.".jpg")))
                        {
                                eqr('delete from im_photo where id='.$photo_id);
                                $photo_id=false;
                        }
        }
    }
        return($photo_id);*/
   }

?>