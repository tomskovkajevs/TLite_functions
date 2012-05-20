<?php

class TFieldsEdit extends tBase {

var $GlobalInnerID = "";
var $GlobalKeyField = "";
var $GlobalTable = "";
var $GlobalDatabase = null;
var $GlobalKeyValue = 0;

var $FieldSettings = Array();
var $FieldData = Array();

var $RegisteredEditors = Array();
var $RegisteredActions = Array();

var $LayoutHTML = "";

function clearSessionVars() {
}

function SetLayoutHTML($LayoutHTML){
	$this->LayoutHTML = $LayoutHTML;
}
function setDatabase($DBName){
	$this->GlobalDatabase = $DBName;
}

function getModuleName() {
	return "Fields_Edit_Class";
}
function call__SetVariableNames() {
	//$this->SetVariableNames();
}

function TFieldsEdit($EditorID = ""){
	$this->GlobalInnerID = "a".$EditorID;
}

function getGlobalID(){
	return $this->GlobalInnerID;
}

function FindRow($table, $keyField, $KeyValue){
	$this->GlobalTable = $table;
	$this->GlobalKeyField = $keyField;
	$this->GlobalKeyValue = $KeyValue;
	$this->FieldSettings = $this->getDefaultFieldsettings();
}

function TypeSimple($TypeStr) {
	$tips = $TypeStr;
	if (substr_count($tips,"int") > 0) {
		$tips = "int";
	}
	if (substr_count($tips,"varchar") > 0) {
		$tips = "varchar";
	}
	return $tips;
}

function getAllTypesSimple(){
	$ret = Array();

	$data = sqlsel("describe $this->GlobalTable");

	while (list($key, $value) = @each($data))
	{
		$ret[$value['Field']] = $this->TypeSimple($value['Type']);
	}
	return $ret;
}

function getDefaultFieldsettings(){
	$ret = Array();

	$data = sqlsel("describe $this->GlobalTable");
	//dm($data);
	while (list($key, $value) = @each($data))
	{
		$FieldConfig['Name'] = $value['Field'];
		$FieldConfig['Visible'] = true;
		$FieldConfig['Default'] = $value['Default'];
		$FieldConfig['Extra'] = $value['Extra'];
		$FieldConfig['Null'] = $value['Null'];
		$FieldConfig['Type'] = $this->TypeSimple($value['Type']);
		$ret[$value['Field']] = $FieldConfig;
	}
	return $ret;
}

function setAditionalFieldSettings($settings){
	$this->FieldSettings = array_combineA($this->FieldSettings,$settings);
}

function LoadData(){
	$mass = sqlsel("*",$this->GlobalTable," $this->GlobalKeyField = '$this->GlobalKeyValue' ");
//	$mass = selasoc("*",$this->GlobalTable," $this->GlobalKeyField = '$this->GlobalKeyValue' ");
	if (isset($mass[0])) {
		$mass = $mass[0];
		$this->FieldData = $mass;
	}
}

function SaveData(){
	$mass = $this->FieldData;
//	dm($mass);
	while (list($k, $v) = @each($mass))
	{

	}
	reset($mass);
	$ki = new sql_action($this->GlobalDatabase,$this->GlobalTable);
	//$ki->print_sql();
	$ki->update_row($mass," $this->GlobalKeyField = '$this->GlobalKeyValue' ");
}

function Edit(){
	$ret = "";
	$VISS = get_getY();
	$ret .= "<form method=\"POST\" id=\"form$this->GlobalInnerID\" name=\"form$this->GlobalInnerID\" action=\"?$VISS\">";
	$Lid = inputam($this->GlobalKeyValue);
	$this->LoadData();

	if (isset($_GET['deleteInvoice'.$this->GlobalInnerID])) {
		$VISS = get_getN();
		$ret = "<form method=\"POST\" action=\"?$VISS\">";
		$ret .= "<div class=\"inActionsL\">
			<input type=\"submit\" name=\"deleteInvoiceAccept$this->GlobalInnerID\" value=\"Tiešām dzēst?\"><hr>
		</div>";
	}

	$this->processPostData();
	$ret .= $this->processFields($this->FieldSettings);

	$ret .= "<hr>";
	$ret .= $this->getAdditionalButtons();
	$ret .= $this->getMainActionButtons();

	$ret .= "</form>";
	return $ret;
}

function processPostData(){
	$VISS = get_getYx();
	$Lid = inputam($this->GlobalKeyValue);
	if (isset($_POST['deleteInvoice'.$this->GlobalInnerID])) {
		reload("?$VISS&deleteInvoice$this->GlobalInnerID=$Lid");
	}

	if (isset($_POST['deleteInvoiceAccept'.$this->GlobalInnerID])) {
		$toDel = new sql_action(null,$this->GlobalTable);
		$toDel->delete_where(" $this->GlobalKeyField = '$Lid' ");
		$DelBackLink = get_getNx("deleteInvoice".$this->GlobalInnerID,"look");
		reload("?$DelBackLink");
	}

	//	dm($_POST);

	if (isset($_POST["saveInvoice$this->GlobalInnerID"])) {
		$dati = $this->FieldData;
		$datiChanged = $dati;
		$fieldDefs = $this->FieldSettings;
		while (list($k, $v) = @each($dati))
		{
		//	dm($fieldDefs);
			$MustHave = false;
			$CheckBox = false;
			if (isset($fieldDefs[$k]['Editor'])) {
				if (($fieldDefs[$k]['Editor'] == "checkbox") && ($fieldDefs[$k]['Visible'] == "1")) {
					$MustHave = true;
					$CheckBox = true;
				}
			}
			if ($fieldDefs[$k]['Visible'] == "0") {
				$MustHave = false;
			}

			if (isset($_POST[$k])) {
				if ($CheckBox) {
					$datiChanged[$k] = 1;
				} else {
					$datiChanged[$k] = inputam($_POST[$k]);
				}
			}
			if ((isset($_POST[$k]) == false) && ($MustHave == true)) {
				$datiChanged[$k] = 0;
			}

		}
	//dm($datiChanged);
		$this->FieldData = $datiChanged;
		$this->SaveData();
		//reload();
	}

}

// ("SizesSelect","select","photosizes","id",Array("show"=>"UploadWorkClass::getPhotoSizesTextSmall"))
function RegisterEditor($EditorName,$EditorType,$SelectTable,$SelectKeyField,$Params = null){
	$Editor['Type'] = $EditorType;
	$Editor['Table'] = $SelectTable;
	$Editor['KeyField'] = $SelectKeyField;
	$Editor['Params'] = $Params;
	$this->RegisteredEditors[$EditorName] = $Editor;
}

function RegisterEditor2($EditorName, $EditorType = "file", $Params = null){
	$Editor['Type'] = $EditorType;
	$Editor['Params'] = $Params;
	$this->RegisteredEditors[$EditorName] = $Editor;
}

function RegisterAction($ActionName,$Params = Null){
	$this->RegisteredActions[$ActionName] = $Params;
}

function getEditorForMe($me,$value = null){
	//$ret = "<input type=\"checkbox\" name=\"$MyName\" %2 >";
	//echo "nododam: ".drm($me);
	$ret = "text";
	if (isset($me['Name']) == true) {
		$MyName = $me['Name'];
	}
	if (isset($me['Type']) == true) {
		if ($me['Type'] == "int") { 		$ret = "<input type=\"text\" name=\"$MyName\" %1 >"; }
		if ($me['Type'] == "varchar") { 	$ret = "<input type=\"text\" name=\"$MyName\" %1 >"; }
		if ($me['Type'] == "datetime") { 	$ret = "<input type=\"text\" name=\"$MyName\" %1 >"; }
		if ($me['Type'] == "double") { 		$ret = "<input type=\"text\" name=\"$MyName\" %1 >"; }
		if ($me['Type'] == "text") { 		$ret = "<textarea style=\"height:150px;\" name=\"$MyName\">%3</textarea>"; }
	}
	if (isset($me['Editor'])) {
		if ($me['Editor'] == "text") { 		$ret = "<input type=\"text\" name=\"$MyName\" %1 >"; }
		if ($me['Editor'] == "textarea") { 		$ret = "<textarea style=\"height:150px;\" name=\"$MyName\">%3</textarea>"; }
		if ($me['Editor'] == "checkbox") { 	$ret = "<input type=\"checkbox\" name=\"$MyName\" %2 >"; }
		if ($me['Editor'] == "radio") { 	$ret = "<input type=\"radio\" name=\"$MyName\" %1 >"; }
		if ($me['Editor'] == "label") { 	$ret = "<div class=\"inputLabel\">%3</div>"; }
		if (array_key_exists($me['Editor'],$this->RegisteredEditors)) {
			include_once("FieldsEditEditor.class.php");
			$EditorName = $me['Editor'];
			$EditorData = $this->RegisteredEditors[$EditorName];
			$Editor = new FieldsEditEditor($EditorName,$EditorData,$MyName,$value);
			$Editor->setInnerID($this->GlobalInnerID);
			$Editor->LoadOtherFieldData($this->FieldData);
			$ret = $Editor->getEditor();
		}
	}


	if (isset($me['Visible']) == true) {
		if ($me['Visible'] == false) { $ret = "<input type=\"hidden\" name=\"$MyName\" %1 >"; }
	}
	//$ret .= drm($me);
	//$ret .= $me['Visible'];

	return $ret;
}

function processFields($FieldArr){
	$ret = "";
	$dati = $this->FieldData;
//dm($FieldArr);
	$UseLayoutHTML = false;
	if (strlen($this->LayoutHTML) > 0) {
		$ret = $this->LayoutHTML;
		$UseLayoutHTML = true;
	}

	while (list($k, $v) = @each($FieldArr))
	{
		//
		$retCurr = "";

		$FieldName = $k;
		$FieldType = "";
		$val = null;
		if (isset($dati[$k])) {
			$val = $dati[$k];
		}
		$TypeText = $this->getEditorForMe($v,$val);

		if (isset($v['Visible']) == true) { 
						
		} else {
			$v['Visible'] = 1;			
		}
		
		if ($v['Visible'] == 1) {
			$retCurr .= "<div class=\"inRowCT\">";
			if ((isset($v['DisplayText'])) && (strLen($v['DisplayText']) > 0)) {
				$FieldName = $v['DisplayText'];
				if ((isset($v['DisplayLabel'])) && (strLen($v['DisplayLabel']) == "no")) {

				} else {
					$retCurr .= "<div class=\"inLabel\">$FieldName</div>";
				}
			}
		} // if visible
		


		$TypeText = str_ireplace("%1"," value=\"$val\" ", $TypeText);

		$CheckVal = "";
		if ($val == 1) {
			$CheckVal = "checked";
		}
		$TypeText = str_ireplace("%2", $CheckVal, $TypeText);

		$TypeText = str_ireplace("%3", $val, $TypeText);

		$retCurr .= "$TypeText";

		if ($v['Visible'] == 1) {
			$retCurr .= "</div>";
		}
		if ($UseLayoutHTML == true) {
			$ret = str_ireplace("::$k::",$retCurr,$ret);
		} else {
			$ret .= $retCurr;
		}
	}

//	$ret .= drm($this->FieldData);

	return $ret;
}

function getMainActionButtons(){
	//$this->RegisteredActions[$ActionName]
	$ret = "
	<div class=\"inActionsR\">
		<input type=\"submit\" name=\"saveInvoice$this->GlobalInnerID\" value=\"Saglabāt\">
		<input type=\"submit\" name=\"deleteInvoice$this->GlobalInnerID\" value=\"Dzēst\">
	</div>
	";
	return $ret;
}

function getAdditionalButtons(){
	$ret = "";
	if (is_array($this->RegisteredActions)) {
		$ret .= "<div class=\"inActionsL\">";

		while (list($k, $v) = @each($this->RegisteredActions))
		{
			$texts = $k;
			if (isset($v['Text'])) { $texts = $v['Text']; }

			$type = "submit";
			if (isset($v['Type'])) { $type = $v['Type']; }

			$Other = "";
			if (isset($v['Other'])) { $Other = $v['Other']; }

			$Class = "";
			if (isset($v['Other'])) { $Other = $v['Other']; }

			$ret .= "<input type=\"$type\" name=\"$k$this->GlobalInnerID\" $Other value=\"$texts\">";
		}
		$ret .= "</div>";
	}
	return $ret;
}

} // class


?>