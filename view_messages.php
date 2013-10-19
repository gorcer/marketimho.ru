<?

if (!isset($_REQUEST['id']))
$pagename='view_messages.html';
else
{
 $pagename='view_message.html';
}

$title='Сообщения';

include('template/index.html');

?>
