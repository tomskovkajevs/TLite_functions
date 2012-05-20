<?php

class TTLiteWebPage extends tPage_Base {

var $PageTitle = "";
var $PageBody = "";
var $CssFiles = Array();
var $CssScripts = Array();
var $JsFiles = Array();
var $JsScripts = Array();
var $BodyStyles = "";

function TTLiteWebPage(){

}

function getMetaTags(){
	$ret = "";
	$ret .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
	return $ret;
}



function getHeader(){
	$ret = "";
	$ret .= "<head>";
	$ret .= $this->getPageTitle();
	$ret .= $this->getMetaTags();
	$ret .= $this->getCssTags();
	$ret .= $this->getJsTags();
	$ret .= "</head>\n";
	return $ret;
}


function setBodyStyles($Styles){
	$this->BodyStyles = $Styles;
}
function setBody($BodyTxt){
	$this->PageBody = $BodyTxt;
}
function setBodyAppend($BodyTxt){
	$this->PageBody .= $BodyTxt;
}
function getBody(){
	$ret = "";
	$BStyles = $this->BodyStyles;
	if (strlen($BStyles) > 0) {
		$BStyles = "$BStyles ";
	}
	if ((is_array($this->JsScriptsOnLoad)) && (count($this->JsScriptsOnLoad) > 0)) {
		$BStyles .= " onLoad=\"javascript: TLiteOnLoadPage(); \"";
		$OnLoadScripts = "function TLiteOnLoadPage() { \n";
		while (list($keyu, $vau) = @each($this->JsScriptsOnLoad))
		{
			$OnLoadScripts .= " /*script: $keyu */ \n$vau \n ";
		}
		$OnLoadScripts .= "}\n";
		$this->setJsScriptsAppend(Array("onLoadSrciptsTLite"=>$OnLoadScripts));
	}
	$ret .= "<body $BStyles>".$this->PageBody."
</body>";
	return $ret;
}

function CheckOverlayAlerts() {
	$ret = "";
	if (isset($_SESSION['SHOW_MODAL'])) {
	
	if (is_array($_SESSION['SHOW_MODAL'])) {
		
	} else {
		$ObjectIde = "objx1".randomString(5,5)."e";
		$oboo = $this;
		$ret .= alertMod($oboo, $ObjectIde, $_SESSION['SHOW_MODAL']);
	}

	$_SESSION['SHOW_MODAL'] = NULL;
	unset($_SESSION['SHOW_MODAL']);	
	}
	return $ret;		
}

function getWebPage(){
	
	$ret = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"
\"http://www.w3.org/TR/html4/loose.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:fb=\"http://www.facebook.com/2008/fbml\">\n";
	
	$AlertDivs = $this->CheckOverlayAlerts();
	$this->PageBody .= $AlertDivs;
	
	$retBody = $this->getBody(); // Need to know if scripts needed for header.
	
	$ret .= $this->getHeader();
	
	$ret .= $retBody;

	
	$ret .= $AlertDivs."
	\n</html>";
	return $ret;
}

} // class

?>