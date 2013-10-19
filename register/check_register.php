<?
$incadd = "";
if(!file_exists("dbmanager.php"))
{
        $incadd = "../";
}
include_once($incadd."dbmanager.php");
$dbmanager = DbManager::get_Instance();

if(isset($_GET['login'])){
                $login = $_GET['login'];
                $login = htmlspecialchars($login,ENT_QUOTES);
                $isExist = '0';
                $user = new User();
                $user->set_Login($login);
				$user->set_Email("useremail");
                $e = $dbmanager->IsUserExists($user);

                if ($e!=null){
                        $isExist = $e;
                }
                // Returning data as xml
        echo '<?xml version="1.0"?>';
                echo '<isExist>'.$isExist.'</isExist>';
        exit;
}else{
        echo "No success";

}
?>