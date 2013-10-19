<?


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
// Создание файла в каталоге include/cache под именем $filename и контентом $content

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
// Функция очищения кеша

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


function t($word)
{
global $words;
if (isset($words[$word]))
return($words[$word]);
else
return ($word);

}

function getEntityByType($t)
{
switch ($t)
        {
        case 1: return('place');break;
        case 3: return('idea');break;
        case 2: return('report');break;
        case 4: return('photo');break;
        }

}


function CreateTumbnail($image_link, $new_foto,$destSize=200, $quality=100, $type=1)
{

                        $imginfo = getimagesize($image_link);



                        $srcWidth = $imginfo[0];
                         $srcHeight= $imginfo[1];
                        if (($srcWidth==0) || ($srcHeight==0)) return;



if ($type==1)
{

$nw=$destSize;
$nh=$nw*($srcHeight/$srcWidth);


 $x1=0;
  $y1=0;

  $w1=$srcWidth;
  $h1=$srcHeight;

}
elseif ($type=2)
{
  if ($srcWidth>$srcHeight)
  {
  $nw=$destSize;
  $nh=$srcHeight*($nw/$srcWidth);
  }
  else
  {
  $nh=$destSize;
  $nw=$srcWidth*($nh/$srcHeight);
  }

 $x1=0;
  $y1=0;

  $w1=$srcWidth;
  $h1=$srcHeight;
}



//$image_type=substr($image_link,strrpos($image_link,'.')+1);

 $imginfo = getimagesize($image_link);
 $image_type=strtolower(substr($imginfo['mime'] ,strpos($imginfo['mime'] ,"/")+1));

//echo $y1.'<br/>';

switch (strtolower($image_type))
{
        case 'jpg':$src_img = imagecreatefromjpeg($image_link);break;
        case 'jpeg':$src_img = imagecreatefromjpeg($image_link);break;
        case 'gif':$src_img = imagecreatefromgif($image_link);break;
        case 'bmp':$src_img = imagecreatefromwbmp($image_link);break;
        case 'png':$src_img = imagecreatefrompng($image_link);break;
        default: return;
}
//$src_img = imagecreatefromjpeg($image_link);
$dst_img = imagecreatetruecolor($nw, $nh);


imagecopyresampled($dst_img, $src_img,0,0,$x1,$y1, (int)$nw, (int)$nh, $w1, $h1);



imagejpeg($dst_img,$new_foto,$quality);

}

function MinmizeImageFile($image_link, $new_foto, $destWidth, $destHeight,  $setCopyright=false, $quality=100)
{


/*
        $file=DOMAIN_URL.'getimage.php?proc=Resize&imagelink='.$foto_link.'&destWidth='.$nWidth.'&destHeight='.$nHeight;

        $fp=fopen($file,"r");
   //        $txt=fread($fp, filesize ($file));
   $txt='';
   while (!feof ($fp)) {
    $txt.= fgets($fp, 4096);

}

           $fp_e=fopen($new_foto,"w+");
           fwrite($fp_e,$txt);
           //copy($file,$new_foto);*/

//          if (($imginfo = getimagesize($image_link))==false) return;
                        @$imginfo = getimagesize($image_link);



                        $srcWidth = $imginfo[0];
                         $srcHeight= $imginfo[1];
                        if (($srcWidth==0) || ($srcHeight==0)) return;

                        if (($srcWidth<$destWidth) && ($srcHeight<$destHeight))
                        {
                        $destWidth=$srcWidth;
                        $destHeight=$srcHeight;
                        }


if ($srcWidth>$srcHeight)
  {
  $nw=$destWidth;
  $nh=$srcHeight*($nw/$srcWidth);
  }
  else
  {
  $nh=$destHeight;
  $nw=$srcWidth*($nh/$srcHeight);
  }

//$image_type=substr($image_link,strrpos($image_link,'.')+1);

 $imginfo = getimagesize($image_link);
 $image_type=strtolower(substr($imginfo['mime'] ,strpos($imginfo['mime'] ,"/")+1));

//echo $y1.'<br/>';


switch (strtolower($image_type))
{
        case 'jpg':$src_img = imagecreatefromjpeg($image_link);break;
        case 'jpeg':$src_img = imagecreatefromjpeg($image_link);break;
        case 'gif':$src_img = imagecreatefromgif($image_link);break;
        case 'bmp':$src_img = imagecreatefromwbmp($image_link);break;
        case 'png':$src_img = imagecreatefrompng($image_link);break;
        default: return;
}

$new_foto=str_replace('*',$image_type,$new_foto);

//$src_img = imagecreatefromjpeg($image_link);
$dst_img = imagecreatetruecolor($nw, $nh);


imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $nw, (int)$nh, $srcWidth, $srcHeight);


if ($setCopyright)
{
$fsize=8;
$text='www.auto-imho.ru'.$author;



$w = ImageColorAllocate($dst_img, 200, 200, 200);
$y = ImageColorAllocate($dst_img, 255, 180, 0);
$b = ImageColorAllocate($dst_img, 30, 30, 30);

$cfg = Config::get_Instance();


$ar=imagettfbbox($fsize,0,$cfg->mainPath.'template/tahoma.ttf',$text);


//imagefilledrectangle ($dst_img,0,$nh-$fsize+$ar[7]-11,10+3+$ar[2],$nh-$fsize+$ar[3], $b);
imagefilledrectangle ($dst_img,0,$nh-15,17+$ar[2],$nh, $b);

//imagettftext($dst_img,$fsize,0,10,$nh-$fsize-12,$w,MAIN_PATH.'/template/Scrawl.ttf',$text);
imagettftext($dst_img,$fsize,0,5,$nh-5,$w,$cfg->mainPath.'template/tahoma.ttf',$text);

}



imagejpeg($dst_img,$new_foto,$quality);

}


function CropBRs($txt)
{
        $txt=str_replace('<br />','',$txt);
        $txt=str_replace('<br>','',$txt);
        $txt=str_replace('<br/>','',$txt);
        $txt=str_replace('<BR>','',$txt);
        $txt=str_replace('<BR/>','',$txt);
        return($txt);

}


function br2nl($txt)
{
        $txt=str_replace('<br />',chr(13),$txt);
        $txt=str_replace('<br>',chr(13),$txt);
        $txt=str_replace('<br/>',chr(13),$txt);
        $txt=str_replace('<BR>',chr(13),$txt);
        $txt=str_replace('<BR/>',chr(13),$txt);
        return($txt);

}

 function LoadContentFromFile($fn)
{

        $fd = fopen ($fn, "r");
        $ret= fread ($fd, filesize ($fn));
        return ($ret);
}

function isContainWarningSymbols($txt)
{
//$ws='<>&@"'.chr(39);
  if ($txt==preg_replace('/[^�-��-�a-zA-Z0-9�_ .,:;!@%$#^&*()+-=]/', '',$txt))
  return(false);
  else
  return(true);


}

function Mark($l)
{
echo '<b>'.$l.'</b><br>';
}

        function sqr_list($txt)
                 {
         $row=mysql_query($txt) or die (Mark($txt).mysql_error());
         return($row);
        }


function sqr_obj($sql)
{

 $result = mysql_query($sql) or die (Mark($sql).mysql_error());
 if ( ($row = mysql_fetch_object($result))!=false)
  {
  $result = $row;
  }
  else
 $result = false;
 return($result);
}

function sqr_listObj($query)
{
$q =  sqr_list($query);
        $arr=array();
        while(($row=mysql_fetch_object($q))!=false)
        {
                $arr[]=$row;
        }

        return($arr);
}

function sqr_listAssoc($query)
{
$q =  sqr_list($query);
        $arr=array();
        while(($row=mysql_fetch_assoc($q))!=false)
        {
                $arr[]=$row;
        }

        return($arr);
}

function sqr($sql)
{

 $result = mysql_query($sql) or die (Mark($sql).mysql_error());
 if ( ($row = mysql_fetch_array($result, MYSQL_ASSOC))!=false)
  {
  $result = $row;
  }
  else
 $result = false;
 return($result);
}

/**
 * @return void
 * @param string $sql
 * @desc ���������� sql ����
*/
function eqr($sql)
{
 mysql_query($sql) or die (Mark($sql).mysql_error());
}

function Now()
{
return(date('Y-m-d G:i:s'));
}



  function MySQLConnect()
         {
            $ret=false;
            $con = mysql_pconnect(HOST_NAME, USER_NAME, PASSWORD);
       if (!($con === false))
       {
       if (mysql_select_db(DB_NAME, $con) === false) echo('Could not select database: ' . mysql_error());
       $db=$con;
       $ret=true;
       }    else echo ('Could not connect to database: ' . mysql_error());
      return($ret);
            }


   function GetTextFromFile($file)
   {
           $fp=fopen($file,"r");
           $txt=fread($fp, filesize ($file));
           return($txt);
   }


?>
