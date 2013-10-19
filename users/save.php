<?


include_once("../config/common.php");
$incadd = "../";
include_once($incadd."lib/DBManager.inc");
include_once($incadd."users/usermanager.php");
include_once($incadd."lib/uMemoryMgr.inc");
session_start();
$dbmanager = DbManager::get_Instance();
$umanager = UserManager::get_Instance();
$MemoryMgr = TMemoryMgr::get_Instance();

$config = Config::get_Instance();

$action = $_REQUEST["action"];
$curUser = $umanager->GetCurrentUser();
$isAdmin = false;
if (($curUser != null)&&(($curUser->isAdmin))){
        $isAdmin = true;
}

switch ($action)
{


        case "edit":
        {
          $id = intval($_REQUEST["id"]);
          $user = $dbmanager->GetUserByID($id);

        //$user = new ClassObject ();
        $MemoryMgr->flush();
        $MemoryMgr->setVars($_POST);

        $userName = htmlspecialchars($_REQUEST['user_name'],ENT_QUOTES);
        $phone = htmlspecialchars($_REQUEST['phone'],ENT_QUOTES);
        $city = htmlspecialchars($_REQUEST['city'],ENT_QUOTES);



        if ($userName=='') $MemoryMgr->triggerError('Не указано обязательное поле: Ваше имя');

        $MemoryMgr->judje();

                if (isset($_REQUEST["birthDate"])){
                        $birthDate = $_REQUEST["birthDate"];
                        $birthDate = htmlspecialchars($birthDate,ENT_QUOTES);
                        if ($birthDate!=""){
                                $birthDate = date("Y-m-d",strtotime($birthDate));
                        }

                        $user->birthDate=$birthDate;

                }

                if (isset($_REQUEST["gender"])){
                        $sex = $_REQUEST["gender"];
                        $sex = htmlspecialchars($sex,ENT_QUOTES);
                        $user->sex=$sex;
                }


        $user->name = $userName;
        $user->phone=$phone;


                                      /*
                                                $file = requestFile();
                                                $filename = $umanager->UploadImage($user,$file);
                                                if ($user->get_Filename()!=""){
                                                        if ($filename !="" or $deleteOld){
                                                                $umanager->DeleteImage($user,$user->get_Filename());
                                                                $user->set_Filename($filename);
                                                        }
                                                }
                                                if ($filename!=""){
                                                        $user->set_Filename($filename);
                                                }

                                                                      */


                                                $cityObj=$dbmanager->getCityByName($city);

                                                        if (!$cityObj)
                                                        {
                                                                $cityObj->id=-1;
                                                                $cityObj->name = $city;
                                                        }
                                                        $user->city=$cityObj;

                                                        $user->city_id=$cityObj->id;
                                                        $user->city_name=$cityObj->name;

                                                        if ($cityObj->id!=-1)
                                                        $user->RegionID=$cityObj->RegionId;


                                                $umanager->SaveUser($user);
                header("Location:".$cfg->get_PublicUrl()."user/".$user->login."");
                                                break;
                }

        }



function requestFile()
{
        return $_FILES['photo'];
}
?>