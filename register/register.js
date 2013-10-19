var registerFile = domainName+'register/check_register.php';
var ajaxObjects = new Array();
var ajaxIndex = null;

function doLoginCheck()
{
        var loginInput = document.getElementById('loginInput').value;
        ajaxIndex = ajaxObjects.length;
        ajaxObjects[ajaxIndex] = new sack();
        ajaxObjects[ajaxIndex].requestFile = registerFile + '?login=' + loginInput;
        ajaxObjects[ajaxIndex].onCompletion = function(){ onLoginCheck(); };
        ajaxObjects[ajaxIndex].runAJAX();
}

function onLoginCheck()
{
        var xml = ajaxObjects[ajaxIndex].response;
    xml = xml.replace(/\n/gi,'');
    var reg = new RegExp("^.*?<isExist>(.*?)<.*$","gi");
    var isExist = xml.replace(reg,'$1');
        var div = document.getElementById('divMessage');
        if (isExist!='0') {
                div.style.display = 'block';
                div.innerHTML='Логин существует!';
                document.getElementById('loginInput').focus();
        } else {
                div.style.display = 'none';
        }
}