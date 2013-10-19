<?

echo 'Clear cache started....';

	 $dir = opendir (getcwd().'/cache');
        chdir (getcwd().'/cache');

$i=0;
        while ($f = readdir ($dir)) {
        	$i++;
                if (is_file ($f) && basename ($f) != '.htaccess') unlink ($f);
        }
        closedir ($dir);

        echo $i.' deleted...Ok.<br/>'

?>