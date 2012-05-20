<?php

class FieldsEditEditor {

	var $GlobalEditorName = "";
	var $GlobalEditorData = Array();
	var	$GlobalFieldName = "";
	var $GlobalFieldValue = null;
	var $GlobalFieldDataAll = Array();
	var $GlobalInnerID = "";

function FieldsEditEditor($EditorName,$EditorData, $FieldName, $FieldValue = null){
	$this->GlobalEditorName = $EditorName;
	$this->GlobalEditorData = $EditorData;
	$this->GlobalFieldName = $FieldName;
	$this->GlobalFieldValue = $FieldValue;
}

function setInnerID($newID){
	$this->GlobalInnerID = $newID;
}

function getEditor(){
	$ret = "";
	if (isset($this->GlobalEditorData['Type'])) {
		if ($this->GlobalEditorData['Type'] == "select") {
			$ret .= $this->getSelect();
		} elseif ($this->GlobalEditorData['Type'] == "gallery") {
			$ret .= $this->getGallery();
		} elseif ($this->GlobalEditorData['Type'] == "date") {
			$ret .= $this->getDate();
		} elseif ($this->GlobalEditorData['Type'] == "html") {
			$ret .= $this->getHTML();
		} elseif ($this->GlobalEditorData['Type'] == "MakeZip") {
			$ret .= $this->getFileArchieve();
		}
		 else {
			$ret .= "<div class=\"inputLabel\">CustomEditor: $this->GlobalEditorName = [%3]</div>";
		}
	} else {
		$ret .= "<div class=\"inputLabel\">CustomEditor: ERROR - no Type for $this->GlobalEditorName = %3</div>";
	}
	return $ret;
}

function LoadOtherFieldData($data){
	$this->GlobalFieldDataAll = $data;
}

function getHTML() {
	$ret = "";	

	$EditorData = $this->GlobalEditorData;
	$MyName = $this->GlobalFieldName;
	$value = $this->GlobalFieldValue;

	//global $page;
		
	
$ret .= "
				<TEXTAREA NAME=\"$MyName\" ID=\"$MyName\" width=\"100%\" height=\"900px\">$value</TEXTAREA>
	<script>
	var oEdit1$MyName = new InnovaEditor(\"oEdit1$MyName\");
        oEdit1$MyName.css=\"".tilts_moduleWWWRoot()."css/main.css\";
        oEdit1$MyName.width=850;
        oEdit1$MyName.heigth=900;
        oEdit1$MyName.features=[\"Save\",\"FullScreen\",\"Preview\",\"Print\",
			\"Search\",\"SpellCheck\",\"|\",
			\"Superscript\",\"Subscript\",\"|\",\"LTR\",\"RTL\",\"|\",
			\"Table\",\"Guidelines\",\"Absolute\",\"|\",
			\"Flash\",\"Media\",\"|\",\"InternalLink\",\"CustomObject\",\"|\",
			\"Form\",\"Characters\",\"ClearAll\",\"XHTMLFullSource\",\"XHTMLSource\",\"BRK\",
			\"Cut\",\"Copy\",\"Paste\",\"PasteWord\",\"PasteText\",\"|\",
			\"Undo\",\"Redo\",\"|\",\"Hyperlink\",\"Bookmark\",\"Image\",\"|\",
			\"JustifyLeft\",\"JustifyCenter\",\"JustifyRight\",\"JustifyFull\",\"|\",
			\"Numbering\",\"Bullets\",\"|\",\"Indent\",\"Outdent\",\"|\",
			\"Line\",\"RemoveFormat\",\"BRK\",
			\"StyleAndFormatting\",\"TextFormatting\",\"ListFormatting\",
			\"BoxFormatting\",\"ParagraphFormatting\",\"CssText\",\"Styles\",\"|\",
			\"CustomTag\",\"Paragraph\",\"FontName\",\"FontSize\",\"|\",
			\"Bold\",\"Italic\",\"Underline\",\"Strikethrough\",\"|\",
			\"ForeColor\",\"BackColor\",\"|\"];
    	oEdit1$MyName.cmdAssetManager = \"modalDialogShow('".tilts_moduleWWWRoot()."assetmanager/assetmanager.php',640,465)\";
		oEdit1$MyName.REPLACE(\"$MyName\");
	</script>

";
//     	oEdit1.cmdAssetManager = \"modalDialogShow('../assetmanager/assetmanager.php',640,465)\";
	
	return $ret;
}

function getDate() {
	$EditorData = $this->GlobalEditorData;
	$MyName = $this->GlobalFieldName;
	$value = $this->GlobalFieldValue;
	$ret = "<input type=\"text\" name=\"$MyName\" id=\"datefield$MyName\" />";
	$ret .= "
<script>
	$('#datefield$MyName').datepicker();
</script>
	";
	return $ret;
}

function getSelect(){
	$EditorData = $this->GlobalEditorData;
	$MyName = $this->GlobalFieldName;
	$value = $this->GlobalFieldValue;
		$ret = "<select name=\"$MyName\">";
		$EditorParams = Array();
		$SQLWhere = null;
		if (isset($EditorData['Params'])) {
			$EditorParams = $EditorData['Params'];
			if (isset($EditorParams['where'])) { $SQLWhere = $EditorParams['where']; }
		}
		$SelectFields = $EditorData['KeyField']; // SELECT id ....
		if (isset($EditorParams['show'])) {
			$SelectFields .= ", ".$EditorParams['show']; // SELECT id, name .....
		}
		$selData = sqlsel($SelectFields,$EditorData['Table'],$SQLWhere);
		$SelArr = Array();
		$SelArr[""] = "";
		while (list($k, $v) = @each($selData))
		{
			$IsData = $v[$EditorData['KeyField']];
			$KeyValue = $v[$EditorData['KeyField']];
			if (isset($EditorParams['show'])) {
				if (isset($EditorParams['show'])) {
					$IsData = $v[$EditorParams['show']];
				}
			} elseif (isset($EditorParams['showEval'])) {
				$EvalSring = $EditorParams['showEval'];
				eval("\$IsData = $EvalSring('$KeyValue'); ");
			}
			$SelArr[$v[$EditorData['KeyField']]] = $IsData;
		}
		$ret .= build_arr_opt_vals($SelArr,$value);
		$ret .= "</select>";
	return $ret;
}

function getGallery(){
	$ret = "";
	$EditorData = $this->GlobalEditorData;
	$MyName = $this->GlobalFieldName;
	$value = $this->GlobalFieldValue;

	$GalleryParams = $EditorData['Params'];
	$SessVal = $this->GlobalFieldDataAll[$GalleryParams['RecDirField']];
//	$ret .= drm($GalleryParams);
//	$ret .= "<hr>$SessVal";

	$Cl = new UploadModule();
	$Cl->setRegCode($SessVal);
	$Cl->CheckFileDeleted();
	// $Cl->get_FilesCount()
	if ($Cl->get_FilesCount() > 0) {
		$OtherMessage = "<div class=\"inLabelCL\">Count: ".$Cl->get_FilesCount()."</div>";
	} else {
		$OtherMessage = "<div class=\"inLabelCL\"></div>";
	}
	$ret .= $Cl->get_InvoiceImagesDiv(true,false,Array("ShowTitle"=>false,"ShowTopLine"=>false,"OtherMessage"=>$OtherMessage));

	return $ret;
}

function getFileArchieve(){
	$ret = "";

	$EditorData = $this->GlobalEditorData;
	$MyName = $this->GlobalFieldName;
	$value = $this->GlobalFieldValue;
	$ZipParams = $EditorData['Params'];
	$DirectoryName = $this->GlobalFieldDataAll[$ZipParams['RecDirField']];
	$FileMask = $ZipParams['TargetFileNameMask'];
	$NewFileName = $this->translateString($FileMask);
	$GetDataPath = $ZipParams['BasePath'].$DirectoryName."/";
	$maFiles = $ZipParams['Files'];
	$ret .= "<div class=\"inActionsL\">&nbsp;<br></div>";

	if (file_exists($ZipParams['BasePath'].$NewFileName)) {
		$TAGAD = get_getY();
		$ret .= "<div class=\"inActionsL\"><a target=\"_blank\" href=\"pic.php?zipFile&file=$NewFileName\">Download file</a><br><br></div>";
		$ret .= "<div class=\"inActionsL\"><input type=\"submit\" name=\"deleteZipArchive$this->GlobalInnerID\" value=\"Delete ZIP\"></div>";
	}

	if (isset($_POST['createZipArchive'.$this->GlobalInnerID])) {
		chdir($GetDataPath);
		include_once ('Archive/Zip.php');        // imports
		$obj = new Archive_Zip($ZipParams['BasePath'].$NewFileName); // name of zip file
		if ($obj->create($maFiles)) {
			//$ret .= 'Archive created successfully!';
		} else {
			//$ret .= 'Error in archive file creation';
		}
		reload();
	}

	if (isset($_POST['deleteZipArchive'.$this->GlobalInnerID])) {
		if (file_exists($ZipParams['BasePath'].$NewFileName)) {
			@unlink($ZipParams['BasePath'].$NewFileName);
		}
		reload();
	}

	$ret .= "<div class=\"inActionsL\"><input type=\"submit\" name=\"createZipArchive$this->GlobalInnerID\" value=\"Make ZIP\"></div>";
	return $ret;
}


function translateString($ret){
	$exp1 = explode(".",$ret);
	$paplasinajums = $exp1[count($exp1)-1];

	$sakums = substr($ret,0,strlen($ret) - strlen($paplasinajums)-1);

	$lauki = explode("_",$sakums);
	$sakums = "";
	$bija = "";
	while (list($k, $v) = @each($lauki))
	{
		$fn = trim(str_ireplace("%", "", $v));
		if ((isset($this->GlobalFieldDataAll[$fn]))) {
			$sakums .= $bija.$this->GlobalFieldDataAll[$fn];
			$bija = "_";
		}
	}
	$sakums =  str_ireplace("-", "", $sakums);
	$sakums =  str_ireplace(" ", "_", $sakums);
	$sakums =  str_ireplace(":", "", $sakums);
	$ret = $sakums.".".$paplasinajums;
	return $ret;
}

} // class

?>