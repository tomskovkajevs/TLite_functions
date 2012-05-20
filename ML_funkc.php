<?php
/*
* SIA IT Tilts
* http://ittilts.lv
*
* Labots 06.03.2011
*
*/
/*
 <!-- Licence:
 	Viss kas Å�ei tir ir vai nu savÄ�kts no citurienes, vai smagu darbu strÄ�dÄ�jot uzprogrammÄ“ts...
 	BET - Å�is viss tomÄ“r ir izmantojams citur. TÄ�pÄ“c tas neko nemaksÄ�. BÅ«tu gan forÅ�i ja Tu Å�o failu
 	izmanto savos projektos, pastÄ�stÄ«tu par to arÄ« autoriem, lai gadÄ«jumÄ� lieli kÄ¼Å«du gadÄ«jumÄ� mÄ“s Tevi
 	varÄ“tu arÄ« pabrÄ«dinÄ�t.
	Ja kads prasa Å�o failu: Tev tas ir jadod un tu nedriksti prasit par to naudu (vai citus labumus).
	Nu, talak arii lidzigi GPL vX -->
*/
//clear_log();

//echo "funkc.php <br>";


if (file_exists('ML_classes.php') == false)
{
	include_once($PORTAL_INCLUDES_PATH.'ML_classes.php');
} else {
	include_once('ML_classes.php');
}


// 0 = do nothing
// 1 = create folder if not exists and if ID > 0
// 2 = list files located in root if ID not set or < 1 (do not create folder)
function get_files($celsh, $id = 0, $action = 0, $pics = 1)
{
	$ret = NULL;
	//echo "In da function <BR>";
	if ($id > 0)
	{
		$diir = $celsh."$id";
	} else {
		$diir = $celsh;
	}
	$d = dir($diir) or  $d = NULL;
	if ($d != NULL)
	{
		while (false !== ($entry = @$d->read())) {
		  if ((substr_count($entry,".") > 0) && ($entry != "..") && ($entry != "."))
		  {
		  	$tempzzzz = strtolower($entry);
		  	if ($pics == 1)
			{
				if ((substr_count($tempzzzz,".jepg") > 0) or (substr_count($tempzzzz,".jpg") > 0) or (substr_count($tempzzzz,".gif") > 0)or (substr_count($tempzzzz,".png") > 0)) {
		  			$ret[] = $entry;
				}
			} else {
				if ((substr_count($tempzzzz,".jpeg") > 0) or(substr_count($tempzzzz,".jpg") > 0) or (substr_count($tempzzzz,".gif") > 0)or (substr_count($tempzzzz,".png") > 0))
				{
				} else {
					$ret[] = $entry;
				}
			}
		  }
		}
		$d->close();
	} else {
		if (($id > 0) && ($action == 1))
		{
			//echo "Making directory <BR>";
			mkdir($celsh."$id");
		}
	}
	return $ret;
}
function get_directories($celsh, $id = 0, $action = 0, $pics = 1)
{
	$ret = NULL;
	//echo "In da function <BR>";
	if ($id > 0)
	{
		$diir = $celsh."$id";
	} else {
		$diir = $celsh;
	}
	$d = dir($diir) or  $d = NULL;
	if ($d != NULL)
	{
  	while (false !== ($entry = @$d->read())) {
	  if ( ($entry != "..") && ($entry != ".")) // substr_count($entry,".") > 0) &&
		  {
//            echo "[$diir\\$entry]<br>";
            if (is_dir("$diir\\$entry")) {
					$ret[] = $entry;
            }
		  }

		}
		$d->close();
	} else {
        /// norÄ�dÄ«tÄ� dira NAV!
	}
	return $ret;
}


function get_getTP($arrx) {
	$ret = "";

	reset($TPArr);
	while (list($ke, $va) = @each($TPArr)) {
		$ret .= top_path_val($va);
	}
	
	return $ret;
}

/**
 * Service Function - do not use it for production purposes
 *
 * @access	none
 * @param	
 * @return	string
 */
function tmp_get_Zeb($TPArr, $InsrtArr,$num) {
	$ret = "";
	$tpArrCnt = count($TPArr);
	//echo "cnt[$tpArrCnt @ $num]";
	if ($tpArrCnt > $num) {
		$nxt = tmp_get_Zeb($TPArr, $InsrtArr, $num+1);
		$npk = 0;
		$currPart = "";
		while (list($ke1, $va1) = @each($TPArr)) {
			if ($num == $npk) {
				$currPart = $ke1;			
			}
			$npk++;	
		}
		//echo "cp[$currPart]@$num.";
		if (array_key_exists($currPart, $InsrtArr)) {
			$ret .= "/".$InsrtArr[$currPart]; 
		} else {
			$currVal = top_path_val_get($num);
			if (strlen($currVal) > 0) {
				$ret .= "/".top_path_val_get($num);
			} else {
				$ret .= "////";
			}
		}
		$nxt2 = str_ireplace("////", "", $nxt);
		if (strlen($nxt2) > 0) {
			// tālākiem ir vērtības!
			$ret .= str_ireplace("////", "/0", $nxt);
		}
	} else {
		/// nekas!
		//echo "end...[$num]";
	}
	return $ret;
}

function get_curr_path() {
	
}

/**
 * Returns formated TOP string!
 *
 * @access	public
 * @param	Array1-TOPDefs (Array("lang"=>"0","sys"=>"1","part"=>"2")), Array2-AddValues (Array("sys"=>"dupersys"))
 * @return	string
 */
function get_top_replace(/* Pirmais ir lielais Array, otrs ir konkrēti ka'ds vajadzīgs.. */) {
	$arrx = Array();
	if (func_num_args() >= 1) {
		$TPArr = func_get_arg(0);
		if (func_num_args() > 1) {
			$arrx = func_get_arg(1);
		}
	}
	$ret = "";
	$bija = "";
	reset($TPArr);
	$ret .= tmp_get_Zeb($TPArr, $arrx,0);
	return $ret;
}

/**
 * Returns formated TOP string! 
 *
 * @access	public
 * @param	Array1-TOPDefs (Array("lang"=>"0","sys"=>"1","part"=>"2")), Array2-AddValues (Array("sys"=>"dupersys"))
 * @return	string
 */
function get_top_replace_strict(/* Pirmais ir lielais Array, otrs ir konkrēti ka'ds vajadzīgs.. */) {
	$arrx = Array();
	if (func_num_args() >= 1) {
		$arrx = func_get_arg(1);
		$TPArr = func_get_arg(0);
	}
	$ret = "";
	$bija = "";
	reset($TPArr);
	$ret .= tmp_get_Zeb($TPArr, $arrx,0);
	return $ret;
}


function top_path_init() {

//$_SESSION['ittpath'] = null;
//unset($_SESSION['ittpath']);	

}

function top_path_val($pos) {
	$ret = "";
	$arrx = top_path_array(); 
	$arrLen = count($arrx);
	if ($pos < $arrLen) {
		$retNul = $arrx[0];
		$ret = $retNul['name'];
	} else {
		$ret = "";
	}
	return $ret;
}

function top_path_val_get($pos) {
	// Izmanto lai iegūtu konkrētu elementu lapas ceļā!
	$ret = "";
	$arrx = top_path_array_get(); 
	$arrLen = count($arrx);
	if ($pos < $arrLen) {
		$ret = $arrx[$pos];
	} else {
		$ret = "";
	}
	return $ret;
}

function top_path_array_get() {
	$retArray = Array();
	foreach (explode ("/", $_SERVER['REQUEST_URI']) as $part)
	{
		if ((strlen($part) > 0) && ($part != "?")) {
			$retArray[] = $part;
		}
	}
	return $retArray;
}

function top_path_array() {
$retArray = Array();
$failF = 0;
foreach (explode ("/", $_SERVER['REQUEST_URI']) as $part)
{
	$row['name'] = $part;
	if (strlen($part) > 0) {
		$row['dbid'] = "0";
		$partText = inputam($part);
		if ($failF == 0) {
			$row['dbid'] = db_get("id","menu"," fname = '$partText' ");
			$row['text'] = db_get("expl","menu"," fname = '$partText' ");
			if (strlen($row['dbid']) == 0) { 
				$failF = 1;
			}
		}
		$retArray[] = $row;
	}
}

return $retArray;
}
function top_path_add($pathName,$pathText,$Link) {
	$ret = -1;
		
	$wrkArr = Array();	
	if (isset($_SESSION['ittpath'])) {
		$wrkArr = $_SESSION['ittpath'];	
	}
	$sml = Array();
	$sml['name'] = $pathName;
	$sml['text'] = $pathText;
	$sml['link'] = $Link;
	$wrkArr[] = $sml;
	$ret = count($wrkArr);
	$_SESSION['ittpath'] = null;
	session_commit();
	$_SESSION['ittpath'] = $wrkArr;
	
	return $ret;	
}

function top_path_set($Level,$pathName,$pathText,$Link) {
$wrkArr = Array();	
if (isset($_SESSION['ittpath'])) {
	$wrkArr = $_SESSION['ittpath'];	
}

$sml['name'] = $pathName;
$sml['text'] = $pathText;
$sml['link'] = $Link;

$wrkArr[$Level] = $sml;

$_SESSION['ittpath'] = $wrkArr;
}

function top_path_links() {
	$ret = "";
if (isset($_SESSION['ittpath'])) {	
$topArary = $_SESSION['ittpath'];
$topValCnt = count($topArary);
$bija = "";
if ($topValCnt > 1) {
	for ($a = 0; $a < $topValCnt;$a++) {
		$vrow = $topArary[$a];
		$txt = $vrow['text'];
		$txlink = $vrow['name']; 
		$ret .= $bija."<div class=\"mpath\"><a href=\"/$txlink/\">$txt</a></div>";
		$bija = "<div class=\"mpathbulet\">»</div>";
	}
} 
} // if isset $_SESSION['ittpath']
	return $ret;
}


function if_post($varName,$default = ""){
	$ret = $default;
	if (isset($_POST[$varName])) {
		$ret = $_POST[$varName];
	}
	return $ret;
}

if (function_exists('HTMLDate') == false) {
function HTMLDate($prefix,$value,$start_y = '',$end_y = ''){
	//echo $prefix.'==='.$value.'<br>';
	$ret = "";
	//$value = date("Y-m-d H:i");
	//$time = strtotime($value);
	if($value == '0'){
		if($prefix=='in')$time=mktime(0,0,0,date("m"),date("d")+1,date("Y"));
		else $time=mktime(0,0,0,date("m"),date("d"),date("Y"));
	}
	else $time = strtotime($value);

	if($start_y == '')$start_y = date("Y");
	if($end_y == '')$end_y = date("Y") + 1;

	$y = date("Y",$time);
	if($y<1970)$time--;
	$m = date("m",$time);
	$d = date("d",$time);
	$h = date("H",$time);
	$mi = date("i",$time);
	$ret.='<table cellpadding="0" cellspacing="0" border="0"><tr>';
	$ret.='<td><select name="'.$prefix.'Day" id="'.$prefix.'Day" style="width:45px">';
	for($i=1;$i<10;$i++){
		$ret.='<option value="0'.$i.'" '.('0'.$i==$d?' selected="selected" ':'').'>0'.$i.'</option>';
	}
	for($i=10;$i<32;$i++){
		$ret.= '<option value="'.$i.'" '.($i==$d?' selected="selected" ':'').'>'.$i.'</option>';
	}
	$ret.='</select></td>';

	$ret.= '<td><select name="'.$prefix.'Month" id="'.$prefix.'Month" style="width:45px">';
	for($i=1;$i<10;$i++){
		$ret.='<option value="0'.$i.'" '.('0'.$i==$m?' selected="selected" ':'').'>0'.$i.'</option>';
	}
	for($i=10;$i<13;$i++){
		$ret.='<option value="'.$i.'" '.($i==$m?' selected="selected" ':'').'>'.$i.'</option>';
	}
	$ret.='</select></td>';

	$ret.= '<td><select name="'.$prefix.'Year" id="'.$prefix.'Year" style="width:58px">';
	for(;$start_y<=$end_y;$start_y++){
		$ret.= '<option value="'.$start_y.'" '.($y==$start_y?' selected="selected" ':'').'>'.$start_y.'</option>';
	}
	$ret.='</select></td></tr></table>';
	return $ret;
}
}

//=============================================================================================================================

function replaceBrokenUTF($Table,$Field,$Key,$Iterations = 5){
$naceData = Array();
$naceData = sqlsel("SELECT * FROM ".$Table);
//$ret .= "<hr>";
 			while (list($keyu, $vau) = @each($naceData))
			{
				$naceName = $vau[$Field];
				$naceID = $vau[$Key];
				$naceNew = $naceName;
				for ($a=0;$a < $Iterations; $a++) {
					$naceNew = str_ireplace('Ć„Ā�',"Ä�",$naceNew);
					$naceNew = str_ireplace('Ć„ā€�',"Ä“",$naceNew);
					$naceNew = str_ireplace('Ć„ā€™',"Ä’",$naceNew);
					$naceNew = str_ireplace('Ć„Ā¼',"Ä¼",$naceNew);
					$naceNew = str_ireplace('Ć…Ā�',"Å�",$naceNew);
					$naceNew = str_ireplace('Ć… ',"Å ",$naceNew); // nestrÄ�dÄ�
					$naceNew = str_ireplace('Ć…Ā«',"Å«",$naceNew);
					$naceNew = str_ireplace('Ć…Ā¾',"Å¾",$naceNew);
					$naceNew = str_ireplace('Ć…Ā¾',"Å¾",$naceNew);
					$naceNew = str_ireplace('Ć„Ā·',"Ä·",$naceNew);
					$naceNew = str_ireplace('Ć„Ā«',"Ä«",$naceNew);
					$naceNew = str_ireplace('Ć…ā€ ',"Å†",$naceNew);
					$naceNew = str_ireplace('Ć„Ā£',"Ä£",$naceNew);
					$naceNew = str_ireplace('Ć„Ā¶',"Ä¶",$naceNew);
					$naceNew = str_ireplace('Ć„Ā¨',"Ä¨",$naceNew);
					$naceNew = str_ireplace('Ć„Å’',"Ä�",$naceNew);
					$naceNew = str_ireplace('Ć¢ā‚¬Ā¯',"\"",$naceNew);
					$naceNew = str_ireplace('Ć¢ā‚¬Å¾',"\"",$naceNew);
					$naceNew = str_ireplace('Ć¢ā‚¬Å“',"\"",$naceNew);
				}
				unset($newData);
				$newData[$Field] = $naceNew;
				$rer = new sql_action(null,$Table);
				$rer->update_row($newData,$Key." = '$naceID' ");
				unset($rer);
				//$ret .= "[$naceNew] vs [$naceName] <br>";
			}

}

//=============================================================================================================================


function get_getInputsN(){
	$ret = "";

$GivenArray = Array();
$skaits = func_num_args();
if ($skaits > 0) {
	for ($e=0;$e<$skaits;$e++) {
		$GivenArray[func_get_arg($e)] = func_get_arg($e);
	}
}
$MyGET = $_GET;
reset($MyGET);
$RealGet = Array();
while (list($ke, $va) = @each($MyGET)) {
	$Add = true;
	reset($GivenArray);
	if (array_key_exists($ke,$GivenArray)) {
		$Add = false;
	}

	if ($Add) {
		$RealGet[$ke] = $va;
	}
}
reset($RealGet);
while (list($ke, $va) = @each($RealGet)) {
	$ret .= "<input type=\"hidden\" name=\"$ke\" value=\"$va\">";
}

	return $ret;
}

//=============================================================================================================================

function remobeRepeatedQuotes($text){
	$text = str_ireplace("''","'",$text);
	$text = str_ireplace("''","\"",$text);
	$text = str_ireplace("\"\"","\"",$text);
	return $text;
}

//=============================================================================================================================

function searchCharExists($string){
	$ret = false;
	if (substr_count($string," & ") > 0) { $ret = true; }
	if (substr_count($string," | ") > 0) { $ret = true; }
	return $ret;
}

//=============================================================================================================================

function explodeByChars($Chars,$string){
	$ret = Array();
	$vardi = explode(" ",$string);
	$CurrentWord = "";
	$LastSign = "";
	$bija = "";
	$BracketsOpenCount = 0;
	while (list($keyw, $vaw) = @each($vardi)) {
		$retW[] = $vaw;
		if (in_array($vaw,$Chars)) {
			if (strlen(trim($CurrentWord)) > 0) {
				if (strlen($LastSign) > 0) {
					$ret[] = $LastSign;
				}

				// Wee START
				$CurrentWord = trim($CurrentWord);
				while ($CurrentWord[0] == "(") {
					$ret[] = "(";
					$BracketsOpenCount++;
					$CurrentWord = substr($CurrentWord,1,strlen($CurrentWord)-1);
				}
				$retPeciekavaCount = 0;
				while (($CurrentWord[strlen($CurrentWord)-1] == ")") && ($BracketsOpenCount > 0)) {
					$BracketsOpenCount--;
					$retPeciekavaCount++;
					$CurrentWord = substr($CurrentWord,0,strlen($CurrentWord)-1);
				}
				$ret[] = $CurrentWord;
				while ($retPeciekavaCount > 0) {
					$ret[] = ")";
					$retPeciekavaCount--;
				}
				// Wee END

				$LastSign = $vaw;
				$CurrentWord = "";
				$bija = "";
			}
		} else {
			$CurrentWord .= $bija.$vaw;
			$bija = " ";
		}
	}
	if (strlen(trim($CurrentWord)) > 0) {
		$CurrentWord = trim($CurrentWord);
		if (strlen($LastSign) > 0) {
			$ret[] = $LastSign;
		}
				// Wee START
				$CurrentWord = trim($CurrentWord);
				while ($CurrentWord[0] == "(") {
					$ret[] = "(";
					$BracketsOpenCount++;
					$CurrentWord = substr($CurrentWord,1,strlen($CurrentWord)-1);
				}
				$retPeciekavaCount = 0;
				while (($CurrentWord[strlen($CurrentWord)-1] == ")") && ($BracketsOpenCount > 0)) {
					$BracketsOpenCount--;
					$retPeciekavaCount++;
					$CurrentWord = substr($CurrentWord,0,strlen($CurrentWord)-1);
				}
				$ret[] = $CurrentWord;
				while ($retPeciekavaCount > 0) {
					$ret[] = ")";
					$retPeciekavaCount--;
				}
				// Wee END

//		$ret[] = $CurrentWord;
	}
	//$ret = drm($retW);
//	dm($ret);
	return $ret;
}

//=============================================================================================================================

function analyzeSearchString($searchField,$srchString,$options = NULL){
	$ret = "";
	$searchFieldStr = "";
	$srchString = mysql_escape_string($srchString);
	if (is_array($searchField)) {
		$bija = "";
		while (list($key, $va) = @each($searchField))
		{
			$searchFieldStr .= "$bija $va";
			$bija = ", ' ', ";
		}
		$searchFieldStr = " concat($searchFieldStr) ";
	} else {
		$searchFieldStr = " $searchField ";
	}
	if (strlen($srchString) > 0) {
		$searchEnabledChars = Array("&","|");
		$meklejamais = explodeByChars($searchEnabledChars,$srchString);
		$bija = "";
		while (list($key, $va) = @each($meklejamais)) {
			if ($va == "(") {
				$ret .= $bija.$va;
			} elseif ($va == ")") {
				$ret .= $bija.$va;
			} elseif (in_array($va,$searchEnabledChars)) {
				switch($va){
					case "&":
						$ret .= " AND ";
						break;
					case "|":
						$ret .= " OR ";
						break;
					default:
						$ret .= " UNKNOWN_TAG ";
				} // switch
			} else {
				$SubsearchSign = "%";
				$CompareSign = "LIKE";
				$ret .= $bija.$searchFieldStr." $CompareSign \"".$SubsearchSign.$va.$SubsearchSign."\"";
			}
			$bija = " ";
		}

	}
	return $ret;
}

//=============================================================================================================================

// checkSuperNodes("t1_categories","id","sid",$_GET['pcat'],$catid
function checkSuperNodes($tablename,$keyFld, $sidFld, $aktivaisID, $lapinjasID){
// StaigÄ� pa koku lÄ«dz nulles lapai (rootam) un Ä¨eko
// JAIET PA KOKU UZ NULLES LAPINJU UN JASKATAS VAI ID ir/nav pa celjam
	$ret = false;

	if ($aktivaisID == $lapinjasID) {
		$ret = true;
	} elseif ($aktivaisID == 0) {
		$ret = false;
	} else {
		$nakisaisID = db_get($sidFld,$tablename," $keyFld = '$aktivaisID' ");
		$ret = checkSuperNodes($tablename,$keyFld, $sidFld, $nakisaisID, $lapinjasID);
	}

	return $ret;
}

function getSubNodeIDs($tableName,$keyFld, $sidFld, $currentID){
	$ret = "$currentID";

	$bubs = selasoc($keyFld,$tableName," $sidFld = $currentID ");
	if (is_array($bubs)) {
		while (list($key, $va) = @each($bubs))
		{
			$cici = $va[$keyFld];
			$ret .= ",".getSubNodeIDs($tableName,$keyFld,$sidFld,$cici);
		}
	}

	return $ret;
}

function checkSubNodes($tableName,$keyFld, $sidFld, $aktivaisID, $lapinjasID, $deep = 0){
/*
	$aktivaisID - no kuras lapiÅ†as skatu punkta tagad skatamies
	$lapinjasID - tÄ� lapiÅ†a kura jÄ�sameklÄ“, jÄ�skatÄ�s vai viÅ†a eksistÄ“
*/
// StaigÄ� pa koku uz lapiÅ†Ä�m un skatÄ�s vai tÄ�da IR
// GRUTS VARIANTS, JAIET CAURI VISAM KOKAM UZ LAPINJAM
	$ret = false;
	//echo "$tableName,$keyFld, $sidFld, act=$aktivaisID, lapa=$lapinjasID, deep=$deep <br>";
	if ($aktivaisID == $lapinjasID) {
		$ret = true;
	} elseif ($aktivaisID == 0) {
		$ret = false;
	} else {
		//$apakshejie = selasoc($keyFld,$tableName," $sidFld = '$aktivaisID' ",1);
		$apakshejie = selasoc($keyFld,$tableName," $sidFld = '$aktivaisID' ");
	//	dm($apakshejie);
		while (list($key, $va) = @each($apakshejie))
		{
			$aidis = $va[$keyFld];
			if ($aidis == $lapinjasID) {
				$ret = true;
				break;
			}
			$deep++;
			$ret = checkSubNodes($tableName,$keyFld, $sidFld, $aidis, $lapinjasID,$deep);
		}
		//$ret = checkSubNodes($tablename,$keyFld, $sidFld, $nakisaisID, $lapinjasID);
	}
	return $ret;
}

function nbsp($text){
	return str_ireplace(" ", "&nbsp;", $text);
}

function eCheckSpecials($txt){
	$ret = $txt;
// TODO
	return $ret;
}

function eReplace(){
	$argCnt = func_num_args();
	$ret = "";
	if ($argCnt > 0) {
			$ret = func_get_arg(0);
	}
	for ($e=1;$e < $argCnt;$e++) {
	 	$repStr = func_get_arg($e);
		$ret = str_ireplace("::$e::", $repStr, $ret);
	}
	return $ret;
}

function getXField(){
	$ret = "";
	$argCnt = func_num_args();
	if ($argCnt >= 1) {
		$FCode = func_get_arg(0);
	}

	$kods = inputam($FCode);
	$SelFields = "longer lnX, info infX";
	$testingLangs = false;
	if (isset($_SESSION['TILTS_ACTIVE_LANG'])) {

		//$testingLangs = true; // COMMENT TO TEST LANGS!!!
		if ($testingLangs == false) {
			if ($_SESSION['TILTS_ACTIVE_LANG'] == "LV") { $SelFields = "longer lnX, info infX"; }
			if ($_SESSION['TILTS_ACTIVE_LANG'] == "RU") { $SelFields = "longerRU lnX, infoRU infX"; }
			if ($_SESSION['TILTS_ACTIVE_LANG'] == "EN") { $SelFields = "longerEN lnX, infoEN infX"; }
		} else {
			$test = "RU";
			$SelFields = "longer$test lnX, info$test infX";
		}
	}
	$nbsp = 0;
	$key = selasoc("id, nbsp, $SelFields","pagetexts"," code = '$kods' ");
	if (is_array($key)) {
		$key = $key[0];
		if ((isset($key['id'])) && ($key['id'] > 0)) {
			$ret = $key['lnX'];
			if (strLen($ret) == 0) {
				$ret = $key['infX'];
			}
		} else {
			$ret = $FCode;
		}
		$nbsp = $key['nbsp'];
		if (($_SESSION['TILTS_ACTIVE_LANG_DBUGMODE_IP'] == "all") or
		($_SESSION['TILTS_ACTIVE_LANG_DBUGMODE_IP'] == $_SERVER['REMOTE_ADDR']))
		{
			if ((isset($_SESSION['TILTS_ACTIVE_LANG_DBUGMODE'])) &&
			(strtolower($_SESSION['TILTS_ACTIVE_LANG_DBUGMODE']) == "on")) {
				$ret = "[{".$key['infX']."}{".$key['lnX']."}{".$FCode."}]";
			}
			if ((isset($_SESSION['TILTS_ACTIVE_LANG_DBUGMODE'])) &&
			(strtolower($_SESSION['TILTS_ACTIVE_LANG_DBUGMODE']) == "on2")) {
				$ret = $FCode;
			}
		}
	} else {
		$ret = $FCode;
	}
	//$ret = $FCode;
	if ($argCnt > 1) {
		for ($e=1;$e < $argCnt;$e++) {
		 	$repStr = func_get_arg($e);
		 	if (is_array($repStr)) {
		 		while (list($kx, $vx) = @each($repStr))
				{
					$ret = str_ireplace("::$kx::", $vx, $ret);
				}
		 	} else {
				$ret = str_ireplace("::$e::", $repStr, $ret);
			}
		}
	}
	$ret = eCheckSpecials($ret);
	if ($nbsp <> 0) {
		$ret = str_ireplace(" ", "&nbsp;", $ret);
	}
	return $ret;
}

function country_long($short){
	$ret = "";
	$ret = db_get("cntry_name","countries_codes"," code2 = '$short' ");
	return $ret;
}

function reload_after(){
	if (isset($_SESSION['tilts_SQL_ACTION'])) {
		$_SESSION['tilts_SQL_ACTION']++;
	} else {
		$_SESSION['tilts_SQL_ACTION'] = 1;
	}
}

function reload_after_do(){
	if (isset($_SESSION['tilts_SQL_ACTION'])) {
		unset($_SESSION['tilts_SQL_ACTION']);
		reload();
	}
}

function DateForDB($DateStr){

	if (substr_count($DateStr," ") == 1) {
		$expq = explode(" ",$DateStr);
		$DateStr = Trim($expq[0]);
	}

	if (substr_count($DateStr,"-")== 2) {
		$expl = explode("-", $DateStr);
		if (strlen($expl[0]) < 4) {
			$DateStr = DateReverse($DateStr,"-","+");
			$DateStr = str_ireplace("+","-",$DateStr);
		}
	}
	if (substr_count($DateStr,"/")== 2) {
		$expl = explode("/", $DateStr);
		if (strlen($expl[0]) < 4) {
			$DateStr = DateReverse($DateStr,"/","+");
			$DateStr = str_ireplace("+","-",$DateStr);
		}
	}
	if (substr_count($DateStr,".")== 2) {
		$expl = explode(".", $DateStr);
		if (strlen($expl[0]) < 4) {
			$DateStr = DateReverse($DateStr,".","+");
			$DateStr = str_ireplace("+","-",$DateStr);
		}
	}
	return $DateStr;
}

function DateInc($datex){
	$ret = DateForDB($datex);
	$ret = db_get("select FROM_DAYS(TO_DAYS('$ret')+1)",null,null,0);
	return $ret;
}
function DateDec($datex){
	$ret = DateForDB($datex);
	$ret = db_get("select FROM_DAYS(TO_DAYS('$ret')-1)",null,null,0);
	return $ret;
}
function DaysFromDates($DateFrom, $DateTo){
	$ret = 0;

	$DateFrom = DateForDB($DateFrom);
	$DateTo = DateForDB($DateTo);
/*	if ((substr_count($DateFrom,"-")== 2)
	&& (substr_count($DateTo,"-")== 2)
	) {

		$expl = explode("-", $DateFrom);
		if (strlen($expl[0]) < 4) {
			$DateFrom = DateReverse($DateFrom,"-","+");
			$DateFrom = str_ireplace("+","-",$DateFrom);
		}

		$expl = explode("-", $DateTo);
		if (strlen($expl[0]) < 4) {
			$DateTo = DateReverse($DateTo,"-","+");
			$DateTo = str_ireplace("+","-",$DateTo);
		}

	} else {
	// ??????


	}
*/
	$df = db_get("select TO_DAYS('$DateFrom')",null,null,0);
	$dt = db_get("select TO_DAYS('$DateTo')",null,null,0);

	if ($dt > $df) {
		$ret = $dt - $df;
	} else {
		$ret = $df - $dt;
	}
	return $ret;
}

function getUUID(){
	return db_get("select UUID();");
}


function DBDate($DateStr){
	$Datums = explode(" ",$DateStr);

}

function DateReverse($DateStr,$Separator,$ShowingSeparator){
	$ret = "";
	$Datums = explode(" ",$DateStr);
	$DateArray = explode($Separator,$Datums[0]);
	$skk = count($DateArray);
//	echo "[$DateStr] Skaits: $skk<br>";
	$ret .= $DateArray[2].$ShowingSeparator.$DateArray[1].$ShowingSeparator.$DateArray[0];
	return $ret;
}

function notNull($val){
	$ret = $val;
	if (strLen(trim($val)) == 0) {
		$ret = "&nbsp;";
	}
	return $ret;
}


function loaded_file($filee)
{
global $_SESSION;

$_SESSION['LOADED_FILES'][$filee]++;
$_SESSION['LOADED_FILES_LAST'] = $filee;
}
function dirTree($dir) {
   $d = dir($dir);
   while (false !== ($entry = $d->read())) {
       if($entry != '.' && $entry != '..' && is_dir($dir.'/'.$entry) != true)
           $arDir[$entry] = $entry;
   }
   $d->close();
   return $arDir;
}
function include_file($filename, $diee = true, $ret = false)
{
	//echo "Including [$filename] ... <br>";
	if (file_exists($filename) == false)
	{
		global $MSG_INCLUDE_FILE_NOT_FOUND;
		//echo "$MSG_INCLUDE_FILE_NOT_FOUND!!!";
		$mess = $MSG_INCLUDE_FILE_NOT_FOUND;
		$rets = str_replace('%file%',$filename,$mess);
		if ($ret == false)
		{
			$dateTime = date("d.m.Y H:a:s ");
			echo $dateTime." - ".$rets."<br>";
		} else {
			return $rets;
		}
		if ($diee == true)
		{
			die('include_fiile.$diee == true <br>');
		}
	} else {
		include($filename);
	}
}

function debug_start(){
	$_SESSION['debug_mode'] = 'on';
}

function debug_end(){
	$_SESSION['debug_mode'] = 'off';
}

function is_debug_mode(){
	$ret = false;
	if ((isset($_SESSION['debug_mode'])) && (strtolower($_SESSION['debug_mode']) == "on")) {
		$ret = true;
	}
	return $ret;
}
function debug($msg){
	//
	if (is_debug_mode()) {
		echo $msg;
	}
}

function debugret($msg){
	$ret = "";
	if (is_debug_mode()) {
		$ret = $msg;
	}
	return $ret;
}

function is_developer()
{
	return true;
}
function get_day_count($year, $month)
{
	$kad = mktime(1, 0, 0, $month, 1, $year);
	return date("t",$kad);
}
function get_first_day($year, $month)
{
	$kad = mktime(1, 0, 0, $month, 1, $year);
	return date("w",$kad);
}
function nulle_klat($kam)
{
	$ret = $kam;
	if (strlen($ret) < 2)
	{
		$ret = "0$ret";
	}
	return $ret;
}
function get_filesize($file)
{
	$all_units[] = "B";
	$all_units[] = "KB";
	$all_units[] = "MB";
	$all_units[] = "GB";
	$all_units[] = "TB";

$ret = "";
$unit = 0;

$fsize = filesize($file);
$ret .= get_size($fsize,$unit);
return round($ret,2).' '.$all_units[$unit];
}
function get_size($size,&$unit)
{
	if ($size > 1024)
	{
		$unit++;
		$size = get_size($size/1024,$unit);
	}
	return $size;
}
function get_dir_files($diraa)
{
$ret = Array();
$d = dir("$diraa");
while (false !== ($entry = $d->read()))
{
	$entry = str_replace("\\","/",$entry);
	if (
	($entry !== "Thumbs.db") &&
	($entry !== ".") &&
	($entry !== "..")
	)
	{
		if (is_dir($diraa."/".$entry))
	{
		//echo "<hr>$direkt - $entry<hr>";
	} else {
	//	$f_skaits++;
		$ret[] = $entry;
	}
	}
}
$d->close();
return $ret;
}
/*
* Ieg?st pamatinform?ciju par bildi
*/
function getImageData( $fails, $data )
{
	$size = GetImageSize( $fails );
	switch ( $data )
		{
		case 'width':
			return $size[0];
			break;
		case 'height':
			return $size[1];
			break;
		case 'type':
			switch ( $size[2] )
				{
				case 1:
					return 'gif';
					break;
				case 2:
					return 'jpg';
					break;
				case 3:
					return 'png';
					break;
				}
			break;
		}
}
/*
* Druk? sessijas log datu v?rt?bas
*/
function dm_log($name = "logs")
{
	echo drm_log($name);
}
/*
* Atgrie? sessijas log datu v?rt?bas sak?rtotas tabul?
*/
function drm_log($name = "logs")
{
	global $_SESSION;
	$hohoo = $_SESSION['logs'];
	$hohoo = array_reverse($hohoo);
	return drm($hohoo);
}
/*
* Att?ra sessijas logu
*/
function clear_log($name = "logs")
{
	global $_SESSION;
	unset($_SESSION[$name]);
	global $_SESSION;
}
/*
* Logo sessij?!!
*/
function add_log($what,$name = "logs", $max_log = 16)
{
	$sess_var_name = $name;
	$data = null;
	global $_SESSION;
	if (isset($_SESSION[$sess_var_name]))
	{
		$data = $_SESSION[$sess_var_name];
	}
	$pi = explode(" ",microtime());
	$maz = (round($pi[0],2) * 100);
	if ($maz < 10)
	{
		$maz = "0".$maz;
	}
	$laiks = date("h:i:s:").$maz;

	if (count($data) > $max_log - 1)
	{
		$data = array_reverse($data);
		unset($data[$max_log+1]);
		$data = array_reverse($data);
		$data[$max_log+1] = "$laiks: ".$what;
	}
	else
	{
		$data = array_reverse($data);
		$data[] = "$laiks: ".$what;
		$data = array_reverse($data);
	}
	$_SESSION[$sess_var_name] = $data;
}
/*
* Bilzu attelosana ramiitii....
*
* @array		- Masivs ar failiem
* @path			- Celsh!??!
* @column		- Kolonnu skaits
* @href			- Saites HREF vertiba [filtreejas]
* @onClick		- OnClick utt iesp?jas ...[filtreejas]
* @td_styles	- ieksh <TD>, lai varetu definet stilus ja vajag... [filtreejas]
* @img			- ieksh <img> taga briivais... [filtreejas]
* [filtreejas]	- iespejams izmantot ##file## un ##path## - tie tiks nomainiiti!?!
*
*
* 28.01.2005, Toms Kovk?jevs, IT-Tilts
*/
function karto_pic($array, $path , $column = 3,$href="",$onClick="", $td_styles="", $img="", $src="")
{
$ret = "";
$curr_col = 0;
while (list($key, $va) = @each($array))
	{
		if ($curr_col >= $column)
		{
			$curr_col = 0;
			$ret .= "</TR><TR>";
		}
		$curr_col++;
//--------------------------------------------------------------------------
		$onClick_Action = "";
		if (strlen($onClick) > 0)
		{
			$onClick_Action = $onClick;
			$onClick_Action = str_replace("##file##",$va,	$onClick_Action);
			$onClick_Action = str_replace("##path##",$path,	$onClick_Action);
		}
//--------------------------------------------------------------------------
		$active_href = "";
		if (strlen($href) > 0)
		{
			$active_href = " href=\"".$href."\" ";
			$active_href = str_replace("##file##",$va,		$active_href);
			$active_href = str_replace("##path##",$path,	$active_href);
		}
//--------------------------------------------------------------------------
		$active_td_styles = "";
		if (strlen($td_styles) > 0)
		{
			$active_td_styles = $td_styles;
			$active_td_styles = str_replace("##file##",$va,		$active_td_styles);
			$active_td_styles = str_replace("##path##",$path,	$active_td_styles);
		}
//--------------------------------------------------------------------------
		$active_img = "";
		if (strlen($img) > 0)
		{
			$active_img = $img;
			$active_img = str_replace("##file##",$va,		$active_img);
			$active_img = str_replace("##path##",$path,		$active_img);
		}
//--------------------------------------------------------------------------
		if (strlen($src) == 0)
		{
			$src = $path.$va;
		}
		$active_src = "";
		if (strlen($src) > 0)
		{
			$active_src = $src;
			$active_src = str_replace("##file##",$va,		$active_src);
			$active_src = str_replace("##path##",$path,		$active_src);
		}
//--------------------------------------------------------------------------
		$ret .= "<TD $active_td_styles><center>
					<a $active_href $onClick_Action>
							<img $active_img src=\"$active_src\">
					</a>
				</center>
				</TD>";
	}

	while ( $curr_col < $column )
		{
			$curr_col++;
			$ret .= "<TD $active_td_styles><center>
					&nbsp;
				</center>
				</TD>";
		}

return $ret;
}
/*
 t_send_mail($from, $to, $subject, $body);
*/
function t_send_mail($from, $to, $subject, $body, $mail_server = "mail.valmiera.lv", $mail_port="25", $user_name = "test2", $password = "salaga")
{
//$datums = date("j.m.Y, H:i");
include_once("Mail.php");
$headers["From"]    = "$from";
$headers["Subject"] = "$subject";
$params["host"] = "$mail_server";
$params["port"] = "$mail_port";
$params["auth"] = true;
if (isset($_SESSION['SERVER_REAL_IP'])) {
	$params["localhost"] = $_SESSION['SERVER_REAL_IP'];
}
$params["username"] = "$user_name";
$params["password"] = "$password";
$params["debug"] = true;

//$headers['Content-type'] = "text/plain";
//$headers['Š�harset'] = 'utf-8';
//$headers['Content-Transfer-Encoding'] = "8bit";
$headers["Content-Type"] = "text/plain; charset=\"UTF-8\"";
$headers["Content-Transfer-Encoding"] = "Quoted-Printable";
//$message .= "Content-Type: text/html; charset=UFT-8\n";
//$message .= "Content-Transfer-Encoding: Quoted-Printable\n";
$mail_object =& Mail::factory("smtp", $params);
$mail_object->send($to, $headers, $body);

}
function ebr($ko)
{
	echo "$ko<BR>";
}
function ren_lv_chars($kam)
{
	$ret = $kam;
	$ret = str_replace("Ä�","a",$ret);
	$ret = str_replace("Ä¨","c",$ret);
	$ret = str_replace("Ä“","e",$ret);
	$ret = str_replace("Ä£","g",$ret);
	$ret = str_replace("Ä«","i",$ret);
	$ret = str_replace("Ä·","k",$ret);
	$ret = str_replace("Ä¼","l",$ret);
	$ret = str_replace("Å†","n",$ret);
	$ret = str_replace("Å�","s",$ret);
	$ret = str_replace("Å«","u",$ret);
	$ret = str_replace("Å¾","z",$ret);

	$ret = str_replace("Ä€","A",$ret);
	$ret = str_replace("Ä�","C",$ret);
	$ret = str_replace("Ä’","E",$ret);
	$ret = str_replace("Ä¢","G",$ret);
	$ret = str_replace("ÄŖ","I",$ret);
	$ret = str_replace("Ä¶","K",$ret);
	$ret = str_replace("Ä»","L",$ret);
	$ret = str_replace("Å…","N",$ret);
	$ret = str_replace("Å ","S",$ret);
	$ret = str_replace("ÅŖ","U",$ret);
	$ret = str_replace("Å½","Z",$ret);

	return $ret;
}
function ren_lv_chars_chat($kam)
{
	$ret = $kam;
	$ret = str_replace("Ä�","aa",$ret);
	$ret = str_replace("Ä¨","ch",$ret);
	$ret = str_replace("Ä“","ee",$ret);
	$ret = str_replace("Ä£","gj",$ret);
	$ret = str_replace("Ä«","ii",$ret);
	$ret = str_replace("Ä·","kj",$ret);
	$ret = str_replace("Ä¼","lj",$ret);
	$ret = str_replace("Å†","nj",$ret);
	$ret = str_replace("Å�","sh",$ret);
	$ret = str_replace("Å«","uu",$ret);
	$ret = str_replace("Å¾","zh",$ret);

	$ret = str_replace("Ä€","Aa",$ret);
	$ret = str_replace("Ä�","Ch",$ret);
	$ret = str_replace("Ä’","Ee",$ret);
	$ret = str_replace("Ä¢","Gj",$ret);
	$ret = str_replace("ÄŖ","Ii",$ret);
	$ret = str_replace("Ä¶","Kj",$ret);
	$ret = str_replace("Ä»","Lj",$ret);
	$ret = str_replace("Å…","Nj",$ret);
	$ret = str_replace("Å ","Sh",$ret);
	$ret = str_replace("ÅŖ","Uu",$ret);
	$ret = str_replace("Å½","Zh",$ret);

	return $ret;
}

function ja_ir_nav($kam)
{
if ($kam == 1)
{
	$ret = "ir";
} else {
	$ret = "nav";
}
return $ret;
}
function ja_ir()
{
	$ret = "";
	$cancel = 0;
	$numargs = func_num_args();
	for ($a = 0; $a < $numargs; $a++)
	{
		$argum = func_get_arg($a);
		if (strlen($argum) > 0)
		{
			$ret .= $argum;
		} else {
			$cancel = 1;
		}
	}
	if ($cancel == 1)
	{
		$ret = "";
	}
	return $ret;
}
function get_act_db()
{
	global $_SESSION;
	$rrr = $_SESSION['props'];
	return $rrr['db'];
}
//pages($cik = 1, $ko, $tabula = NULL, $where = NULL, $neradities = NULL, $def_link = NULL)
//pages($_SESSION['CIK_RADIT_LAPAA'], "*",  "web", " ip = '$client_aipi' GROUP BY adress order by date desc ");
function split_rc($lauks = "date", $tabula = NULL, $ko = "*", $where = NULL, $dalit = true, $dalit_cik = 10)
{
	$ret = "";
	$mazais_platums = 10;
	$lielais_platums = 40;
	if ($tabula == NULL) { die('Wrong usage of function funkc.php->split_rc()'); }
	$dati = selasoc($ko,$tabula,$where);
	global $_GET;
//	$_GET = $_GET;
	$get_augsha = get_get(NULL,NULL,NULL,array("gads"=>"s","menesis"=>"s","diena"=>"s"));
	if (!($lauks == null))
	{ // Jataisa sadalijumspa datumiem
		$ret .= "<TABLE CELLPADDING=0 CELLSPACING=0><TR><TD><TABLE CELLPADDING=1 CELLSPACING=1><TR>";
		// GADI
		$gadi = selasoc("SELECT YEAR($lauks)as gads FROM $tabula group by gads");
		$act_gads = dabuu_gadu_arhiivam($_GET);
		$aktivs = "#CCFF99";
		$neaktivs = "#f0f0f0";
		while (list($key, $v) = @each($gadi))
		{
			$gads = $v['gads'];
			if ($act_gads == $gads)
			{
				$krasa = $aktivs;
			} else
			{
				$krasa = $neaktivs;
			}
			$ret .= "<TD width=50 BGCOLOR=\"$krasa\" WIDTH=$lielais_platums><a class=\"saite_datumiem\" href=\"?$get_augsha&gads=$gads\">$gads</a></TD>";
		}

		$ret .= "</TR></TABLE></TD><TD></TD></TR>";
		$ret .= "<TR><TD><TABLE CELLPADDING=1 CELLSPACING=1><TR>";
		// Meneshi

		$act_menesis = dabuu_menesi_arhiivam($_GET);
		$menesi = selasoc("SELECT MONTH($lauks)as menesis FROM $tabula WHERE YEAR(date) = $act_gads group by menesis");

		$meneshu_TD = "";

		while (list($key, $v) = @each($menesi))
		{
			$a_menesis = $v['menesis'];
			if ($act_menesis == $a_menesis)
			{
				$krasa = $aktivs;
			} else
			{
				$krasa = $neaktivs;
			}
			$meneshu_TD[$a_menesis] = "<TD BGCOLOR=\"$krasa\" WIDTH=$mazais_platums><a class=\"saite_datumiem\" href=\"?$get_augsha&gads=$gads&menesis=$a_menesis\">$a_menesis</a></TD>";
		}

		for ($rre = 1; $rre < 13; $rre++)
		{
			if (strlen($meneshu_TD[$rre]) > 0)
			{
				$ret .= $meneshu_TD[$rre];
			} else
			{
				$ret .= "<TD width=$mazais_platums><a class=\"saite_datumiem\">$rre</a></TD>";
			}
		}

		$ret .= "</TR></TABLE></TD><TD></TD></TR>";
		$ret .= "<TR>";
		$ret .= "<TD></TD></TR>";
		$ret .= "<TR><TD><TABLE CELLPADDING=1 CELLSPACING=1><TR>";
		// Dienas

		$act_diena = dabuu_dienu_arhiivam($_GET);
		$dienas = selasoc("SELECT DAYOFMONTH($lauks)as diena FROM $tabula WHERE YEAR(date) = $act_gads and MONTH(date) = $act_menesis group by diena");
		$dienu_TD = "";
		while (list($key, $v) = @each($dienas))
		{
			$a_diena = $v['diena'];
			if ($act_diena == $a_diena)
			{
				$krasa = $aktivs;
			} else
			{
				$krasa = $neaktivs;
			}
			$dienu_TD[$a_diena] = "<TD BGCOLOR=\"$krasa\" WIDTH=$mazais_platums><a class=\"saite_datumiem\" href=\"?$get_augsha&gads=$gads&menesis=$a_menesis&diena=$a_diena\">$a_diena</a></TD>";
		}
		//alert(date('t'));
		for ($rre = 1; $rre <= 31; $rre++)
		{
			if (strlen($dienu_TD[$rre]) > 0)
			{
				$ret .= $dienu_TD[$rre];
			} else
			{
				$ret .= "<TD width=$mazais_platums><a class=\"saite_datumiem\">$rre</a></TD>";
			}
		}

		$ret .= "</TR></TABLE></TD><TD></TD></TR>";
		$ret .= "</TABLE>";
	}

	if (strlen($where) > 0)
	{
		$wheriess = " and $where ";
	} else
	{
		$wheriess = " ";
	}

	if ($dalit == true)
	{ // Jadala pa lapaam
		$lapaz = pages($dalit_cik , "$ko",  "$tabula", "TO_DAYS($lauks) = TO_DAYS('$act_gads-$act_menesis-$act_diena') $wheriess ", true);
		$ret .= $lapaz["tabula"];
		//echo "".$lapaz["tabula"];
	}

	$selekts = "SELECT * FROM $tabula where TO_DAYS($lauks) = TO_DAYS('$act_gads-$act_menesis-$act_diena') $wheriess ".$lapaz['limit'];
	//echo $selekts;
	$visi_dati = selasoc("$selekts");

	$rety['data'] = $visi_dati;
	$rety['tabula'] = $ret;
	$rety['table'] = $ret;
	$rety['datums'] = "$act_gads-$act_menesis-$act_diena";
	$rety['date'] = "$act_gads-$act_menesis-$act_diena";
	return $rety;
}
function get_vared_or($field_id,$data_array, $get_field = "id", $type = "or")
{
$ret = "";
$biju = "";
	while (list($key, $v) = @each($data_array))
	{
		$val = $v[$get_field];
		$ret .= " $biju $field_id = $val ";
		$biju = $type;
	}
return $ret;
}
function alert($ko)
{
	echo "
		<SCRIPT>
		 alert('$ko');
		</SCRIPT>
	";
}

function array_combineA($ArrOld, $ArrNew){
	//$ret = Array();
	$sizeOld = count($ArrOld);
	$sizeNew = count($ArrNew);
	$OldBigger = true;

/* vajag taisÄ«t tÄ�:
	1: uztaisam jaunu Array
	2: izejam cauri vecajam Array
	3: izejam cauri jaunajam Array un viss!!! :)

	.. bet kaut kas vÄ“l nav...!!!
	TODO
*/

$rab = Array();
reset($ArrOld);
while (list($k, $v) = @each($ArrOld))
{
	$rab[$k] = $v;
}
reset($ArrNew);
while (list($k, $v) = @each($ArrNew))
{
	if ((isset($rab[$k])) && (is_array($v))) {
		$rab[$k] = array_combineA($rab[$k],$v);
	} else {
		$rab[$k] = $v;
	}
//	$rab[$k] = $v;
}

$ret = $rab;

//....................................
/*	$ret = $ArrOld;
	if (is_array($ArrNew)) {
		reset($ArrNew);
		while (list($k, $v) = @each($ArrNew))
		{
			if (isset($ret[$k])) {
			// KEY ierksts IR, skatamies uz VertÄ«bÄ�m
				if ($v == $ret[$k]) {

				} else {
					$ret[$k] = array_combineA($ret[$k], $v);
				}
			} else {
				$ret[$k] = $v;
			}
		}
	} else {
		$ret = $ArrNew;
	}
//....................................
*/
	return $ret;
}

function reload($kur,$targets = null,$sleep = 0)
{
	if (isset($kur))
	{
		$locis= $kur;
	} else
	{
		//dm($_SERVER);
		//$locis = "?".get_getB();
		$locis = "?".get_getNx();
		//$locis = $_SERVER['HTTP_REFERER'];
	}
	$taa = "";
	if ($targets != null)
	{
		$taa = "$targets.";
	}
	sleep($sleep);
	if (headers_sent()) {
		die("
			<SCRIPT>
				".$taa."document.location = '$locis'
			</SCRIPT>
			");
	} else {
		//echo "<hr>$locis<hr>";
		header("Location: $locis");
		die();
	}
}

function reloadH($kur,$targets = null,$sleep = 0)
{
	if (isset($kur))
	{
		$locis= $kur;
	} else
	{
		//dm($_SERVER);
		//$locis = "?".get_getB();
		$locis = "?".get_getNx();
		//$locis = $_SERVER['HTTP_REFERER'];
	}
	$taa = "";
	if ($targets != null)
	{
		$taa = "$targets.";
	}
	sleep($sleep);
	if (strlen($locis) == 0) {
		$locis = "#";
	}
	if (headers_sent()) {
		die("
			<SCRIPT>
				".$taa."document.location = '$locis'
			</SCRIPT>
			");
	} else {
		//echo "<hr>$locis<hr>";
		header("Location: $locis");
		die();
	}
}

function file_copy($file_origin, $destination_directory, $file_destination, $overwrite, $fatal) {

if ($fatal) {
$error_prefix = 'FATAL: File copy of \'' . $file_origin . '\' to \'' . $destination_directory . $file_destination . '\' failed.';
$fp = @fopen($file_origin, "r");
if (!$fp) {
echo $error_prefix . ' Originating file cannot be read or does not exist.';
exit();
}

$dir_check = @is_writeable($destination_directory);
if (!$dir_check) {
echo $error_prefix . ' Destination directory is not writeable or does not exist.';
exit();
}

$dest_file_exists = file_exists($destination_directory . $file_destination);
 if ($dest_file_exists) {
if ($overwrite) {
$fp = @is_writeable($destination_directory . $file_destination);
  if (!$fp) {
  echo  $error_prefix . ' Destination file is not writeable [OVERWRITE].';
  exit();
  }
  $copy_file = @copy($file_origin, $destination_directory . $file_destination);
  }
} else {
$copy_file = @copy($file_origin, $destination_directory . $file_destination);
}
     } else {
     $copy_file = @copy($file_origin, $destination_directory . $file_destination);
  }
return $copy_file;
}
function recursive_ls($listing, $directory, $count)
{
$dummy = $count;
if ($handle = opendir($directory))
{
	while ($file = readdir($handle))
	{
        if ($file=='.' || $file=='..') continue;
		else if ($h = @opendir($directory.$file."/"))
		{
			closedir($h);
			$count = -1;
			$listing["$file"] = array();
			recursive_ls($listing[$file], $directory.$file."/", $count + 1);
		}
		else
		{
			$listing[$dummy] = $file;
         	$dummy = $dummy + 1;}
	}
}
closedir($handle);
return ($listing);
}
function for_check(&$var)
{
	if ($var == 1)
	{
		$var = "checked";;
	} else
	{
		$var = "";
	}
}
function check_for_db(&$mas,$ko)
{
	$mas = 0;
	if ((isset($ko)) && (strlen($ko) > 0))
	{
		$mas = 1;
	}
}
function db_get()
{
// $lauks, $tabula, $where,$prim
	$ret = null;
	if (func_num_args() >= 1) {

		$ArgsArray = func_get_args();
		$lauks = "";
		$tabula = null;
		$where = null;
		$prim = null;

		if (func_num_args() >= 1) { $lauks = $ArgsArray[0]; }
		if (func_num_args() >= 2) { $tabula = $ArgsArray[1]; }
		if (func_num_args() >= 3) { $where = $ArgsArray[2]; }
		if (func_num_args() >= 4) { $prim = $ArgsArray[3]; }

		$mas = selasoc($lauks,$tabula,$where,$prim);

		if ((count($mas) > 0) && (is_array($mas))) {
			$mas = $mas[0];
		}
		if (is_array($lauks))
		{
			$ret = $mas;
		} else
		{
			if (isset($mas[$lauks])) {
				$ret = $mas[$lauks];
			} else {
				while (list($k, $v) = @each($mas))
				{
					$ret = $v;
				}
			}
		}
	}
	return $ret;

/*
	$ret = "";
	$mas = selasoc("$lauks",$tabula,$where,$prim);
	$mas = $mas[0];
	if (is_array($lauks))
	{
		$ret = $mas;
	} else
	{
		$ret = $mas[$lauks];
	}
	return $ret;
*/
}
function get_Xdiena($datums,$tagat_menesis)
{
	$tmp = $datums;
	$datums = substr($datums,strlen($datums) - 2,2);
	if (substr($datums,0,1) == "0")
	{
		$datums = substr($datums,1,1);
	}
	if (strlen($tagat_menesis) == 1)
	{
		$tagat_menesis = "0".$tagat_menesis;
	}
	//if (substr($tmp,5,2) != $tagat_menesis)
	{
		$datums = $datums.". ".get_gar_month(substr($tmp,5,2));
	}
	return $datums;
}
function get_gar_day($kurs)
{
$ret = "";
switch ($kurs)
{
case 1:
	$ret = "pirmdiena";
	break;
case 2:
	$ret = "otrdiena";
	break;
case 3:
	$ret = "treÅ�diena";
	break;
case 4:
	$ret = "ceturtdiena";
	break;
case 5:
	$ret = "piektdiena";
	break;
case 6:
	$ret = "sestdiena";
	break;
case 0:
	$ret = "svÄ“tdiena";
	break;
}
return $ret;
}
function get_gar_month($kurs, $kur = 0)
{
$ret= "";
if ($kur == 0)
{
	switch ($kurs)
	{
	case 1:
		$ret = "janvÄ�ris";
		break;
	case 2:
		$ret = "februÄ�ris";
		break;
	case 3:
		$ret = "marts";
		break;
	case 4:
		$ret = "aprÄ«lis";
		break;
	case 5:
		$ret = "maijs";
		break;
	case 6:
		$ret = "jÅ«nijs";
		break;
	case 7:
		$ret = "jÅ«lijs";
		break;
	case 8:
		$ret = "augusts";
		break;
	case 9:
			$ret = "septembris";
		break;
	case 10:
		$ret = "oktobris";
		break;
	case 11:
		$ret = "novembris";
		break;
	case 12:
		$ret = "decembris";
		break;
}
} else {
	switch ($kurs)
	{
	case 1:
		$ret = "janvÄ�rÄ«";
		break;
	case 2:
		$ret = "februÄ�rÄ«";
		break;
	case 3:
		$ret = "martÄ�";
		break;
	case 4:
		$ret = "aprÄ«lÄ«";
		break;
	case 5:
		$ret = "maijÄ�";
		break;
	case 6:
		$ret = "jÅ«nijÄ�";
		break;
	case 7:
		$ret = "jÅ«lijÄ�";
		break;
	case 8:
		$ret = "augustÄ�";
		break;
	case 9:
		$ret = "septembrÄ«";
		break;
	case 10:
		$ret = "oktobrÄ«";
		break;
	case 11:
		$ret = "novembrÄ«";
		break;
	case 12:
		$ret = "decembrÄ«";
		break;
}
}
return $ret;
}
if (function_exists('checkEmail')) {

} else {
function checkEmail($eMailAddress)
{
$atom = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]';    // allowed characters for part before "at" character
$domain = '([a-z]([-a-z0-9]*[a-z0-9]+)?)'; // allowed characters for part after "at" character

$regex = '^' . $atom . '+' .        // One or more atom characters.
'(\.' . $atom . '+)*'.              // Followed by zero or more dot separated sets of one or more atom characters.
'@'.                                // Followed by an "at" character.
'(' . $domain . '{1,63}\.)+'.        // Followed by one or max 63 domain characters (dot separated).
$domain . '{2,63}'.                  // Must be followed by one set consisting a period of two
'$';                                // or max 63 domain characters.

$regex = '^'.
'[-a-z0-9!#$%&\'*+/=?^_<{|}~]+'. // One or more underscore, alphanumeric, or allowed characters.
'(\.[-a-zA-Z0-9!#$%&\'*+/=?^_<{|}~]+)*'. // Followed by zero or more sets consisting of a period
 // of one or more underscore, alphanumeric, or allowed characters.
'@'. // Followed by an "at" character.
'[a-z0-9-]+'. // Followed by one or more alphanumeric or hyphen characters.
// here is the update
'(\.[a-z0-9-]+)*'. // May be followed by zero or more alphanumeric or hyphen characters.
'\.[a-z0-9-]{2,}'. // Must be followed by one sets consisting a period of two
// end of update
 // or more alphanumeric or hyphen characters.
'$'; // Each set of characters (without first set) must start with a dot

//    if (eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,3}$", $eMailAddress, $check)) {
$valid = false;
if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $eMailAddress)) {
$valid = false;
} else {
$valid = true;
}
return $valid;
 /*   if (ereg($regex , $eMailAddress, $check)) {
    }
    return false;
    */

}
} // exists
function dabu_dbd($tabula = NULL, $kolona  = "*", $where = NULL)
{
$ret = NULL;
if ($tabula == NULL)
	{
		// atgriez visas tabulas
		$tabula = last_used_table();
	}

	// atgriez to ko vajag
	//last_used_table($tabula);
	$ret = selasoc ("$kolona", "$tabula", $where);
	if ((count($ret) == 1) & (strlen($kolona) > 1))
		{
			//echo "KLJUDA!!!<BR>";
			$ret = $ret[0];
			$ret = $ret["$kolona"];
		}
return $ret;
}
function date_menesisg_gads($laiks, $nulle = true)
{
$gads=substr($laiks,0,4);
$menesis=substr($laiks,5,2);
if (substr_count($menesis,"-") > 0)
{
	$menesis=substr($laiks,6,1);
	$menesis = get_gar_month($menesis);
	$diena=substr($laiks,8,2);
	if (substr_count($diena," ") > 3)
	{
		$mazais = "";
		if ($nulle == true) {
			$mazais = "0";
		}
		$diena=$mazais.substr($laiks,8,1);
	}
}else
{
	$menesis = get_gar_month($menesis);
	$diena=substr($laiks,8,2);
	if ($diena < 10) {
		if ($nulle == false) {
			$diena = str_replace("0","",$diena);
		}
	}
}
$datums = "$gads. gada $diena. $menesis";
return $datums;

}
if (!function_exists('date1'))
{
function date1($l)
{
$laiks = $l;
settype($laiks,"string");

$gads=substr($laiks,0,4);
$menesis=substr($laiks,5,2);
if (substr_count($menesis,"-") > 0)
{
	$menesis="0".substr($laiks,6,1);
	$diena=substr($laiks,7,2);
	if (substr_count($diena," ") > 3)
	{
		$diena="0".substr($laiks,7,1);
	}
}else
{
	$diena=substr($laiks,8,2);
}

$datums=$diena.".".$menesis.".".$gads.".";
return $datums;

/*$laiks = $l;
settype($laiks,"string");

$gads=substr($laiks,0,4);
$menesis=substr($laiks,6,2);
if (substr_count($menesis,"-") > 0)
{
	$menesis="0".substr($laiks,6,1);
	$diena=substr($laiks,8,2);
	if (substr_count($diena," ") > 3)
	{
		$diena="0".substr($laiks,8,1);
	}
}else
{
	$diena=substr($laiks,7,2);
}

$datums=$diena.".".$menesis.".".$gads.".";
return $datums;
*/
}

}
if (!function_exists('time1'))
{
function time1($l)
{
//return date("m.d.Y H:i:s ",$l);
$laiks = $l;
settype($laiks,"string");
$stunda=substr($laiks,8,2);
$minute=substr($laiks,10,2);
$sekunde=substr($laiks,12,2);
$pulkst=$stunda.":".$minute.":".$sekunde;
return date1($l)." ".$pulkst;
}

}
if (!function_exists('file_get_contents'))
{

function file_get_contents($filename, $use_include_path = 0) {
 $fd = fopen ($filename, "rb", $use_include_path);
 $contents = fread($fd, filesize($filename));
 fclose($fd);
 return $contents;
}

}
/*
* Ja $arr = (array(rrr=>ttt))
* tad atgrie? ttt, ja $active == rrr
*/
function get_act_val($arr, $active = NULL)
{
$ret= "NULL";
if (is_array($arr))
	{
		while (list($key, $v) = @each($arr))
			{
				if (($active == $v) | ($active == $key))
				{ $ret = $v; }
			}
	}
else
	{
		echo "ERROR: Arguments nav masvivs ... [funkc.php->get_act_val] <BR>";
	}
	return $ret;
}
function build_arr_opt_vals($arr, $active = NULL)
{
$ret= "";
//dm($arr);
if (is_array($arr))
	{
		while (list($key, $v) = @each($arr))
			{
				if (($active == $v) | ($active == $key))
				{ $se = "selected=\"selected\""; } else { $se = ""; }
				if (is_array($v)) {
					//dm($v);
				} else
				{
				$ret .= "<option $se value=\"$key\">$v</option>";
				}
			}
	}
else
	{
		//echo "ERROR: Arguments nav masvivs ... [funkc.php->build_arr_opt_vals] <BR>";
	}
	return $ret;
}

function build_arr_opt_nokey_wselected($arr, $active = NULL){
$ret= Array();
$retOptions = "";
$npk = 0;
if (is_array($arr))
	{
		while (list($key, $v) = @each($arr))
			{
			$npk++;
			if ($npk == 1) {
				$ret['selected'] = $v;
			}
				if ($active == $v)
				{
					$se = "selected=\"selected\"";
					$ret['selected'] = $v;
				} else { $se = ""; }
				$retOptions .= "<option $se value=\"$v\">$v</option>";
			}
	}
else
	{
		echo "ERROR: Arguments nav masvivs ... [funkc.php->build_arr_opt] <BR>";
	}
	$ret['options'] = $retOptions;
	return $ret;
}

function build_arr_opt_nokey($arr, $active = NULL)
{
	$ret = build_arr_opt_nokey_wselected($arr,$active);
	return $ret['options'];
}
function build_arr_opt($arr, $active = NULL)
{
$ret= "";
if (is_array($arr))
	{
		while (list($key, $v) = @each($arr))
			{
				if (($active == $v) | ($active == $key))
				{ $se = "selected=\"selected\""; } else { $se = ""; }
				$ret .= "<option $se value=\"$v\">$v</option>";
			}
	}
else
	{
		echo "ERROR: Arguments nav masvivs ... [funkc.php->build_arr_opt] <BR>";
	}
	return $ret;
}
/* piemers:
* echo build_opt("pilsonji", "vards", 4);
* echo build_opt("kaki, laki", "vards",5,"kaki.kaka as id, laki.buu as vards");
*/
function build_opt($komanda = NULL, $kolona = NULL, $aidi = NULL, $lol = NULL, $wher = NULL)
// komanda = tabula, $kolona=kolona, aktivais id, lol=selekta pirmaa dalja, kolonnas
{
if ($lol == NULL)
{
$lol = "*";
}
$maaaa = selasoc ("$lol",$komanda,$wher);
$optionz = "";
while (list($key, $value) = @each($maaaa))
{
	 $komanda = $value[$kolona];
	 $id = $value['id'];
	 if ($id == $aidi)
	 	{ $seee = "selected=\"selected\""; }
	 else
	 	{ $seee = ""; }
	 $optionz =  $optionz."<option $seee value=\"$id\">$komanda</option>";
}
return $optionz;
}
function format_data_for_file($data)
{
$data = str_replace('\"',"\"",$data);
$data = str_replace('\\\'',"'",$data);
$data = str_replace('\\\\',"\\",$data);
return $data;
}
function inputam($text){ // lai nodotu stringus qwerijam!
	$text = str_replace('\"',"\'",$text);
	$text = str_replace('\'',"\'",$text);
	$text = str_replace('\\\\\'','\\\'',$text);
	return $text;
}
function outputam($text){ // lai paraditu resultu uz ekraana!!!
	$text = str_replace('\"',"&#34;",$text);
	$text = str_replace('\\\'',"'",$text);
	$text = str_replace("<", "&lt;", $text);
	$text = str_replace(">", "&gt;", $text);
	return $text;
}
function es($ko = 'id')
{
	$mas = get_reg_vals();
	return $mas["$ko"];
}
function atpakalj($celjs = NULL)
{
	if($celjs == NULL)
	{
		$server = $GLOBALS['HTTP_SERVER_VARS'];
		$jaut = strpos($server['HTTP_REFERER'],"?");
		$garums = strlen($server['HTTP_REFERER']);
		$lin = substr($server['HTTP_REFERER'], $jaut, $garums - $jaut);
	}
	else
	{
		$lin = "?$celjs";
	}
	add_link($lin);
	echo "&nbsp;<a class=\"krasa\" href=\"$lin\">Atpaka?</a>";
}
function debug_string($str)
{
return strip_tags(nl2br(stripslashes($str)), '<a><b><i><u><br>');
}
function pagars_datums_sodien()
{
$diena = date("D");
switch($diena){
	case "Mon":		$diena = "Pirmdiena";		break;
	case "Tue":		$diena = "Otrdiena";		break;
	case "Wed":		$diena = "TreÅ�diena";		break;
	case "Thu":		$diena = "Ceturtdiena";		break;
	case "Fri":		$diena = "Piektdiena";		break;
	case "Sat":		$diena = "Sestdiena";		break;
	case "Sun":		$diena = "SvÄ“tdiena";		break;
}
$menesis = date("m");
switch($menesis){
	case "01":		$menesis = "janvÄ�ris";	break;
	case "02":		$menesis = "februÄ�ris";	break;
	case "03":		$menesis = "marts";		break;
	case "04":		$menesis = "aprÄ«lis";	break;
	case "05":		$menesis = "maijs";		break;
	case "06":		$menesis = "jÅ«nijs";	break;
	case "07":		$menesis = "jÅ«lijs";	break;
	case "08":		$menesis = "augusts";	break;
	case "09":		$menesis = "septembris";break;
	case "10":		$menesis = "oktobris";	break;
	case "11":		$menesis = "novembris";	break;
	case "12":		$menesis = "decembris";	break;
}
$datums = date("j");
//$gads = date("Y");
return $diena." ".$datums.". ".$menesis;//." ".$gads;
}
function norm_date($datumz)
{
$ret = "";
// 2003-05-21   --> 21.05.2003
$datums = substr($datumz,8,2);
$menesis = substr($datumz,5,2);
$gads = substr($datumz,0,4);
return $datums.".".$menesis.".".$gads;
}
function norm_laiks($datumz)
{
$ret = "";
// 2003-05-22 04:11:49   --> 04:11:49
$laiks = substr($datumz,11,8);
return $laiks;
}
function pages_small_center($cik = 1, $ko, $tabula = NULL, $where = NULL, $neradities = NULL, $def_link = NULL)
{
//$_GET = $GLOBALS['_GET'];
$ma = selasoc($ko, $tabula, $where);
$kopaa = count($ma);
$vert = intval($kopaa)/intval($cik);
$lapas1 = round($vert); // lapu skaits
$kontr = $kopaa % $cik;
if(($kontr > 0) && (($lapas1 * $cik) < $kopaa)) { $lapas1++; }
	if ((isset($_GET['lapa'])) && ($_GET['lapa'] <= $lapas1))
		{	$lap = $_GET['lapa']; }
	else
		{ $lap = 1; }
	 // lap - aktiva lapa...
	$otrs = $cik * $lap;
	$pirmais = $otrs - $cik;
	$lapu_sky = 10;
	$pusse = round($lapu_sky/2);
	$lim = " LIMIT $pirmais , $cik ";
	$ret['limit'] = $lim;
	$getget = get_get($_GET, NULL, NULL, array("lapa"=>"c"));
	if(strlen($getget) < 2)
		{
			$getget = "$def_link";
		}
	$lapaam = "";
	$lapu_sakums = 0;
	$lapu_beigas = $lapas1;
	if (($lapu_beigas - $lapu_sakums) > $lapu_sky)
	{
		// ja lau skaits ir lielaks par noradito
		if ($lap <= $pusse)
		{
		// jarada pirmas n lapas
			$lapu_sakums = 0;
			$lapu_beigas = $lapu_sky;
		}
		else if ( $lap >= ($lapas1 - $pusse) )
		{
		// jarada pedejas n lapas
			$lapu_sakums = $lapas1 - $lapu_sky;
			$lapu_beigas = $lapas1;
		}
		else
		{
		// jarada n/2 uz katru pusi :)
			$lapu_sakums = $lap - $pusse;
			$lapu_beigas = $lap + $pusse;
		}
	}
	for($i = $lapu_sakums; $i < $lapu_beigas; $i++)
		{
			$ipl = $i + 1;
			if($ipl == $lap)
				{
					// ja aktivais links
					$links = " class=lapas style=\" color=#999999 \" ";
				}
			else
				{
					// vel spiezams links
			     	$links = "?$getget&amp;lapa=$ipl";
				 //   add_link($links);
					$links  = "href=\"".$links."\" class=lapas ";
				}
			if(strlen($lapaam) > 0)
				{
					$tuks = "&nbsp;";
				}
			else
				{
					$tuks = "";
				}
		 	$lapaam .= "$tuks<a $links>$ipl</a>";
		 }
$naakamaa ="?";
if($lap < $lapas1)
	{
    	$lapnak = "?$getget&amp;lapa=".($lap + 1);
		//add_link("$lapnak");
		$naakamaa ="<a href=\"$lapnak\" class=lapas >?</a>";
	}
$ieprieksejaa= "?";
if($lap -1 > 0)
	{
    	$lapnak = "?$getget&lapa=".($lap -1);
		//add_link("$lapnak");
		$ieprieksejaa = "<a href=\"$lapnak\" class=lapas>?</a>";
	}
$ret['tabula'] = "";
$lll = $_GET;
while (list($key, $value) = @each($lll))
        {
			$GET2[] = $key;
		}
$m2s = @array_intersect($neradities, $GET2);
$skaitz = count($m2s);
if(( $skaitz == 0 ) && ($lapas1 > 1))
	{
		$ret['tabula'] = "<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><td class=\"lapas\"><center>$ieprieksejaa&nbsp;$lapaam&nbsp;$naakamaa</center></td></tr></table>";
	}
return $ret;
}
function pages($cik = 1, $ko, $tabula = NULL, $where = NULL, $neradities = NULL, $def_link = NULL)
{
//$_GET = $GLOBALS['_GET_'];
if (!isset($_GET)) {
	global $_GET;
}
$ma = selasoc($ko, $tabula, $where);
$kopaa = count($ma);
$ret['total'] = $kopaa;
$vert = intval($kopaa)/intval($cik);
$lapas1 = round($vert); // lapu skaits
$kontr = $kopaa % $cik;
if(($kontr > 0) && (($lapas1 * $cik) < $kopaa)) { $lapas1++; }
	if ((isset($_GET['lapa'])) && ($_GET['lapa'] <= $lapas1))
		{	$lap = $_GET['lapa']; }
	else
		{ $lap = 1; }
	 // lap - aktiva lapa...
	$otrs = $cik * $lap;
	$pirmais = $otrs - $cik;
	$lapu_sky = 10;
	$pusse = round($lapu_sky/2);
	$lim = " LIMIT $pirmais , $cik ";
	$ret['limit'] = $lim;
	$getget = get_getN("lapa");
	if(strlen($getget) < 2)
		{
			$getget = "$def_link";
		}
	$lapaam = "";
	$lapu_sakums = 0;
	$lapu_beigas = $lapas1;
	if (($lapu_beigas - $lapu_sakums) > $lapu_sky)
	{
		// ja lau skaits ir lielaks par noradito
		if ($lap <= $pusse)
		{
		// jarada pirmas n lapas
			$lapu_sakums = 0;
			$lapu_beigas = $lapu_sky;
		}
		else if ( $lap >= ($lapas1 - $pusse) )
		{
		// jarada pedejas n lapas
			$lapu_sakums = $lapas1 - $lapu_sky;
			$lapu_beigas = $lapas1;
		}
		else
		{
		// jarada n/2 uz katru pusi :)
			$lapu_sakums = $lap - $pusse;
			$lapu_beigas = $lap + $pusse;
		}
	}
	for($i = $lapu_sakums; $i < $lapu_beigas; $i++)
		{
			$ipl = $i + 1;
			if($ipl == $lap)
				{
					// ja aktivais links
					$links = " class=lapas "; // style=\" color=#999999 \"
				}
			else
				{
					// vel spiezams links
			     	$links = "?$getget&amp;lapa=$ipl";
				 //   add_link($links);
					$links  = "href=\"".$links."\" class=lapas ";
				}
			if(strlen($lapaam) > 0)
				{
					$tuks = "&nbsp;";
				}
			else
				{
					$tuks = "";
				}
		 	$lapaam .= "$tuks<a $links>$ipl</a>";
		 }
$naakamaa ="&raquo;";
if($lap < $lapas1)
	{
    	$lapnak = "?$getget&amp;lapa=".($lap + 1);
		//add_link("$lapnak");
		$naakamaa ="<a href=\"$lapnak\" class=lapas >&raquo;</a>";
	}
$ieprieksejaa= "&laquo;";
if($lap -1 > 0)
	{
    	$lapnak = "?$getget&amp;lapa=".($lap -1);
		//add_link("$lapnak");
		$ieprieksejaa = "<a href=\"$lapnak\" class=lapas>&laquo;</a>";
	}
$ret['tabula'] = "";
$lll = $_GET;
$GET2 = Array();
while (list($key, $value) = @each($lll))
        {
			$GET2[] = $key;
		}

if (is_array($neradities)) {
	$ret['neradities'] = $neradities;
	$m2s = @array_intersect($neradities, $GET2);
} else {
	$m2s = $GET2;
	$ret['neraditiesGET'] = $GET2;
}
$skaitz = count($m2s);
$ret['piki'] = $skaitz;
//if(( $skaitz == 0 ) && ($lapas1 > 1))
if($lapas1 > 1)
	{
		$ret['tabula'] = "<table class=\"svitra\" width=\"100%\" cellpadding=\"2\" cellspacing=\"0\"><tr><td class=\"lapas\">$kopaa ieraksti kopÄ� ($lapas1 lapas)</td><td class=\"lapas\"><div align=\"right\">&nbsp;Lapas:&nbsp;$ieprieksejaa&nbsp;$lapaam&nbsp;$naakamaa</div></td></tr></table>";
	}
// bgcolor=\"#eeeeee\"
return $ret;
}

function run_sql($sql) {
	mysql_query($sql);
}

function select($ko, $padrukat = 0)
{
$result = mysql_query($ko);
$mas = "";
while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
 {
	while (list($key, $value) = @each($row))
    	{ $masivs[$key] = $value; }
	$mas[] = $masivs;
	$rindas++;
 }
if ($padrukat == 1)
{
	echo "[$ko]<BR>[records selected = $rindas ...]<BR>";
}
mysql_free_result($result);
return $mas;
}


function sel($ko="1",$no_kaa=NULL, $where=null )
{
$wh = "";
if ($where <> null ) { $wh = " WHERE $where "; }
if($no_kaa == NULL){
	$from = "";
} else
{
$from = "FROM ";
}
 $sql = "SELECT $ko $from $no_kaa $wh ";
 $result = mysql_query($sql);
 $mas = "";
 $rindas = 0;
while ($row = mysql_fetch_array($result, MYSQL_BOTH))
 {
	while (list($key, $value) = @each($row))
    	{ $masivs[$key] = $value; }
	$mas[] = $masivs;
	$rindas++;
 }
mysql_free_result($result);
return $mas;
}
function selasocc($ko,$no_kaa,$where,&$cik)
{
	return selasoc($ko,$no_kaa,$where,0,$tmp,$cik);
}

function sel1($Sql){
	$result = mysql_query($Sql);
//	echo $Sql;
	$cik = mysql_num_rows($result);
	$mas = Array();
	$rindas = 0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
 	{
		while (list($key, $value) = @each($row))
	    {
			$masivs[$key] = $value;
		}
			$mas[] = $masivs;
			$rindas++;
	}
	mysql_free_result($result);
	return $mas;
}

function sel2($fields, $table){
	$sql = "select $fields FROM $table ";
	return sel1($sql);
}

function sel3($fields, $table, $where = "1=1"){
	$sql = "select $fields FROM $table WHERE $where ";
	return sel1($sql);
}


/*
	@Param Query [field/s]
	[@Param Table name]
	[@Param Where String]
*/
function sqlsel(){
	$ret = Array();
	if (func_num_args() == 1) {
		$statement = func_get_arg(0);
		$ret = sel1($statement);
	}
	if (func_num_args() == 2) {
		$field = func_get_arg(0);
		$table = func_get_arg(1);
		$ret = sel2($field,$table);
	}
	if (func_num_args() == 3) {
		$field = func_get_arg(0);
		$table = func_get_arg(1);
		if (strLen(func_get_arg(2)) > 0) {
			$where = func_get_arg(2);
			$ret = sel3($field,$table,$where);
		} else {
			$ret = sel2($field,$table);
		}
	}


	return $ret;
}
function selasoc($ko="1",$no_kaa="tables", $where=null, $sql11 = 0, &$sqqlz = "", &$cik = 0 )
{
$rindas = 0;
if (($no_kaa == "tables") && ($where == NULL))
{
	$result = mysql_query($ko);
	$cik = mysql_num_rows($result);
	$mas = "";
while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
 {
	while (list($key, $value) = @each($row))
    	{ $masivs[$key] = $value; }
	$mas[] = $masivs;
	$rindas++;
 }
mysql_free_result($result);

}
elseif (($no_kaa == NULL) && ($where == NULL))
{
$sql = $ko;
if($sql11 == 1) echo "<pre>::
$sql
::</pre><br>";
 $result = mysql_query($sql);
 if (is_resource($result)) {
	 $cik = mysql_num_rows($result);
 }
 $Error = mysql_error();
 if ((strLen($Error) > 0)) {
 	echo "<b>MySQL Error:</b>".$Error."<br>";
 }
if($sql11 == 1) echo "::Returned rows: [$cik]::<br>";
 $mas = "";
 $rindas = 0;
while ($row = @mysql_fetch_array($result, MYSQL_ASSOC))
 {
	while (list($key, $value) = @each($row))
    	{ $masivs[$key] = $value; }
	$mas[] = $masivs;
	$rindas++;
 }
@mysql_free_result($result);
}
else
{
$wh = "";
if ($where <> null ) { $wh = " WHERE $where "; }
 $sql = "SELECT $ko FROM $no_kaa $wh ";
 if($sql11 == 1)
	 echo "<pre>::$sql::</pre><br>";
 $result = mysql_query($sql);
 $cik = 0;
 if ($result <> null) {
  	$cik = mysql_num_rows($result);
  }
 $mas = "";
 $rindas = 0;
while ($row = @mysql_fetch_array($result, MYSQL_ASSOC))
 {
	while (list($key, $value) = @each($row))
    	{ $masivs[$key] = $value; }
	$mas[] = $masivs;
	$rindas++;
 }
@mysql_free_result($result);
}
$sqqlz = $sql;
return $mas;
}
function br()
{
 echo "<br>";
}
function rak($ko)
{
 echo $ko;
}
function seldn($sel,$from,$where)
{
	$mass = selasoc($sel,$from,$where);
	echo drn($mass);
}
function seldrn($sel,$from,$where)
{
	$mass = selasoc($sel,$from,$where);
	return drn($mass);
}
function dn($masivs,$pos = 1)
{
	echo drn($masivs,$pos);
}
function drn($masivs,$pos = 1,$tabula = true)
{
// Pos = 1
/*
*  ---------------------------------------------
*  | id  | pirmais  | otrais | tresais | ...   |
*  ---------------------------------------------
*  | 1   | janis    | sdada  | 123123  | ...   |
*  | 2   | petjka   | 4s332  | 334231  | ...   |
*  ---------------------------------------------
*/
$ret = "";
if ($pos == 1)
{
	$ret = drn1($masivs,$pos,$tabula);
} elseif ($pos == 2)
{
	$ret = drn2($masivs,$pos,$tabula);
}
 return $ret;
}
function drn1($masivs,$pos = 1,$tabula = true)
{
// Pos = 1
/*
*  ---------------------------------------------
*  | id  | pirmais  | otrais | tresais | ...   |
*  ---------------------------------------------
*  | 1   | janis    | sdada  | 123123  | ...   |
*  | 2   | petjka   | 4s332  | 334231  | ...   |
*  ---------------------------------------------
*/
$ret = "";

 $npk = 0;
 if ($tabula == true)
 {
 $ret .= "
<table border='1' bordercolor=\"#000000\" cellpadding=2 cellspacing=0 width=100%>";
 }
 while (list($key, $value) = @each($masivs))
        {
        $bg = "#E2E1D6";
        if ($npk % 2 == 0)
         $bg = "#FFFFFF";
             if (!is_array($value))
             {
               //$ret .= "<tr bgcolor=\"$bg\"><td class=mazs_melns valign=top><u>$key</u></td><td class=mazs_melns>$value</td></tr>";
							 if (strlen($value) == 0)
							 {
								 $value = "&nbsp;";
							 }
							 $ret .= "<td class=mazs_melns>$value</td>";
                } else
                {

								if ($npk == 0)
								{ // J?druk? virsraksts...
									$ret .= "<tr>";
									$koloss = $value;
									 while (list($key_kol, $value_kol) = @each($koloss))
									 {
									 	$ret .= "<td>$key_kol</td>";
									 }
									$ret .= "</tr>";
								}

                 $skaitz = count($value);
                 $ret .= "<tr bgcolor=\"$bg\">";
                 $ret .= drn1($value,$pos,false);
                 $ret .= "</tr>";
                }
             $npk++;
        }
if ($tabula == true)
{
 $ret .= "</table>
 ";
}
 return $ret;
}
function drn2($masivs,$pos = 2,$tabula = true)
{
// Pos = 2
/*
*  --------------------------------------------------
*  | id      || 1        | 2      | 3       | ...   |
*  | pirmais || janis    | petjka | laila   | ...   |
*  | otrais  || de3322   | 4s332  | sdf231  | ...   |
*  --------------------------------------------------
*/
$ret = "";

 $npk = 0;
 if ($tabula == true)
 {
 $ret .= "
<table border='0' bordercolor=\"#000000\" bgcolor=\"#222222\" cellpadding=2 cellspacing=1 width=100%>";
 }
 $pirmais = $masivs[0];
 while (list($kolona, $value) = @each($pirmais))
 {
	$ret .= "<tr>";
	$ret .= "<td bgcolor=\"#999999\">$kolona</td>";
	reset($masivs);
	$npk = 0;
	while (list($key, $val) = @each($masivs))
	{
			$bg = "#E2E1D6";
        if ($npk % 2 == 0)
         $bg = "#FFFFFF";
		$vals = $val["$kolona"];
		if (strlen($vals) == 0)
		{
			$vals = "&nbsp;";
		}
		$ret .= "<td bgcolor=\"$bg\">$vals</td>";
		$npk++;
	}
	$ret .= "</tr>";
 }
if ($tabula == true)
{
 $ret .= "</table>
 ";
}
 return $ret;
}
function dm($masivs)
{
	druka_masivu($masivs);
}
function druka_masivu($masivs)
{
	echo drm($masivs);
}
function drm($masivs, $TAB = "")
{
$ret = "";
 $ret .= "
$TAB<!-- masiva drukashana  -->
$TAB<table border='0' bordercolor=\"#000000\" bgcolor=\"#000000\" cellpadding=\"2\" cellspacing=\"1\" width=100%>\n";
 $npk = 0;
 while (list($key, $value) = @each($masivs))
        {
        $bg = "#E2E1D6";
        if ($npk % 2 == 0)
         $bg = "#FFFFFF";
             if (!is_array($value))
             {
             	if (is_object($value)) {
             		$ClassName = get_class($value);
             		$value = $ClassName;
             		$value .= "<br><b>Variables:</b><br>";
             		$value .= drm(get_class_vars($ClassName));
             		$value .= "<b>Methods:</b><br>";
             		$value .= drm(get_class_methods($ClassName));
             	}
             	if ($key == "passw") {
             		$value = str_repeat("*",strlen($value));
             	}
             	if ($key == "password") {
             		$value = str_repeat("*",strlen($value));
             	}

               $ret .= "$TAB	<tr bgcolor=\"$bg\">
$TAB		<td class=mazs_melns valign=top><u>$key</u></td>
$TAB		<td class=mazs_melns>$value</td>
$TAB	</tr>\n";
                } else
                {
               $skaitz = count($value);
               $ret .= "$TAB	<tr bgcolor=\"$bg\">
$TAB		<td class=mazs_melns valign=top><u>Array: $key  ($skaitz)</u></td>
$TAB		<td class=mazs_melns>\n";
               $ret .= drm($value,$TAB."	");
               $ret .= "$TAB		</td>
$TAB	</tr>\n";
                }
             $npk++;
        }
 $ret .= "$TAB</table>
$TAB<!-- masiva drukashana beidzas -->\n
";
 return $ret;
}
 function conecttodb($user, $parole, $db)
 {
/* if ($sessija['k_registrets'] > -1)
 {
  $db = mysql_connect("localhost","regusr","girlfriend")
   or die("Nevareu piesl?gties datu b?zei k? re?istr?ts lietot?js...");
 }
  else
  {
  $db = mysql_connect("localhost","pubusr","12345")
   or die("Nevareu piesl?gties datu b?zei k? publisks lietot?js...");
  }
  */
 mysql_connect("localhost","$user","$parole")
   or die("Nevareu piesl?gties datu b?zei k? re?istr?ts lietot?js...");
 mysql_select_db("$db")
       or exit("Could not select database");
 }
function doco($props)
{
//$celjsh = get_getA();
$ret = mysql_connect($props['host'],$props['user'],$props['passw'],true) or die("<HTML><HEAD><meta http-equiv=\"refresh\" content=\"100; url=?$celjsh\"></HEAD><BODY></BODY></HTML>");
mysql_select_db($props['db'],$ret) or exit("...");
mysql_query("set names 'UTF8'",$ret);
return $ret;
}
/**
 *  function doconect($sessija)
 {
/// if ($sessija['k_registrets'] > -1)
 {
  $db = mysql_connect("localhost","regusr","girlfriend")
   or die("Nevareu piesl?gties datu b?zei k? re?istr?ts lietot?js...");
 }
  else
  {
  $db = mysql_connect("localhost","pubusr","12345")
   or die("Nevareu piesl?gties datu b?zei k? publisks lietot?js...");
  }
  ///
 mysql_connect("10.20.30.5","toms","parole")
   or die("Nevareu piesl?gties datu b?zei k? re?istr?ts lietot?js...");
 mysql_select_db("adreses")
       or exit("Could not select database");
 }
 */

function ArrayValueExists($findVal,$walkThroughArray){
	$ret = false;
	//echo "Finding: Value=[$findVal]<br>";
	//dm($walkThroughArray);
	reset($walkThroughArray);
	while (list($key, $value) = @each($walkThroughArray))
    {
    	if ($value == $findVal) {
    		$ret = true;
    		break;
		}
    }
    $ret2 = "false";
    if ($ret == true) { $ret2 = "true"; }
	//echo"<br>[$ret2]<hr>";
	return $ret;
}
function ArrayKeyExists($findKey,$walkThroughArray){
	$ret = false;
	if (is_array($walkThroughArray)) {
		reset($walkThroughArray);
		while (list($key, $value) = @each($walkThroughArray))
    	{
	    	if ($value == $findKey) {
	    		$ret = true;
	    		break;
			}
	    }
	}
	return $ret;
}

function get_getA($neeArray = NULL){
	$ret = "";
	$_GET2 = $_GET;
	$bija = "";
	while (list($key, $value) = @each($_GET2))
    {
		if (ArrayKeyExists($key,$neeArray) === false)
		{
			if (strlen($value) == 0)
			{
				$ret .= $bija.$key;
			}
			 else
			 {
				$ret .= $bija.$key."=".$value;
			}
			$bija = "&amp;";
		}
    }
	return $ret;
}

function get_getAx($neeArray = NULL){
	$ret = "";
	$_GET2 = $_GET;
	$bija = "";
	while (list($key, $value) = @each($_GET2))
    {
		if (ArrayKeyExists($key,$neeArray) === false)
		{
			if (strlen($value) == 0)
			{
				$ret .= $bija.$key;
			}
			 else
			 {
				$ret .= $bija.$key."=".$value;
			}
			$bija = "&";
		}
    }
	return $ret;
}

function get_getB($JaaArray = NULL){
	$ret = "";
	$_GET2 = $_GET;
	$bija = "";
	reset($_GET2);
	while (list($key, $value) = @each($_GET2))
    {
    	reset($JaaArray);
		if ((ArrayKeyExists($key,$JaaArray) === true) or (count($JaaArray) == 0))
		{
			if (strlen($value) == 0)
			{
				$ret .= $bija.$key;
			}
			else
			{
				$ret .= $bija.$key."=".$value;
			}
			$bija = "&amp;";
		}
    }
	return $ret;
}
function get_getBx($JaaArray = NULL){
	$ret = "";
	$_GET2 = $_GET;
	$bija = "";
	reset($_GET2);
	while (list($key, $value) = @each($_GET2))
    {
    	reset($JaaArray);
		if ((ArrayKeyExists($key,$JaaArray) === true) or (count($JaaArray) == 0))
		{
			if (strlen($value) == 0)
			{
				$ret .= $bija.$key;
			}
			else
			{
				$ret .= $bija.$key."=".$value;
			}
			$bija = "&";
		}
    }
	return $ret;
}

function get_getY(){
	$ret = "";
	if (func_num_args() > 0) {
		$ArgsArray = func_get_args();
		$ArgsArrayGUT = Array();
		while (list($keyq, $valueq) = @each($ArgsArray))
	    {
	    	$ArgsArrayGUT[$valueq] = $valueq;
	    }
		$ret .= get_getB($ArgsArrayGUT);
	} else {
		$ret .= get_getA();
	}
	return $ret;
}

function get_getYx(){
	$ret = "";
	if (func_num_args() > 0) {
		$ArgsArray = func_get_args();
		$ArgsArrayGUT = Array();
		while (list($keyq, $valueq) = @each($ArgsArray))
	    {
	    	$ArgsArrayGUT[$valueq] = $valueq;
	    }
		$ret .= get_getBx($ArgsArrayGUT);
	} else {
		$ret .= get_getAx();
	}
	return $ret;
}

function get_getN(){
	$ret = "";
	if (func_num_args() > 0) {
		$ArgsArray = func_get_args();
		$ArgsArrayGUT = Array();
		while (list($key, $value) = @each($ArgsArray))
	    {
	    	$ArgsArrayGUT[$value] = $value;
	    }
		$ret = get_getA($ArgsArrayGUT);
	} else {
		$ret = get_getA();
	}
	return $ret;
}

function get_getNx(){
	$ret = "";
	if (func_num_args() > 0) {
		$ArgsArray = func_get_args();
		$ArgsArrayGUT = Array();
		while (list($key, $value) = @each($ArgsArray))
	    {
	    	$ArgsArrayGUT[$value] = $value;
	    }
		$ret = get_getAx($ArgsArrayGUT);
	} else {
		$ret = get_getAx();
	}
	return $ret;
}

function get_getNf(){
	$ret = "";
	if (func_num_args() > 0) {
		$ArgsArray = func_get_args();
		$ArgsArrayGUT = Array();
		while (list($key, $value) = @each($ArgsArray))
	    {
	    	$ArgsArrayGUT[$value] = $value;
	    }
		$ret = get_getA($ArgsArrayGUT);
	} else {
		$ret = get_getA();
	}
	$Parts = explode("&",$ret);
	$ret2 = "";
	while (list($ke, $ve) = @each($Parts))
	{
		$rx = explode("=",$ve);
		$kex = $rx[0];
		$valx = $rx[1];
		$ret2 .= "<input type=\"hidden\" name=\"$kex\" value=\"$valx\">";
	}
	return $ret2;
}

function get_getOld($_GET_VAR = NULL, $minus = NULL, $cik = NULL, $nee = NULL, $obligatie = NULL)
{
if (isset($_GET_VAR) == false)
{
	$_GET_VAR = NULL;
	//echo "<hr>";
	while (list($key, $value) = @each($_GET))
        {
			//echo "$key = $value <br>";
        	$_GET_VAR[$key] = $value;
        }
	//echo "<hr>";
}
$nee["SESSIJA"] = "s";
$ret = "";
$bija = -1;
$skaits = count($_GET_VAR);
$liidz = $skaits;

if($liidz == 0){
	$liidz = count($obligatie);
} else
{
 if(count($obligatie) > 0){
 	$liidz += count($obligatie);
 }
}
if (!($minus == NULL))
{
  if ($minus < 0) { $minus = $minus * -1; }
  $liidz = $skaits-$minus;
}
if (!($cik == NULL))
{
  if ($minus < 0) { $minus = $minus * -1; }
  $liidz = $cik;
}
$npk = 0;
$geti = $_GET_VAR;
while (list($key, $value) = @each($obligatie))
        {
			if (!(array_key_exists($key, $geti)))
				{
					$geti[$key] = $value;
				}
		}
reset($geti);
//dm($geti);
 while (list($key, $value) = @each($geti))
        {
			if (!(array_key_exists($key, $nee)))
			{
				if ($bija == 0) $and = "&"; else $and = "";
					$bija = 0;
				if ($npk < $liidz) {
					if (strLen($value) > 0) {
						$ret .= $and.$key."=".$value;
					} else {
						$ret .= $and.$key;
					}
				}
				$npk ++;
			}
		}
//		echo "<hr>";

//	echo $ret;
//		echo "<hr>";

$ret = ren_lv_chars($ret);
return $ret;
}

function dabuu_gadu_arhiivam($_GET2)
{
if (!(isset($_GET2['gads'])))
{ $gads = date('Y'); } else { $gads = $_GET2['gads']; }
return $gads;
}

function dabuu_menesi_arhiivam($_GET2)
{
$gads = dabuu_gadu_arhiivam($_GET2);
if (!(isset($_GET2['menesis'])))
{  	if ($gads == date('Y')) { $menesis = date('m'); } else { $menesis = "01"; }
} else { $menesis = $_GET2['menesis']; }
return $menesis;
}

function dabuu_dienu_arhiivam($_GET2)
{
$gads = dabuu_gadu_arhiivam($_GET2);
$menesis = dabuu_menesi_arhiivam($_GET2);
if (!(isset($_GET2['diena'])))
{  	if (($gads == date('Y')) && ($menesis == date('m'))) { $diena = date('d'); } else { $diena = "01"; }
} else { $diena = $_GET2['diena']; }
return $diena;
}
function ipCheck() {
		$ip = "";
        if (getenv('HTTP_CLIENT_IP')) {
            $ip .= getenv('HTTP_CLIENT_IP')." ";
        }
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip .= getenv('HTTP_X_FORWARDED_FOR')." ";
        }
        if (getenv('HTTP_X_FORWARDED')) {
            $ip .= getenv('HTTP_X_FORWARDED')." ";
        }
        if (getenv('HTTP_FORWARDED_FOR')) {
            $ip .= getenv('HTTP_FORWARDED_FOR')." ";
        }
        if (getenv('HTTP_FORWARDED')) {
            $ip .= getenv('HTTP_FORWARDED')." ";
        }
        else {
            $ip .= $_SERVER['REMOTE_ADDR']." ";
        }
        return trim($ip);
}
function randomString($garums = 5,$tips = 0){
	$ret = "";
	if ($tips == 0) {
		$AllStrings = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";
	} elseif ($tips == 1) {
		$AllStrings = "1234567890";
	} elseif ($tips == 2) {
		$AllStrings = "QWERTYUIOPASDFGHJKLZXCVBNM";
	} elseif ($tips == 3) {
		$AllStrings = "qwertyuiopasdfghjklzxcvbnm";
	} elseif ($tips == 4) {
		$AllStrings = "qwertyuiopasdfghjklzxcvbnm1234567890";
	} elseif ($tips == 5) {
		$AllStrings = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890";
	}
	for ($q=0; $q < $garums; $q++)
	{
		$randis = rand(0,strLen($AllStrings)-1);
		$ret .= $AllStrings[$randis];
	}
	return $ret;
}

function getResizedImgPath($srcPath, $sourceImg, $sizeW, $sizeH, $userFolder, $pubTralslationFile){
	$ret = "";
	$DestDirSmall = $sizeW."x".$sizeH;
	$DestDir = $srcPath.$DestDirSmall;
	if (is_dir($DestDir) == false) {
		try {
		mkdir($DestDir);
		} catch (Exception $e) { echo $e->getMessage()."\n"; }
	}
	$DestDirPath = $DestDir."/";
	$DestFile = $DestDirPath.$sourceImg;
	//echo "$DestFile<br>";
	$FileCreated = false;
	if (is_file($DestFile) == false) {
		// create small file
		if (strLen(getImageData($srcPath.$sourceImg,'type' )) > 0) {
			global $PORTAL_INCLUDES_PATH;
			include_once($PORTAL_INCLUDES_PATH."thumbnail.class.php");
			$myThumb = new Thumbnail; // Start using a class
			$myThumb->setMaxSize( $sizeW, $sizeH ); // Specify maximum size (width, height)
			$myThumb->setImgSource(	$srcPath.$sourceImg ); // Specify original image filename
			$myThumb->Create( $DestFile ); // Specify destination image filename or leave empty to output directly
			$FileCreated = true;
		}
	} else {
		$FileCreated = true;
	}
	if ($FileCreated) {
		$ret .= "dir=$userFolder&w=$sizeW&h=$sizeH&file=$sourceImg";
	} else {
		$ret .= "file=notImg.jpg";
	}

	$ret = $pubTralslationFile."?".$ret;
	return $ret;
}

/*
while (list($key, $va) = @each($mas))
	{
*/
////////echo "1234567890";
?>