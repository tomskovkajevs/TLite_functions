<?php

class tGalleryWorks extends tBase {

private $RegistrationCode = "";
private $GalleryParams = Array();

function __construct(){
//
}

function setRegCode($regCode){
	$this->RegistrationCode = $regCode;
}

function getModuleName() {
	return "TGALLERYWORKS";
}

function call__SetVariableNames() {
	$this->SetVariableNames("sess","do");
}


function clearSessionVars($kur = 0) {
//	alert("Clearing session vars $kur ...");
	unset($_SESSION['registered']);
	unset($_SESSION['uploadUser']);
	unset($_SESSION['uploadPass']);
}

function doAction($actionName = ""){
	$ret = "";
//	$ret .= drm($_SESSION);
//	$ret = "<hr>ARCTION NAME: [$actionName]<hr>";
//	$ret .= "<hr>".$this->RegistrationCode." == ".$_SESSION['registered']."<hr>";

//	if ((strLen($actionName) == 0) or (isset($_SESSION['registered']) == false)) {
	return $ret;
}

//-------------------------------------------------------------------------------------------------------------------------------------------------------

function XXsendConfirmationEmail(){
	$sessija = $_SESSION['registered'];
	$bijaID = db_get("id","photoinvoices"," sess = '$sessija' and length(uConfirmationKey) > 0 ");
	if (($bijaID > 0)) {
		$maCode = db_get("uConfirmationKey","photoinvoices"," id = $bijaID ");
		$mailAddr = db_get("uEmail","photoinvoices"," id = $bijaID ");
		$vards = db_get("uName","photoinvoices"," id = $bijaID ");
		$uzvards = db_get("uSureName","photoinvoices"," id = $bijaID ");
		$IPe = db_get("uIP","photoinvoices"," id = $bijaID ");
		$iesn = db_get("acceptTime","photoinvoices"," id = $bijaID ");
		$summa = db_get("BillSumm","photoinvoices"," id = $bijaID ");
$sodienasDatums = date_menesisg_gads(db_get("NOW()","photoinvoices"));
//============================================================================
// %7 = http://foto.ittilts.lv/?page=9

$EMAILBODY = $this->getSField("PHOTO_EMAIL_BODY");
$MONEY_SIGN = $this->getSField("PHOTO_COMMON_MONEY_TYPE");
$LINK_PATH = $this->getSField("PHOTO_EMAIL_CONFIRM_PATH");

$Mail_Subject = $this->getSField("PHOTO_EMAIL_SUBJECT");

$mail_addr_from = $this->getSField("PHOTO_EMAIL_ADDRESS");
$mail_server = $this->getSField("PHOTO_EMAIL_SERVER");
$mail_port = $this->getSField("PHOTO_EMAIL_PORT");
$mail_username = $this->getSField("PHOTO_EMAIL_USERNAME");
$mail_password = $this->getSField("PHOTO_EMAIL_PASSWORD");

$message = $EMAILBODY;
$message = str_ireplace("%10", $MONEY_SIGN, $message);
$message = str_ireplace("%1", $vards, $message);
$message = str_ireplace("%2", $uzvards, $message);
$message = str_ireplace("%3", $iesn, $message);
$message = str_ireplace("%4", $IPe, $message);
$message = str_ireplace("%5", $summa, $message);
$message = str_ireplace("%6", $maCode, $message);
$message = str_ireplace("%7", $LINK_PATH, $message);
$message = str_ireplace("%8", $sodienasDatums, $message);
$message = str_ireplace("%9", $bijaID, $message);


$messageOLD = "SveicinÄ�ti %1 %2 \n
\n
PasÅ«tÄ«jums FotoPils mÄ�jas lapÄ� veikts %3 no IP adreses %4 un kopÄ“jÄ� apmaksas summa ir %10 %5. \n
\n
ApstiprinÄ�t pasÅ«tÄ«jumu: \n
%7&do=c&code=%6 \n
\n
NoraidÄ«t/Atcelt pasÅ«tÄ«jumu: \n
%7&do=d&code=%6 \n
\n
PasÅ«tÄ«jums tiks izpildÄ«ts vienas darba dienas laikÄ� no apstiprinÄ�Å�anas brÄ«Å¾a \n
\n
\n
JÅ«su FOTO salons FotoPils \n
%8, PasÅ«tÄ«juma ID = %9";
$subject = str_ireplace("%1", $iesn, $Mail_Subject);

//$message = str_ireplace("\n", "<br />", $message);
//$message = nl2br($message);
//echo $message;
//echo "<hr>";
//echo "$mail_addr_from ,$mailAddr,$subject,message,$mail_server ,$mail_port,$mail_username,$mail_password";
//$subject = "Foto izgatavoÅ�ana FotoPils salonÄ� - saÅ†emts %1";
//	t_send_mail("toms@hsc.lv",$mailAddr,$subject,$message,"mail.ittilts.lv",25,"toms","demonsmana");
	t_send_mail($mail_addr_from ,$mailAddr,$subject,$message,$mail_server ,$mail_port,$mail_username,$mail_password);
//	die();
	} else {
		die('Error: 18; unexpected call of function sendConfirmationEmail();<br>');
	}
//============================================================================

/*
$message = "";
$message .= "SveicinÄ�ti $vards $uzvards \n";
$message .= "\n";
$message .= "PasÅ«tÄ«jums FotoPils mÄ�jas lapÄ� veikts $iesn no IP adreses $IPe un kopÄ“jÄ� apmaksas summa ir LVL $summa. \n";
$message .= "\n";
$message .= "ApstiprinÄ�t pasÅ«tÄ«jumu: \n";
$message .= "http://foto.ittilts.lv/?page=9&do=c&code=$maCode \n";
$message .= "\n";
$message .= "NoraidÄ«t/Atcelt pasÅ«tÄ«jumu: \n";
$message .= "http://foto.ittilts.lv/?page=9&do=d&code=$maCode \n";
///$message .= "\n";
///$message .= "ApskatÄ«t pasÅ«tÄ«jumu: \n";
///$message .= "http://foto.ittilts.lv/?page=9&do=review&code=$maCode \n";
$message .= "\n";
$message .= "PasÅ«tÄ«jums tiks izpildÄ«ts vienas darba dienas laikÄ� no apstiprinÄ�Å�anas brÄ«Å¾a \n";
$message .= "\n";
$message .= "\n";
$message .= "JÅ«su FOTO salons FotoPils \n";
$message .= "$sodienasDatums, PasÅ«tÄ«juma ID = $bijaID";
	$subject = "Foto izgatavoÅ�ana FotoPils salonÄ� - saÅ†emts $iesn";
	t_send_mail("toms@hsc.lv",$mailAddr,$subject,$message,"mail.ittilts.lv",25,"toms","demonsmana");
	} else {
		die('Error: 18; unexpected call of function sendConfirmationEmail();<br>');
	}

*/
//	die();
}

function saveUploadData(){
/*
// TODO: Jāustaisa universālais variants

	$sessija = $_SESSION['registered'];

	$bijaID = db_get("id","photoinvoices"," sess = '$sessija'");
	if ($bijaID > 0) {
		$mass['PhotoCount'] = $this->get_FilesCount();
		$ki = new sql_action(null,"photoinvoices");
		$ki->update_row($mass," id = $bijaID ");
	} else {
		die('Error: 15; unexpected call of function saveUploadData();<br>');
	}
*/

}


function setGalleryParams($ParamArray){
	$this->GalleryParams = array_combineA($this->GalleryParams,$ParamArray);
}

function getSuperClassModuleName(){
	if (isset($this->GalleryParams['ModuleName'])) {

	} else {
		die('No ModuleName (!!) set - tgalleryWorks.class.php ');
	}
	return $this->GalleryParams['ModuleName'];
}

function get_InvoiceImagesDiv($canDelete = false, $LinkToPart = false, $Params = null){

	$this->checkCompulsaryGetSFields();
	$this->checkUploads();
	//$this->
	$ret = "";
	$fpath = $this->get_FilesDirPath();
	$jau_pievienotas = $this->get_FilesArray();
	$pievienotsSkaits = $this->get_FilesCount();

	$ShowTitle = true;
	$ShowTopLine = true;
	$OtherMessage = "";

	if (isset($Params)) {
		if (is_array($Params)) {
			while (list($param, $value) = @each($Params))
			{
				 $$param = $value;
				// ${$param} = $value;
			}
		}
	}

	$ret .= $OtherMessage;

	$PicMsg = $this->getSField($this->getSuperClassModuleName()."_COMMON_PICS_ADDED");

	$PicDelLink = $this->getSField($this->getSuperClassModuleName()."_COMMON_PICS_DELETE");
	$PicEditLink = $this->getSField($this->getSuperClassModuleName()."_COMMON_PICS_EDIT");


//	$ret .= drm($this->GalleryParams);


	$PicMsg = str_ireplace("%1", $pievienotsSkaits, $PicMsg);
	if ($ShowTitle) { 	$ret .= "<div class=\"message1\">$PicMsg"; }
	if ($ShowTopLine) { $ret .= "<hr>"; }
	if ($ShowTitle) { 	$ret.= "</div>"; }
	$COMMON_FILE_PATH = $this->getSField($this->getSuperClassModuleName()."_COMMON_PIC_PHP_PATH");
	while (list($keyfa, $vafa) = @each($jau_pievienotas))
	{
		$fsize = get_filesize($fpath.$vafa);
		$wd = getImageData($fpath.$vafa,'width');
		$ht = getImageData($fpath.$vafa,'height');
		$tp = getImageData($fpath.$vafa,'type');
		$izmers = "";
		if (strLen($tp) > 0) {
			$izmers = "[".$wd."x".$ht."]";
		}
		$tagatt = get_getN("delFile");
		/*
			TO INSTALL
		*/
	//	$mimg = getResizedImgPath($fpath, $vafa, 100, 75, $this->RegistrationCode, "http://ittilts.lv/projects/FotoPils/foto/pic.php");
		$mimg = getResizedImgPath($fpath, $vafa, 100, 75, $this->RegistrationCode, $COMMON_FILE_PATH);

		$DeleteLink = "";
		if ($canDelete == true) {
			$DeleteLink = "<div class=\"imDetails\" style=\"float: right;\"><a class=\"lang_link2\" href=\"?$tagatt&delFile=$vafa\">$PicDelLink</a></div>";
		}
		$lidz_punktam = stripos($vafa,'.');
		$vafaShort = strtolower(substr($vafa, 0, $lidz_punktam));
		$typz = strtolower(substr($vafa, $lidz_punktam + 1,5));
		$ret .= "
		<div class=\"ImageContainer\">
			<div style=\"height: 130;\">
				<div class=\"clb\"><img class=\"clb\" style=\"float: left;\" src=\"$mimg\"></div>
				<div class=\"imName\">$vafaShort</div>
				<div class=\"imDetails\">($fsize)</div>
				<div class=\"imDetails\">$izmers</div>
			</div>$DeleteLink
		</div>
		";
	}
	$xMessage2ADD_FILE = "Add file:";
	$ret .= "
		<div class=\"UPpic\">
			<div class=\"fname\">$xMessage2ADD_FILE</div>
			<div class=\"fitem\"><input id=\"my_file_element\" type=\"file\" name=\"file_1\"  type=\"file\"></div>
			<div id=\"files_list\" class=\"textSmallEtc\">
			<script>
				<!-- Create an instance of the multiSelector class, pass it the output target and the max number of files -->
				var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), 5 );
				<!-- Pass in the file element -->
				multi_selector.addElement( document.getElementById( 'my_file_element' ) );
			</script>
			</div>
		</div>
	";
	return $ret;
}

function get_FilesArray(){
	$this->checkCompulsaryGetSFields();
	$fpath = $this->get_FilesDirPath();
	return get_dir_files($fpath);
}

function get_FilesCount(){
	$ret = 0;
	$this->checkCompulsaryGetSFields();
	$jau_pievienotas = $this->get_FilesArray();
	$pievienotsSkaits = count($jau_pievienotas);
	if ($pievienotsSkaits > 0) {
		$ret = $pievienotsSkaits;
	}
	return $ret;
}



function handleUploads(){
	$this->checkCompulsaryGetSFields();
	$destination_path = $this->get_FilesDirPath();
	//echo "<hr>DESTINATION PATH: $destination_path<hr>";
	global $PORTAL_INCLUDES_PATH;
	include($PORTAL_INCLUDES_PATH."upload.multiple.php");
	$this->handleArchives();
}

function handleArchives(){
	$AllFiles = $this->get_FilesArray();
	$FilePath = $this->get_FilesDirPath();

	if (is_array($AllFiles))
	{
	    foreach ($AllFiles as $file)
	    {
	    	//$tips =
	    	$fName = strtolower($file);
	    	$daljas = explode('.', $fName);
			$papl = $daljas[count($daljas)-1];
			if (($papl == 'zip')) {
//				include_once('zip.class.php');
				include_once('Archive/zip.php');
				$zip = new Archive_ZIP($FilePath.$fName);
				$tmpZipDir = $FilePath."tmpZipFolder";
				if (is_dir($tmpZipDir) == false) {
					mkdir($tmpZipDir);
				}
				$tmpZipPath = $tmpZipDir."/";
				$zip->extract(array('add_path'=>$tmpZipPath));

				$ZipFiles = $zip -> listContent();
				if (is_array($ZipFiles))
				{
	    			foreach ($ZipFiles as $zipfile)
	    			{
	    				$srcFile = $tmpZipPath.$zipfile['filename'];
	    				$laast = explode("/",$zipfile['filename']);
	    				$destFile = $FilePath.$laast[ count($laast) - 1 ];
	    				copy($srcFile, $destFile);
	    				unlink($srcFile);
	        			//echo "ZipContent: " . drm($zipfile['filename']) . "<br>";
	    			}
				}
				unset($zip);
				unlink($FilePath.$fName);
			}
	    }
	}

/*
	$zip = new Archive_ZIP("zipuploads/filename.zip");
	$zip -> extract(array('add_path' => 'extractZip/'));
	$files = $zip -> listContent();
	if (is_array($files))
	{
	    foreach ($files as $file)
	    {
	        echo "Filename : " . $file['filename'] . " ||| ";
	    }
	}
*/
}

function checkUploads(){
//	echo "66666666666666666666666666666666666";
	//dm($_FILES);
	//dm($_POST);
//	die();
	if (count($_FILES) > 0) {
		$this->handleUploads();
		$this->saveUploadData();
	}
}

function checkCompulsaryGetSFields(){
	$ret = "";
	$mc = $this->getSuperClassModuleName();

	$massx[$mc.'_COMMON_STORAGE_PATH'] = $this->getSField($mc."_COMMON_STORAGE_PATH");
	$massx[$mc.'_COMMON_PICS_ADDED'] = $this->getSField($mc."_COMMON_PICS_ADDED");
	$massx[$mc.'_COMMON_PICS_DELETE'] = $this->getSField($mc."_COMMON_PICS_DELETE");
	$massx[$mc.'_COMMON_PICS_EDIT'] = $this->getSField($mc."_COMMON_PICS_EDIT");
	$massx[$mc.'_COMMON_PIC_PHP_PATH'] = $this->getSField($mc."_COMMON_PIC_PHP_PATH");

	$errors = 0;
	while (list($keyfa, $vafa) = @each($massx))
	{
		if ((strLen(trim($vafa)) == 0) or ($vafa == $keyfa)) {
			$errors++;
			echo "<br>Please do this! ADD SYSTEM string: $keyfa ";
		}
	}

	if ($errors > 0) {
		dm($massx);
		die();
	}
//dm($massx);
	return $ret;
}

function get_FilesDirPath(){
	$ret = "";
	$this->checkCompulsaryGetSFields();

	$userFilePah = $this->getSField($this->getSuperClassModuleName()."_COMMON_STORAGE_PATH");// "C:/web/fotoUserFiles/";
///	$userFilePah = "C:/web/www.ittilts.lv/projects/FotoPils/fotoUserFiles/";
	/*
		TO INSTALL
		Tas pats ieks PIC.php (INDEX ROOT DIR)

	*/
	$workDir = $userFilePah.$this->RegistrationCode;
	if (is_dir($workDir) == false) {
		mkdir($workDir);
	}
	$ret = $workDir."/";

	return $ret;
}


function CheckFileDeleted(){
	$fpath = $this->get_FilesDirPath();
	if (isset($_GET['delFile'])) {
		$ttere = inputam($_GET['delFile']);
		unlink($fpath.$ttere);
		//delete($fpath.$ttere);
		$nodelFile = get_getN("delFile");
		reload("?$nodelFile");
	}
}


} // class

?>