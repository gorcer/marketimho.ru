<?

header('Content-type: text/html; charset=UTF-8');
session_start();
include_once("config/common.php");
require_once ('lib/DBManager.inc');


DbManager::Connect();
$_GET['tag'] = iconv('utf-8','Windows-1251',$_GET['tag']);

      $tag = htmlspecialchars($_GET['tag'], ENT_QUOTES);
      $Tags = DbManager::getTagsByWords($tag);

      $match=array();
      foreach($Tags as $item)
      {
              $match[]=iconv('Windows-1251','UTF-8',$item->name);

      }


            echo json_encode($match);
            exit;



?>