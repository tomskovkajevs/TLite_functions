<?php

class TFormElements extends tPage_Base {

var $TheSameValueTo = Array();
var $enableIfSet = Array();
var $arrayData;
var $DefaultDate = "";
var $ActiveDate = "";
var $SelectedValue;

function setArrayData($NewData){
	$this->arrayData = $NewData;
}

function getSelectForm($FormID,$defs = 0){
	$opcijasArray = build_arr_opt_nokey_wselected($this->arrayData,$defs);
	$opcijas = $opcijasArray['options'];
	$this->SelectedValue = $opcijasArray['selected'];
	$ret = "<select class=\"inputCountSelect2\" name=\"$FormID\" id=\"$FormID\">$opcijas</select>";
	return $ret;
}

function defaultDate($DefDate){
	$this->DefaultDate = $DefDate;
}

function copyValAfter($Value){
	$this->TheSameValueTo = array_combineA($this->TheSameValueTo, Array($Value=>$Value));
}
function enableValAfter($Value){
	$this->enableIfSet = array_combineA($this->enableIfSet, Array($Value=>$Value));
}

function getDateForm($PlaceID){
	$ret = "<table cellpadding=\"0\" cellspacing=\"0\"><tr>";

	$Scr['vardateSplitString'] = "var dateSplitString = \"/\";";
	$otherObjectVals = "";
	$usedSplitdatesFunkc = "splitdates$PlaceID";
	$OnLoadScripts = "";
	$OnBlurscripts = "";

	if ((is_array($this->enableIfSet)) && (count($this->enableIfSet) > 0)) {
			$Scr['function_enabler'] = "function enableDateFormElements(elementName,isdisabled) {
//alert(elementName + ':' + isdisabled);
getObj('dnd' + elementName).disabled = isdisabled;
getObj('dnm' + elementName).disabled = isdisabled;
getObj('dny' + elementName).disabled = isdisabled;
if (isdisabled == true) {
	getObj('dndiv'+elementName).style.display = 'none';
 } else {
	getObj('dndiv'+elementName).style.display = '';
 }

}
";
			$Scr['function_ckeckDateValue'] = "function ckeckDateValue(valuez, big) {
ret = false;
if ((valuez >= 1) && (valuez <= 31) && (big == false)) {
	ret = true;
} else {
	if ((big == true) && (valuez > 2006) && (valuez < 2020)) {
		ret = true;
	}
}
return ret;
}
";
$OnLoadScriptsBija = "";
		while (list($keyu, $vau) = @each($this->enableIfSet))
		{
			$Scr['function_enable'.$vau] = "function checkEnableDisableDates$vau() {
ret = true;
if ( ckeckDateValue(getObj('dnd$PlaceID').value,false) == false) { ret = false; }
if ( ckeckDateValue(getObj('dnm$PlaceID').value,false) == false) { ret = false; }
if ( ckeckDateValue(getObj('dny$PlaceID').value,true) == false) { ret = false; }
if (ret == false) {
	enableDateFormElements('$vau',true);
} else {
	enableDateFormElements('$vau',false);
}
}";
	$this->setJsScriptsOnLoadAppend(Array("checkEnableDisableDates$vau"=>"checkEnableDisableDates$vau();"));
	$OnLoadScripts .= $OnLoadScriptsBija."'checkEnableDisableDates$vau'";
	$OnLoadScriptsBija = ", ";
		}
$Scr['function_my_OwnONBlurScripts'] = "function myOwnOnBlur$PlaceID() {
	var srww = new Array($OnLoadScripts);
	for (a=0; a< srww.length; a++) {
		eval(srww[a] + '();');
	}
//	splitdatesReverse(getObj('kopaa$PlaceID'),getObj('dnd$PlaceID'),getObj('dnm$PlaceID'),getObj('dny$PlaceID'));
//	$usedSplitdatesFunkc(getObj('kopaa$PlaceID'),getObj('dnd$PlaceID'),getObj('dnm$PlaceID'),getObj('dny$PlaceID'));
}
";
$OnBlurscripts = "onChange=\"javascript: myOwnOnBlur$PlaceID(); \" ";
	}

	if ((is_array($this->TheSameValueTo)) && (count($this->TheSameValueTo) > 0)) {
		while (list($keyu, $vau) = @each($this->TheSameValueTo))
		{
			$otherObjectVals .= "getObj('dnd'+'$vau').value = arr[0]; \n";
			$otherObjectVals .= "getObj('dnm'+'$vau').value = arr[1]; \n";
			$otherObjectVals .= "getObj('dny'+'$vau').value = arr[2]; \n";
		}
	}
	$Scr['function_'.$usedSplitdatesFunkc] = "function $usedSplitdatesFunkc(allThing,dayTh, monthTh, yearTh) {
	arr = allThing.value.split(dateSplitString);
	try {
		dayTh.value		= arr[0];
		monthTh.value	= arr[1];
		yearTh.value	= arr[2];
		$otherObjectVals
	} catch (e) { } }";

	$Scr['function_splitdatesReverse'] = "function splitdatesReverse(allThing,dayTh, monthTh, yearTh) {
	try {
		allThing.value = dayTh.value + dateSplitString + monthTh.value + dateSplitString + yearTh.value;
	} catch (e) { } }";
	$this->setJsScriptsAppend($Scr);
	$this->setCssFilesAppend(Array("main.css"));
	$this->setJsFilesAppend(Array("tilts_calendar.js"));
	//var mycars=new Array("Saab","Volvo","BMW");
	$usedAfterFunctions = "'$usedSplitdatesFunkc'";
	if (StrLen($OnLoadScripts) > 0) {
		$usedAfterFunctions .= ", $OnLoadScripts";
	}
	$lcsFunkc = "lcs(getObj('kopaa$PlaceID'), this, getObj('dnd$PlaceID'),getObj('dnm$PlaceID'), getObj('dny$PlaceID'), Array($usedAfterFunctions));";
	$DefaultDD = "-";
	$DefaultMM = "-";
	$DefaultYYYY = "-";

	if (StrLen($this->DefaultDate) > 0) {
		$DdateExploded = explode("-",$this->DefaultDate);
		$DefaultDD = $DdateExploded[2];
		$DefaultMM = $DdateExploded[1];
		$DefaultYYYY = $DdateExploded[0];
	}
	if (isset($_GET['dnd'.$PlaceID])) { $ValueDnd = $_GET['dnd'.$PlaceID]; } else { $ValueDnd = $DefaultDD; }
	if (isset($_GET['dnm'.$PlaceID])) { $ValueDnm = $_GET['dnm'.$PlaceID]; } else { $ValueDnm = $DefaultMM; }
	if (isset($_GET['dny'.$PlaceID])) { $ValueDny = $_GET['dny'.$PlaceID]; } else { $ValueDny = $DefaultYYYY; }
	//if (isset($_GET['kopaa'.$PlaceID])) { $Valuekopaa = $_GET['kopaa'.$PlaceID]; } else { $Valuekopaa = "$DefaultDD/$DefaultMM/$DefaultYYYY"; }

	$monthDays = Array("-","01","02","03","04","05","06","07","08","09","10",
		"11","12","13","14","15","16","17","18","19","20","21","22","23","24",
		"25","26","27","28","29","30","31");
	$months = Array("-","01","02","03","04","05","06","07","08","09","10","11","12");
	$ValueDnyOptsArr = Array("-","2008","2009");

	$ValueDndOpts = build_arr_opt_nokey($monthDays,$ValueDnd);
	$ValueDnmOpts = build_arr_opt_nokey($months,$ValueDnm);
	$ValueDnyOpts = build_arr_opt_nokey($ValueDnyOptsArr,$ValueDny);

	$ret .= "<td><select class=\"inputDateSmall\" name=\"dnd$PlaceID\" id=\"dnd$PlaceID\" $OnBlurscripts>$ValueDndOpts</select>";
	$ret .= "<td><select class=\"inputDateSmall\" name=\"dnm$PlaceID\" id=\"dnm$PlaceID\" $OnBlurscripts>$ValueDnmOpts</select>";
	$ret .= "<td><select class=\"inputDateBig\" name=\"dny$PlaceID\" id=\"dny$PlaceID\" $OnBlurscripts>$ValueDnyOpts</select>";
//	$ret .= "<input type=\"text\" class=\"inputDateBig\" name=\"kopaa$PlaceID\" id=\"kopaa$PlaceID\" value=\"$Valuekopaa\">";

/*	$ret .= "<td><input type=\"text\" $OnBlurscripts size=\"2\" class=\"inputDateSmall\" name=\"dnd$PlaceID\" id=\"dnd$PlaceID\" value=\"$ValueDnd\"></td>";
	$ret .= "<td><input type=\"text\" $OnBlurscripts size=\"2\" class=\"inputDateSmall\" name=\"dnm$PlaceID\" id=\"dnm$PlaceID\" value=\"$ValueDnm\"></td>";
	$ret .= "<td><input type=\"text\" $OnBlurscripts size=\"4\" class=\"inputDateBig\" name=\"dny$PlaceID\" id=\"dny$PlaceID\" value=\"$ValueDny\"></td>";
	$ret .= "<input type=\"hidden\" name=\"kopaa$PlaceID\" id=\"kopaa$PlaceID\" value=\"$Valuekopaa\">";
*/	//calendar.gif
	$this->ActiveDate = $Valuekopaa;

//	$ret .= "<input type=\"button\" class=\"inputDateButton\" onfocus=\"$lcsFunkc \" onclick=\"event.cancelBubble=true;splitdatesReverse(kopaa$PlaceID,dnd$PlaceID,dnm$PlaceID,dny$PlaceID); $lcsFunkc \" name=\"dnb$PlaceID\" value=\"...\">";
//	$ret .= "<input type=\"image\" class=\"inputDateButtonImg\" src=\"calendar.gif\" onfocus=\"$lcsFunkc \" onclick=\"event.cancelBubble=true;splitdatesReverse(kopaa$PlaceID,dnd$PlaceID,dnm$PlaceID,dny$PlaceID); $lcsFunkc \" name=\"dnb$PlaceID\" value=\"Select Date\">";
	$PREPATH_PIC = "http://travel-baltic.lv/tilts/HotelReservations/calendar.gif";
	$ret .= "<td><div id=\"dndiv$PlaceID\"><img class=\"inputDateButtonImg\" src=\"$PREPATH_PIC\" onclick=\"event.cancelBubble=true;splitdatesReverse(kopaa$PlaceID,dnd$PlaceID,dnm$PlaceID,dny$PlaceID); $lcsFunkc \" id=\"dnb$PlaceID\" alt=\"Select Date\"></div></td></tr></table>";
	return $ret;
	// this.select();
}

function getActiveDate(){
	$ret = DateReverse($this->ActiveDate,"/","-");
	return $ret;
}

function getSelectedValue(){
	return $this->SelectedValue;
}

} // class


?>