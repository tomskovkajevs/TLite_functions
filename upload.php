<?

function dumpAssociativeArray($array) {
    $res = '';
    $header = false;
    if (is_array($array) && sizeof($array)) {
        $res .= "<table border=1>\n";
        foreach(@$array as $values) {
            if (!$header) {
                $res .= "<th>" . implode("</th><th>", array_keys($values)) . "</th>\n";
                $header = true;
            }
            $res .= "<tr>\n";
            foreach($values as $key => $value) {
                $res .= "<td>" . ($value != '' ? $value : "&nbsp;") . "</td>";
            }
            $res .= "</tr>\n";
        }
        $res .= "</table>\n";
    }
    return $res;
}

function upl_err_paz($ko)
{
//echo "ERROR: $ko<BR>";
}

function uploadTo($path, $overwrite=false, $allowedTypeArray=null, $faili) {
    // fix path
    $path = str_replace('\\', '/', $path);
	$path = str_replace('-', '_', $path);
    if (substr($path, -1) != '/') {
        $path .= '/';
    }
    // does upload path exists?
    if ((file_exists($path)) && (is_writable($path))) {
        // for all files
        $res = array();
        // get the file list
        if (@$_FILE) {
            $files = & $_FILE;
        } else {
            $files = & $faili;
        }
        // for all files...
        foreach($files as $key => $file) {
            // does the file exist?
            if (!@$file['error'] && $file['size'] && $file['name'] != '') {
                // is the file type allowed?
				//alert($file['type']);
                if (($allowedTypeArray == null) || (@in_array($file['type'], $allowedTypeArray))) {
                    // is it really an uploaded file?
                    if (is_uploaded_file($file['tmp_name'])) {
                        // does file exists?
                        $exists = file_exists($path . $file['name']);
                        // overwrite file?
                        if ($overwrite || !$exists) {
                            // move file to new destination
                            move_uploaded_file($file['tmp_name'], $path . $file['name']);
                            // store name, path, type and size information
                            array_push ($res, array('name' => $file['name'], 'full_path' => $path . $file['name'], 'type' => $file['type'], 'size' => $file['size'], 'overwritten' => $exists));
                        } else {
                           // $this->error .= $this->_error("File \"" . $file['name'] . "\" already exists!");
                        }
                    } else {
					upl_err_paz("Faila nebija!!!");
                       // $this->error .= $this->_error("File \"" . $file['name'] . "\" is not a file!");
                    }
                } else {
				upl_err_paz("Neat�autais faila tips!!!");
                  //  $this->error .= $this->_error("Content Type \"" .  $file['type'] . "\" for file \"".$file['name']."\" not allowed!");
                }
            } else {
			upl_err_paz("Fails neeksist�!!!");
              //  if (@$file['error'] && $file['error'] != 4) {
               //     $this->error .= $this->_error("File \"" .  $file['name'] . "\" does not exist!");
                //}
            }
        }
        return $res;
    }
	upl_err_paz("Ce�� \"$path\" neeksist�, vai ar� nav rakst�ms!!!");
 //   $this->_error("Path \"$path\" does not exist or is not writable!");
    return false;
}
//dm($_FILES);
//echo "<br><hr><br>";
//	$user = $HTTP_SESSION_VARS['registered'];
//	$lietotajs = $user['lietotajs'];
//$direkt = "$VARIABLE_USER_PATH/$lietotajs";
//$direkt = "C:\Program Files\Apache Group\Apache\htdocs\\newsk\upload";
$allowedTypes = array("image/bmp","image/gif","image/pjpeg","image/jpeg","image/x-png","application/octet-stream"
,"application/x-zip-compressed","application/msword", "application/pdf");
$UPLOADFILE_faili = null;
if (strlen($direkt) > 3)
{
	$UPL_OVE_333sw32wfwfwf = true;
	if (isset($_SESSION['UPLOAD_OVERWRITE']))
	{
		$UPL_OVE_333sw32wfwfwf = $_SESSION['UPLOAD_OVERWRITE'];
		$_SESSION['UPLOAD_OVERWRITE'] = null;
		unset($_SESSION['UPLOAD_OVERWRITE']);
	}
//	echo "<p>about to upload</p>";
	$UPLOADFILE_faili = uploadTo($direkt,$UPL_OVE_333sw32wfwfwf, $allowedTypes, $_FILES);
	$ieladetais_fails = '';
	if (isset($UPLOADFILE_faili[0])) {
		$muu = $UPLOADFILE_faili[0];
		$ieladetais_fails = $muu['name'];
	}
}

?>