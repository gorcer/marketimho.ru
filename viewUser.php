<?

$pagename='user_page.html';

  if (isset($_REQUEST["loginName"])){
                $name = $_REQUEST["loginName"];
$name = iconv('UTF-8', 'cp1251', $name);   
                $name = htmlspecialchars($name,ENT_QUOTES);
                }
$title = 'Пользователь - '.$name;
include('template/index.html');

?>