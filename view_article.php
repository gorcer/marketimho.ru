<?

if (!isset($_REQUEST['name']))
$pagename = '404.html';
else
{
$pagename=$_REQUEST['name'];
$pagename=htmlspecialchars($pagename).'.html';



if (!file_exists('content/articles/'.$pagename))
$pagename = '404.html';
}

switch ($pagename)
{
        case 'agreement.html':$title=' Правила';break;
        case 'regulation.html':$title=' Рекомендации';break;
        case 'about.html':$title=' О проекте';break;
        case 'send_claim.html':$title=' Заявки на номера';break;
        case 'penalty.html':$title=' Привлечение к административной ответственности';break;
        case 'specnum.html':$title=' Публикация информации по спец. номерам';break;
}

$pagename='content/articles/'.$pagename;





include('template/index.html');

?>