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
        case 'agreement.html':$title=' �������';break;
        case 'regulation.html':$title=' ������������';break;
        case 'about.html':$title=' � �������';break;
        case 'send_claim.html':$title=' ������ �� ������';break;
        case 'penalty.html':$title=' ����������� � ���������������� ���������������';break;
        case 'specnum.html':$title=' ���������� ���������� �� ����. �������';break;
}

$pagename='content/articles/'.$pagename;





include('template/index.html');

?>