<?


class TFileMgr {



       function getRateFileHTML($comment)
       {

        $cfg = Config::get_Instance();
        $fn =($comment->get_Filename());
        $fn_url = 'content/cities/'.$comment->city->id.'/'.$comment->street->id.'/';
        $fn_path=$cfg->mainPath.$fn_url;
        $fn_small= '';
        $imgfn='';


        if (($fn!='') and (file_exists($fn_path.$fn)))
        {

        $fn_small= $fn_url.'small_'.$fn;

        $imgfn = '
        <a href="'.$cfg->get_PublicUrl().$fn_url.$fn.'" target="_blank">
        <img src="'.$fn_small.'" border="0" align="left" class="rateimg"/>
        </a>
        ';
        }

        return($imgfn);
       }

        function processPath($path)
        {
             if (!file_exists($path))
                {
                           mkdir($path,0755,1);
                }
        }

        function DeleteRateFile($cityid, $streetid, $file)
        {
        $config = Config::get_Instance();
                $fn = $config->mainPath.'content/cities/'.$cityid.'/'.$streetid.'/'.$file;
                $fn_small = $config->mainPath.'content/cities/'.$cityid.'/'.$streetid.'/small_'.$file;

                unlink($fn);
                unlink($fn_small);
        }

        function UploadRateImage($cityid, $streetid, $file)
        {

                $config = Config::get_Instance();

                $FileName = $config->mainPath.'content/cities/'.$cityid;
                TFileMgr::processPath($FileName);

                $FileName.='/'.$streetid;
                TFileMgr::processPath($FileName);
                $FileName.='/';

                $out = array();

                if ($file['name'] !="")
                {
                                        $ext = $file['name'];
                                        preg_match('/\S+\.(\S+)$/', $ext, $out);
                                        $ext = $out[1];
                                        $new_filename = uniqid().".".$ext;
                                        $imgsize = getimagesize($file['tmp_name']);
                                        $img_type = $imgsize[2];
                                        if (intval($imgsize[0])>0 && intval($imgsize[1])>0 && ($img_type == 1 or $img_type == 2))
                                        {
                                                move_uploaded_file($file['tmp_name'], $FileName.'tmp_'.$new_filename);

                                                MinmizeImageFile($FileName.'tmp_'.$new_filename,$FileName.$new_filename,800,800, true, 80);
                                                CreateTumbnail($FileName.$new_filename,$FileName.'small_'.$new_filename,80);

                                                unlink($FileName.'tmp_'.$new_filename);
                                                return $new_filename;
                                        }
                    return "";
                }
                return "";
        }

        }

?>