<?php
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
* IT Tilts
* Versija 2.1
* Labots 15.05.2011
*/
$INCLUDED[] = "ML_Classes included";

include_once('TzTable.class.php');

//-------------------------------------------------------------------------------------------------------------
class ServiceFields {

/*
DROP TABLE IF EXISTS `pagetexts`;
CREATE TABLE  `pagetexts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `info` varchar(255) default NULL,
  `longer` text,
  `code` varchar(50) NOT NULL,
  `description` varchar(100) default NULL,
  `module` varchar(15) default NULL,
  `infoEN` varchar(255) default NULL,
  `infoRU` varchar(255) default NULL,
  `longerEN` text,
  `longerRU` text,
  PRIMARY KEY  USING BTREE (`id`,`code`),
  KEY `index_2` (`code`),
  KEY `index_3` (`module`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
*/

var $GLOBALDatabaseName = "";
var $GLOBALTableName = "pagetexts";

function __construct($DatabaseName, $TableName){
	$this->GLOBALDatabaseName = $DatabaseName;
	$this->GLOBALTableName = $TableName;
}

function getModuleTexts($moduleName = "") {
	$ret = "";
	$ret .= "<fieldset>
		<legend>Statiskie teksti</legend>";

	$TableXX = $this->GLOBALTableName;
	$DatabaseXX = $this->GLOBALDatabaseName;

	if (isset($_POST['new_code_erdsdderewqert543w'])) {
		//$ret .= drm($_POST);
		$dat['code'] = inputam($_POST['new_code_erdsdderewqert543w']);
		if ((isset($_POST['new_code_fromOthererderewqert543w'])) && (strlen($_POST['new_code_fromOthererderewqert543w']) > 0)) {
			$idixx = inputam($_POST['new_code_fromOthererderewqert543w']);
			$froms = db_get("module",ja_ir($DatabaseXX,".").$TableXX," id = $idixx ");
			$dat['module'] = $froms;
		} else {
			$dat['module'] = $moduleName;
		}
		$er = new sql_action($DatabaseXX,$TableXX);
		//$er->print_sql();
		$er->insert_row($dat);
		$newIDee = db_get("id",ja_ir($DatabaseXX,".").$TableXX,"code = '".$dat['code']."' ");
		$noLook = get_getNx("look");
		reload("?$noLook&look=$newIDee");
	}

	if (isset($_GET['look'])) {
		$aide = inputam($_GET['look']);


		$rand1 = "";
//		$ret .= "<hr>".$_SESSION['editTable2']."<hr>";


		$tagabakijs = get_getNx("look");
		$ret .= "<br><div style=\"float:right;\"><a href=\"?$tagabakijs\">AtpakaÄ†ā€˛Ä€Ā¼ uz sarakstu</a></div><br>";

		if (isset($_SESSION['editTable2'])) {
			$rand1 = $_SESSION['editTable2'];
		} else {
			$rand1 = randomString(5);
			$_SESSION['editTable2'] = $rand1;
		}
		$rand1 = "CccXw";

		$Settings = Array();
		$Settings['id'] = 	Array("Visible"=>0);
		//$Settings['id'] = 	Array("Editor"=>"label","DisplayText"=>"Identifikators");
		$Settings['module'] = 	Array("DisplayText"=>"Modulis");
		$Settings['code'] = 		Array("DisplayText"=>"Kods");
		$Settings['longer'] = 		Array("DisplayText"=>"GarÄ�ks teksts");
		$Settings['longerEN'] = 		Array("DisplayText"=>"","DisplayLabel"=>"no");
		$Settings['longerRU'] = 		Array("DisplayText"=>"","DisplayLabel"=>"no");
		$Settings['info'] = 		Array("DisplayText"=>"ÄŖsÄ�ks teksts");
		$Settings['infoEN'] = 		Array("DisplayText"=>"","DisplayLabel"=>"no");
		$Settings['infoRU'] = 		Array("DisplayText"=>"","DisplayLabel"=>"no");
		$Settings['description'] = 		Array("Editor"=>"textarea", "DisplayText"=>"Paskaidrojums");

		$Editor = new TFieldsEdit($rand1);
		$Editor->setDatabase($DatabaseXX);
		$Editor->FindRow($TableXX,"id",$aide);
		$Editor->SetLayoutHTML("
		<table>
		<tr>
			<td colspan=\"3\">::code::</td>
		</tr>
		<tr>
			<td colspan=\"3\">::module::</td>
		</tr>
		<tr>
			<td></td>
			<td><b>EN</b></td>
			<td><b>RU</b></td>
		</tr>
		<tr>
			<td>::info::</td>
			<td>::infoEN::</td>
			<td>::infoRU::</td>
		</tr>
		<tr>
			<td>::longer::</td>
			<td>::longerEN::</td>
			<td>::longerRU::</td>
		</tr>
		<tr>
			<td colspan=\"3\">::description::</td>
		</tr>
		</table>
		");
		$Editor->setAditionalFieldSettings($Settings);
		$ret .= $Editor->Edit();
//		$ret .= drm($_SESSION);

	$modName = db_get("module",ja_ir($DatabaseXX,".").$TableXX," id = $aide ");
	$parejie = selasoc("id, code",ja_ir($DatabaseXX,".").$TableXX," module = '$modName' ");
	$oprArr = Array();
	while (list($k, $v) = @each($parejie))
	{
		$oprArr[$v['id']] = $v['code'];
	}
	$optStr = build_arr_opt_vals($oprArr, $aide);

	$wspuIR = "";
	if (isset($_GET['wspu'])) {
		$wspuIR = "	<input type=\"hidden\" name=\"wspu\" value=\"$_GET[wspu]\">";
	}
	$ret .= "<br><div style=\"float:right;\"><a href=\"?$tagabakijs\">AtpakaĆ„Ā¼ uz sarakstu</a></div><br>";
	$ret .= "
	<form method=\"GET\" action=\"?\" name=\"otherSelectForm\" id=\"otherSelectForm\">
	<div style=\"float:left; clear: right;\">Cits Ć…ļæ½ajĆ„ļæ½ modulĆ„Ā«:
	<input type=\"hidden\" name=\"wpu\" value=\"$_GET[wpu]\">$wspuIR
	<select onChange=\"document.forms.otherSelectForm.submit();\" name=\"look\">$optStr</select>
	</div></form>
	";
	$ret .= $this->addItem();


	} else {


		$TableCols = Array();
		//$TableCols['id'] = "ID";
		$TableCols['code'] = "Kods";
		$TableCols['module'] = "Modulis";
		$TableCols['description'] = "Paskaidrojums";
		$TableCols['info'] = "Info LV";
		$TableCols['infoEN'] = "Info EN";
		$TableCols['infoRU'] = "Info RU";
		$TableCols['link:look:id:SkatÄ«t'] = "DarbÄ«ba";

		$table = new TzTable();
		$table->SetFields($TableCols);

		$sortString = "";
		if (isset($_GET['sort'])) {
			$sortString = $table->get_sortString($_GET['sort']);
		} else {
			$sortString = $table->get_sortString(1);
		}
		$tableWHEREString = "1=1";
		if (strLen($moduleName) > 0) {
			$tableWHEREString = " module = '$moduleName' ";
		}
		$tabdata = selasoc("*",ja_ir($DatabaseXX,".").$TableXX," $tableWHEREString $sortString ");
		if (is_array($tabdata)) {
			while (list($keyu, $vau) = @each($tabdata))
			{
				$table->AddRow($vau);
			}
		}
		$table->StripTags(true,"<b>");
		$ret .= $table->getTable();

		$ret .= $this->addItem();

	} // else if isset look

	$ret .= "</fieldset>";



	return $ret;
}

function addItem($modName = ""){
	$TADSPATS = get_getY();
	if (isset($_GET['look'])) {
		$lookijs = $_GET['look'];
	} else {
		$lookijs = 0;
	}
	$ret = "
		<form method=\"POST\" action=\"?$TADSPATS\">
		<input type=\"hidden\" name=\"new_code_fromOthererderewqert543w\" value=\"$lookijs\">
		<div style=\"float:left; clear: both;\"><hr>
		Kods: <input type=\"text\" name=\"new_code_erdsdderewqert543w\"> <input type=\"submit\" name=\"new_static_text123ew\" value=\"Pievienot\">
		</div>
		</form>";
	return $ret;
}


}

//-------------------------------------------------------------------------------------------------------------
interface iPublic {
	function doAction($actionName = "");
} // interface

//-------------------------------------------------------------------------------------------------------------
interface iPageElements {
	function getCssTags();
	function getJsTags();
	function setPageTitle($Title);
	function getPageTitle();
	function setCssFiles($CssFileArray);
	function getCssFiles();
	function setCssFilesAppend($CssFileArray);
	function setCssScripts($CssScriptsArray);
	function getCssScripts();
	function setCssScriptsAppend($CssScriptArray);
	function setJsFiles($JsFileArray);
	function getJsFiles();
	function setJsFilesAppend($JsFileArray);
	function setJsScripts($ScriptArray);
	function getJsScripts();
	function setJsScriptsAppend($ScriptArray);

	function getJsScriptsOnLoad();
	function setJsScriptsOnLoad($SrciptArray);
	function setJsScriptsOnLoadAppend($SrciptArray);
} // interface

//-------------------------------------------------------------------------------------------------------------
abstract class tBaseInner {

public static function getSField($FCode){
	$ret = "";
	$kods = inputam($FCode);
	$key = selasoc("id, longer, info","pagetexts"," code = '$kods' ");
	if (is_array($key)) {
		$key = $key[0];
		if ((isset($key['id'])) && ($key['id'] > 0)) {
			$ret = $key['longer'];
			if (strLen($ret) == 0) {
				$ret = $key['info'];
			}
		} else {
			$ret = $FCode;
		}
	} else {
		$ret = $FCode;
	}
	//$ret = $FCode;
	return $ret;
}


}

//-------------------------------------------------------------------------------------------------------------
abstract class tBase extends tBaseInner {

	var	$stepnames = Array("dummy");
	private $RegistrationCode = "";
	var $SessionVarName = "";
	var $ActionVarName = "";
	var $headerArrays = Array();
	var	$headerNoGetArray = Array();
	var $PageTitle = "";

abstract function clearSessionVars();
abstract function getModuleName();
abstract function call__SetVariableNames();
//abstract function getPageTitle();

function SetVariableNames($SessionVariable, $ActionVariable) {
	$this->SessionVarName = $SessionVariable;
	$this->ActionVarName = $ActionVariable;
}

function getSessionVarName() {
	return $this->SessionVarName;
}

function getActionVarName(){
	return $this->ActionVarName;
}

function update_steps_registration($upTo = 0){
	if (isset($_SESSION[$this->getModuleName().'_STEPS_TAKEN'])) {
		if ($_SESSION[$this->getModuleName().'_STEPS_TAKEN'] < $upTo ) {
			$_SESSION[$this->getModuleName().'_STEPS_TAKEN'] = $upTo;
		}
	} else {
		$_SESSION[$this->getModuleName().'_STEPS_TAKEN'] = $upTo;
	}
}

function setRegCode($regCode){
	$this->RegistrationCode = $regCode;
}

function setStepNames($NamesArray) {
	$this->stepnames = $NamesArray;
}

function printHeader(){
	$ret = "";
	$NotGets = $this->headerNoGetArray;
	$Nottable = TzTable::getUsedGets();
	$NotAll = array_merge($NotGets,$Nottable);
	//$TAGAD = get_getY("wpu","wspu");
	$TAGAD = get_getY("q12w","act");
//	$TAGAD = get_getA($NotAll);

	$LinkNormal = "topLink";
	$LinkActive = "topLinkAct";

	$css_pu_act = "topLink";
	$css_pu_arch = "topLink";
	$css_spu_prev = "topLink";
	$css_spu_today = "topLink";

	while (list($keyu, $vau) = @each($this->headerArrays))
	{
		$$keyu = $vau;
	}
	$ret .= "<div class=\"cmsPart\">";
	$bija1 = "";
	$activeKey = "";
	$npk1 = 0;
	while (list($keyu, $vau) = @each($bigLinks))
	{
		$npk1++;
		$CssPU = $LinkNormal;
		if (((isset($_GET['wpu'])) && ($_GET['wpu'] == $keyu)) or (isset($_GET['wpu']) == false) && ($npk1 == 1)) {
			$CssPU = $LinkActive;
			$activeKey = $keyu;
		}
		$ret .= "$bija1<a class=\"$CssPU\" href=\"?$TAGAD&amp;wpu=$keyu\">$vau</a>";
		$bija1 = " | ";
	}
	$ret .= "</div>";

	$actArrayName = "undermenu_".$activeKey;
	$bija1 = "";
	$activeKey2 = "";
	if ((isset($$actArrayName)) && (is_array($$actArrayName))) {
		$ret .= "<div class=\"cmsPart2\">";
		$kopskaits2 = count($$actArrayName);
		$npk2 = 0;
		while (list($keyu, $vau) = @each($$actArrayName))
		{
			$npk2++;
			$CssPU = $LinkNormal;
			if (((isset($_GET['wspu'])) && ($_GET['wspu'] == $keyu)) or ($kopskaits2 == 1) or (($npk2 == 1) && (isset($_GET['wspu']) == false))) {
				$CssPU = $LinkActive;
				$activeKey2 = $keyu;
			}
			$ret .= "$bija1<a class=\"$CssPU\" href=\"?$TAGAD&amp;wpu=$activeKey&amp;wspu=$keyu\">$vau</a>";
			$bija1 = " | ";
		}

		$ret .= "</div>";
	}
	$ret = Array("data"=>$ret,"part"=>$activeKey2);
	return $ret;
}

function setHeaderNoGETs($arrays){
	$this->headerNoGetArray = $arrays;
}

function setHeaderArrays($arrays){
	$this->headerArrays = $arrays;
}

function tBaseParseString($string){
	return $string;
}

}

//-------------------------------------------------------------------------------------------------------------
class tPage_Base extends tBase implements iPageElements {
// tikai ja ir Inner

var $CssFiles = Array();
var $CssScripts = Array();
var $JsFiles = Array();
var $JsScripts = Array();
var $JsScriptsOnLoad = Array();

function getAllSettings($FromObject){
//	echo "Getting settings!!! <br>";
	$this->setCssFilesAppend($FromObject->getCssFiles());
	$this->setCssScriptsAppend($FromObject->getCssScripts());

	$this->setJsFilesAppend($FromObject->getJsFiles());
	$this->setJsScriptsAppend($FromObject->getJsScripts());
	$this->setJsScriptsOnLoadAppend($FromObject->getJsScriptsOnLoad());
}

function clearSessionVars(){

}
function getModuleName(){

}
function call__SetVariableNames(){

}

function getJsScriptsOnLoad() {
	return $this->JsScriptsOnLoad;
}
function setJsScriptsOnLoadAppend($SrciptArray) {
	$this->JsScriptsOnLoad = array_combineA($this->JsScriptsOnLoad, $SrciptArray) ;
}
function setJsScriptsOnLoad($SrciptArray) {
	$this->JsScriptsOnLoad = $SrciptArray;
}

function getCssScripts() {
	return $this->CssScripts;
}
function getCssFiles() {
	return $this->CssFiles;
}

function getJsScripts(){
	return $this->JsScripts;
}
function getJsFiles(){
	return $this->JsFiles;
}

function getCssTags(){
	$ret = "";
	if (is_array($this->CssFiles)) {
		$tmpArr = Array();
	 	while (list($keyu, $vau) = @each($this->CssFiles)) {
			$tmpArr[$vau] = $vau;
		}
		reset($tmpArr);
	 	while (list($keyu, $vau) = @each($tmpArr))
		{
			$ret .= "<link type=\"text/css\" rel=\"stylesheet\"  href=\"$vau\">\n";
		}
	}
	if (is_array($this->CssScripts)) {
	 	while (list($keyu, $vau) = @each($this->CssScripts))
		{
			$ret .= '<style type="text/css">
/* START STYLE:'.$keyu.' */
'.$vau.'
/* END STYLE:'.$keyu.' */
</style>'."\n";
		}
	}
	return $ret;
}

function getJsTags(){
	$ret = "";
	//<script src="calendar.js"></script>
	if (is_array($this->JsFiles)) {
		$tmpArr = Array();
	 	while (list($keyu, $vau) = @each($this->JsFiles)) {
			$tmpArr[$vau] = $vau;
		}
		reset($tmpArr);
	 	while (list($keyu, $vau) = @each($tmpArr))
		{
			$ret .= '<script type="text/javascript" src="'.$vau.'"></script>'."\n";
		}
	}
	if (is_array($this->JsScripts)) {
		$ret .= '<script type="text/javascript">'."\n";
	 	while (list($keyu, $vau) = @each($this->JsScripts))
		{
if (is_array($vau)) {
	$viii = drm($vau);
} else {
	$viii = $vau;
}

$ret .= "\n".'/* START SCRIPT:'.$keyu.' */
'.$viii.'
/* END SCRIPT:'.$keyu.' */'."\n"."\n";
		}
		$ret .= '</script>'."\n";
	}

	$ret .= '
	<script type="text/javascript">
/* start browser checks2 */
	var ua		= navigator.userAgent;
	var opera	= false;
	var safari	= false;
	var konq	= false;
	var ie		= false;
	var moz		= false;
	var mozilla = false;

	if (opera = ua.match(/opera.([0-9\.]+)/i))
		opera	= opera[1];
	else if (safari = ua.match(/safari.([0-9\.]+)/i))
		safari	= safari[1];
	else if (konq = ua.match(/konqueror.([0-9\.]+)/i))
		konq	= konq[1];
	else if (ie = ua.match(/msie.([0-9\.]+)/i))
		ie		= ie[1];
	else if (moz = ua.match(/mozilla.([0-9\.]+)/i))
		mozilla = moz = moz[1];
	else
		var other = ua;
/* end browser checks */
</script>'."
\n";
	return $ret;
}

function setPageTitle($Title){
	$this->PageTitle = $Title;
}

function getPageTitle(){
	$ret = "<title>".$this->PageTitle."</title>\n";
	return $ret;
}

function setCssFiles($CssFileArray){
	$this->CssFiles = $CssFileArray;
}

function setCssFilesAppend($CssFileArray){
	$this->CssFiles = array_combineA($this->CssFiles, $CssFileArray) ;
}

function setCssScripts($CssScriptsArray){
	$this->CssScripts = $CssScriptsArray;
}

function setCssScriptsAppend($CssScriptArray){
	$this->CssScripts = array_combineA($this->CssScripts, $CssScriptArray) ;
}

function setJsFiles($JsFileArray){
	$this->JsFiles = $JsFileArray;
}

function setJsFilesAppend($JsFileArray){
	$this->JsFiles = array_combineA($this->JsFiles, $JsFileArray) ;
}

function setJsScripts($ScriptArray){
	$this->JsScripts = $ScriptArray;
}

function setJsScriptsAppend($ScriptArray){
	$this->JsScripts = array_combineA($this->JsScripts,$ScriptArray);
}

} // class

//-------------------------------------------------------------------------------------------------------------
class phpBuildTime
{
 var $start_time = 0;
 function __construct() {
 $this->start_time = $this->GET_TIME(); }
 function GET_TIME()
 {  list($usec, $sec) = explode(" ",microtime());
 	$ret = ((float)$usec + (float)$sec);
	//echo "Naa: $ret <br>";
    return $ret; }
 function DIFF_TIME()
 {
 	    $endtime = $this->GET_TIME();
 		$diff = $endtime - $this->start_time;
        return number_format($diff,3,"."," "); }
} // class
//-------------------------------------------------------------------------------------------------------------
class sql_action
{
//=============================
  var $datubaze;
  var $tabula;
  var $HTTP_SESSION_VARS;
  var $izmainas_rindaam = -1;
  var $izmainitais_id = -1;
  var $PRINT_SQL_CODE = "";
  var $print_this = false;
  var $GlobalFieldTypes = Array();
//========================================================================================
   function get_ch_row_count()
   {       return $this->izmainas_rindaam;}
//========================================================================================
   function get_ch_row_id()
   {       return $this->izmainitais_id;}
//========================================================================================
	function loadFieldTypes(){
	 	$data = sqlsel("describe $this->datubaze.$this->tabula");
	 	$ret = Array();
	 	while (list($keyu, $vau) = @each($data))
		{
			$ret[$vau['Field']] = $vau;
		}
		$this->GlobalFieldTypes = $ret;
	}
//========================================================================================
	function sql($echo = true)
	{
	/*
	* Returns SQL code.
	* USAGE:
	* 	If want ir to return the SQL code:
	* 		$object->sql(0); 					// 0 may be anything, except "true"...
	* 	If you want to echo the code to screen:
	* 		$object->sql();
	*/
	$ret = true;
	if ($echo === true)
	 {
	 	echo $this->PRINT_SQL_CODE;
	 } else
	 {
	 	$ret = $this->PRINT_SQL_CODE;
	 }
	return $ret;
	}
//========================================================================================
   function sql_action($datubaze = NULL, $tabula = NULL, $HTTP_SESSION_VARS2 = NULL)
   {
   /*
   * Constructor of class.
   * USAGE:
   * 	If want to create simple class object:
   * 		$object = new sql_action(null, "table name");
   * 	If there is specific DATABASE to connect to:
   * 		$object = new sql_action("database name","table name"); // $HTTP_SESSION_VARS are set correctly...
   * 		$object = new sql_action("database name","table name",$ARRAY_OF_CORRECT_PORPERTIES);
   * Structure of Array to pass (Minimum for this class):
   * 	$props['host'] = "localhost";
   * 	$props['user'] = "db user name";
   * 	$props['passw'] = "db user password";
   * 	$props['db'] = "database name";
   * 	$PASABLE_ARRAY['props'] = $props;
   */
     $this->tabula = $tabula;
	 if (isset($HTTP_SESSION_VARS2))
	 {

	 } else
	 {
	 	$HTTP_SESSION_VARS2 = $_SESSION;
	 }
	 $this->HTTP_SESSION_VARS = $HTTP_SESSION_VARS2;
      //doconect($this->HTTP_SESSION_VARS);
   	 if ($datubaze == null)
	 {
	 	$datubaze = $HTTP_SESSION_VARS2['props']['db'];
	 }
     $this->datubaze = $datubaze;


	 // Veco lapu versijaam (LIMBAI) vajadziiga vecaa piesleegshanaas
	  if (isset($HTTP_SESSION_VARS2['props']))
	  {
	  // doco($HTTP_SESSION_VARS2['props']);
	  } else
	  {
		//  conecttodb("ieks", "parole", "intra");
	  }
	  if (strLen($this->datubaze) > 0) {
	//		mysql_select_db($this->datubaze);
	  }
      $this->loadFieldTypes();
   }
//========================================================================================
	function print_sql()
	{
		$this->print_this = true;
	}
//========================================================================================
	function update_table_structure($new_struct)
	{
		$ret = false;

		return $ret;
	}
//========================================================================================
	function table_is_up_to_date($curr_table_struct)
	{
		$ret = true;

		return $ret;
	}
//========================================================================================
	function good_col_name($cool)
	{
	/*
	* Returns TRUE if Column is not SERVICE column, like IP, DATE etc...
	*/
	$ret = true;
	if (($cool == "id") or
		($cool == "ip") or
		($cool == "lielaiskods") or
		($cool == "row_editable") or
		($cool == "datums"))
		{
			$ret = false;
		}
	return $ret;
	}
//========================================================================================
	function get_cols()
	{
	/*
	* Returns Array of column names for table in database. Table = class table
	* This function excepts all service col names, like ID, DETE etc..
	*/
		$fields = mysql_list_fields($this->datubaze, $this->tabula);
    	$columns = mysql_num_fields($fields);
		$ret = null;
		for ($a = 0; $a < $columns; $a++)
		{
			$nos = mysql_field_name($fields, $a);
			if ($this->good_col_name($nos))
			{
				$ret[] = $nos;
			}
		}
		return $ret;
	}
//========================================================================================
	function get_all_cols()
	{
	/*
	* Returns Array of column names for table in database. Table = class table
	*/
		$fields = mysql_list_fields($this->datubaze, $this->tabula);
    	$columns = mysql_num_fields($fields);
		$ret = null;
		for ($a = 0; $a < $columns; $a++)
		{
			$ret[] = mysql_field_name($fields, $a);
		}
		return $ret;
	}
//========================================================================================
	function selasoc($fields, $where= null)
	{
		$ret = false;


		return $ret;
	}
//========================================================================================
	function table_exists($tabula)
	{
	/*
	* Returns true if teble exists in database
	*/
		$ret = false;
		$tabulas = selasoc("SHOW TABLES FROM `$this->datubaze`;");
		while (list($key, $value) = each($tabulas))
               {
			   		if ($value["Tables_in_$this->datubaze"] == $tabula)
					{
						$ret = true;
					}
			   }
		return $ret;
	}
//========================================================================================
	function make_table($dati)
	{
	/*
	* Makes table in database
	* New table ARRAY structure:
	* 	$data['col_name'] = "data type";
	*/
		$ret = false;
		if ($this->table_exists($this->tabula))
		{
			if ($this->delete_table())
			{
			} else
			{
				die('While making NEW table and deleting old table - ocured problem...<BR>');
			}
		}

		$lauki = "";
		while (list($key, $va) = each($dati))
        {
			$tt = "";
			switch ($va)
			{
				case "int": 	$tt = " int(11) NULL "; break;
				case "varchar": $tt = " varchar(255) NULL "; break;
				case "text": 	$tt = " text NULL "; break;
				case "date": 	$tt = " datetime NULL "; break;
				case "double": 	$tt = " double NULL "; break;
			}
			if (strlen($tt) > 0)
			{
				$lauki .= "`$key` $tt,
				";
			}
		}

		$demo = "CREATE TABLE `$this->datubaze`.`$this->tabula` (
		  `id` bigint(20) unsigned NOT NULL auto_increment,
		  $lauki
		  PRIMARY KEY (`id`)
		)   TYPE=MyISAM;";
		//echo "$demo";
		if ($this->run_query($demo) == 0)
		{
			$ret = true;
		}
		return $ret;
	}
//========================================================================================
   function delete_row($id)
   {
   /*
   * Deletes row from table by given row ID
   */
   $ret = false;
   if ($id > 0)
     {
       if ($this->run_query("DELETE FROM $this->datubaze.$this->tabula WHERE id=$id", "DELETE") == 0)
         {
            $ret = true;
         }
     }
   return $ret;
   }
//========================================================================================
   function delete_where($where)
   {
	$ret = false;
	if ($this->run_query("DELETE FROM $this->datubaze.$this->tabula WHERE $where", "DELETE") == 0)
	{
		$ret = true;
	}
	return $ret;
   }
//========================================================================================
   function delete_rows($ids)
   {
   /*
   *
   */
   $ret = false;
   while (list($key, $value) = @each($ids))
       {
         if ($this->delete_row($value))
            {
              $ret = true;
            }
       }
   return $ret;
   }
//========================================================================================
   function delete_table()
   {
   $ret = false;
       if ($this->run_query("DROP TABLE $this->datubaze.`$this->tabula`") == 0)
         {
            $ret = true;
         }
   return $ret;
   }
//========================================================================================
   function delete_rows_where($sql)
   {
   $ret = false;
       if ($this->run_query("DELETE FROM $this->datubaze.$this->tabula WHERE $sql", "DELETE") == 0)
         {
            $ret = true;
         }
   return $ret;
   }
//========================================================================================
	function value_DateFunkc($vauex, $fieldName){
		$ret = false;
		if ($this->GlobalFieldTypes[$fieldName]['Type'] == "datetime") {
			if ($vauex == "NOW()") {
				$ret = true;
			}
			if ( substr($vauex,0,9) == 'DATE_ADD(' ) {
				$ret = true;
			}
		}
		return $ret;
	}

   function update_row($masivs, $wheris) // argumenti masivs, "kolona= 3"
   {
   $ret = false;
     if (count($masivs) > 0)
     {
        $wher = " WHERE " . $wheris;
        if (!(strlen($wheris) > 3))
          {
             $wher = " WHERE 1 = 1 ";
          }
        $sqls = "UPDATE $this->datubaze.$this->tabula SET ";
        $seti = "";
        $biju = "";
        //dm($this->GlobalFieldTypes);
        while (list($key, $value) = each($masivs))
               {
			    //$type  = mysql_field_type($fields, $key);
				 if ($this->value_DateFunkc($value, $key)) {  $pedinjas = ""; } else
				 {
				  $pedinjas = "'";
				 }
				 if ($this->GlobalFieldTypes[$key]['Type'] == "datetime") {
				 	if (strLen(trim($value)) == 0) {
				 		$value = $this->GlobalFieldTypes[$key]['Default'];
				 	}
				 	$value = "$value";
				 }
                 $seti .= " $biju $key = ".$pedinjas.$value.$pedinjas;
                 $biju = ",";
               }
        $sqls = $sqls . $seti . $wher;
        if ($this->run_query($sqls, "UPDATE") == 0)
          {
             $ret = true;
          }
     }
   return $ret;
   }
//========================================================================================
   function clear_table()
   {
   $ret = false;
       if ($this->run_query("DELETE FROM $this->datubaze.$this->tabula", "CLEAR") == 0)
         {
            $ret = true;
         }
   return $ret;
   }
//========================================================================================
   function def_vals()
   {
     $sql = "SHOW FIELDS FROM `$this->tabula`";
     $retis;
     $results = mysql_query($sql)
           or die("Nepareizs pieprasijums  $sql");
      for ($il = 0; $il < mysql_num_rows($results); $il++)
         {
            if(!($rowa = mysql_fetch_object($results)))
             continue;
              $retis[$rowa->Field] = $rowa->Default;
         }
      return $retis;
   }
//========================================================================================
   function insert_row($masivs)
   {
       $fields = @mysql_list_fields($this->datubaze, $this->tabula);
       $columns = @mysql_num_fields($fields);
       $def_vals = $this->def_vals();
       $kol_stripa = "";
       for ($i = 0; $i < $columns; $i++)
         {
            $kolonas[] = mysql_field_name($fields, $i);
            $kol_stripa .= " ".mysql_field_name($fields, $i);
            if (($i + 1) < $columns)
               {
                  $kol_stripa .= ", ";
               }
         }
		//if ($this->print_this == true)
	{
		//$sk1 = count($masivs);
		//$sk2 = count($kolonas);
		//$kolas = drm($kolonas);
		//alert("te1 = sk1: $sk1 = 4  sk2: $sk2 = 3 ");
		//echo "[[[$kolas]]]";
	}
       if (count($masivs) <= count($kolonas))
         {

            $sqly = " INSERT INTO $this->tabula ( $kol_stripa ) VALUES ( ";
            $vertibas = "";
            $biju = "";
            while (list($key, $value) = each($kolonas))
               {
                 $type  = @mysql_field_type($fields, $key);
                 $pedinjas = "";

                 if (($type == "string") or ($type == "blob") or ($type == "text"))
                    {
                      $pedinjas = "'";
                    }

				if (isset($masivs[$value])) {
                	if ((($type == "date") or ($type == "datetime")) and (strtolower($masivs[$value]) != "now()")) {
                 		$pedinjas = "'";
                	}
                } else {
                	if ((($type == "date") or ($type == "datetime")) /* and (strtolower($masivs[$value]) != "now()") */ ) {
                 		$pedinjas = "'";
                	}
				}

                 $flags = @mysql_field_flags($fields, $key);
                 if (isset($masivs[$value]))
                    {
                      $vertibas .= $biju . $pedinjas . $masivs[$value] . $pedinjas . "  ";
                    }
                 else
                    {
                      if ((isset($def_vals[$value])) && ((strlen($def_vals[$value]) > 0)))
                         {
                            $vertibas .= $biju . $pedinjas . $def_vals[$value] . $pedinjas;
                         }
                      else
                         {
                            $vertibas .= $biju . " NULL ";
                         }
                    }
                 $biju = ",";
               }
            $sqly = $sqly . $vertibas . ")";
            //echo $sqly."<br>";
            if ($this->run_query($sqly, "INSERT") == 0)
            {
               $ret = true;
            }
            else
            {
               $ret = false;
            }
         }
     return $ret;
   }
//========================================================================================
function run_query($sql, $type)
   {
     $kljuda = 0;
	 $this->PRINT_SQL_CODE = $sql;
	 if ($this->print_this == true)
	 {
	 	echo "<BR>::$sql::<BR>";
	 }
	 $this->print_this = false;
     mysql_query($sql);
     if ($kljuda != 1)
       {
         if ($type == "INSERT")
           {

           }
       }
       mysql_query("commit;");
       try {
      $this->izmainitais_id = mysql_insert_id();
			} catch (Exception $e) {
					//echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
       try {
			$this->izmainas_rindaam =  mysql_affected_rows();
			} catch (Exception $e) {
					//echo 'Caught exception: ',  $e->getMessage(), "\n";
			}

     return $kljuda;
   }
//========================================================================================
} // class
/*
* Klase domata sarakstu sakartosanai pa lapaam.
*
* Uzgenerejot klasi nekas netiek drukaats, tikai noraadiits pilns sql,
*
*/
class lists
{
//---------------------------------------
// Klases mainigie:
	var $sql  = NULL;
	var $lapaa = 10; 		// iegustam no objekta izveidosanas parametriem (konstruktoraa)
	var $lapas = 0;  		// lapas = ceil($ieraksti / $lapaa);
	var $ieraksti = 0; 		// iegustam
	var $act_lapa = 1;
	var $act_record = 0;	// tiek rekinats aprekinot to kura lapa tagad atrodamies un to
					 	// cik vienaa lapaa ir ierakstu
//---------------------------------------
/*
* Argumenti:
* 	$HTTP_SESSION_VARS
* 		Nepieciesams mainigais ar doco fuunkcijas parametriem - mainiigaa PROPS
* 	$sql
* 		pilns SQL pieprasijums.
*/
function lists( $HTTP_SESS_VARS = NULL,
				$HTTP_G_VARS = NULL,
				$name = "arr", // name used in references, should be unique in project
				$sql = NULL, // full SQL statement
				$lapaa = NULL, // active page - used only if in _GET array it is not defined
				$curr_page = NULL // current page - used as primary, not from _GET array
				)
{
	if (doco($HTTP_SESS_VARS['props']) == true)
	{
		$this->sql = $sql;
		$this->ieraksti = 0;
		$this->lapaa = $lapaa;
		$maa = selasoc($this->sql);
		$this->ieraksti = count($maa);
		$this->lapas = ceil($this->ieraksti / $this->lapaa);
		if ($curr_page >= 1) {
			$this->act_record = ($curr_page - 1) * $lapaa;
		} else {
			$this->act_record = 0;
			$curr_page = 1;
			//echo "Error: Ievadita nepareiza lapa... (iespejams negativa) CLASS->lists <br> ";
		}
		$this->act_lapa = $curr_page;
	} else
	{
		echo "Error: Nav iespejams pieslegties pie DB, CLASS->lists <BR>";
	}
}
//---------------------------------------
function pages()
{

}
//---------------------------------------
function next_page()
{
return "<div align=right>Skat?ties sarakst? pa | <a class=zals_a href=\"#\">20</a>
							  | <a class=zals_a href=\"#\">50</a>
							  | <a class=zals_a href=\"#\">100</a>
							  | <a class=zals_a href=\"#\">skat?t visu</a></div>";
}
//---------------------------------------
function limit()
{
 return " limit ".$this->act_record.", ".$this->lapaa;
}
//---------------------------------------
}
//========================================================================================
?>