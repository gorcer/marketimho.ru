<?
include_once("lib/functions.php");
session_start();

/*
if (isset($_SESSION['scode'])){
        $num = $_SESSION['scode'];
}
else {*/
$num = generateNumber();
$_SESSION['scode'] = $num;
//}

Header("Content-type: image/png");
$img = imagecreate('50', '20');
$back = imagecolorallocate($img, 255, 255 ,255);
$black = imagecolorallocate($img, 99, 99, 99);
imageline($img, 0, 0, 49, 0, $black);
imageline($img, 0, 0, 0, 19 , $black);
imageline($img, 0, 19, 49, 19 , $black);
imageline($img, 49, 0, 49, 19 , $black);
imagestring($img,3,5,2,$num,$black);
imagepng($img);
?>