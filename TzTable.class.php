<?php
//echo "Including TTable file<hr>";
class TzTable {

var $FieldDef = Array();
var $globalRet = "";
var $globalRowCount = 0;
var $STRIP_TAGS = true;
var $STRIP_TAGS_ALLOWABLE_TAGS ="";

function SetFields($FieldArr){
	$this->FieldDef = $FieldArr;
}

function keyForNr($Nr){
	$ret = "";
	$manz = $this->FieldDef;
	reset($manz);
	$colNr = 0;
	while (list($keyu, $vau) = @each($manz))
	{
		$colNr++;
		if ($colNr == $Nr) {
			$ret = $keyu;
		}
	}
	return $ret;
}

function get_ColSortable($SortNr){
	$ret = false;

	$keyForSort = $this->keyForNr($SortNr);

	$keyType = $this->getKeyType($keyForSort);
	if (
		($keyType == 0) or
		($keyType == 3) or
		($keyType == 4) or
		($keyType == 1)
		)
	{
		$ret = true;
	}
	return $ret;
}


function get_SortType(){
	$ret = "";
	if (isset($_GET['sorttype'])) {
		if ($_GET['sorttype'] == "asc") {
			$ret = "desc";
		} else {
			$ret = "asc";
		}
	} else {
		$ret = "desc";
	}
	return $ret;
}

function get_sortString($SortNr){
	$ret = "";
	if ($this->get_ColSortable($SortNr)) {
		$key = $this->keyForNr($SortNr);
		$KeyType = $this->getKeyType($key);
		$SortType = $this->get_SortType();
		if ($KeyType == 0) {
			$ret .= "order by $key $SortType";
		}
		if ($KeyType == 1) {
			$mass = explode(' ',$key);
			$field = $mass[1];
			$ret .= "order by $field $SortType";
		}
		if ($KeyType == 3) {
			$mass = explode(" ",$key);
			$ret .= "order by ";
			$bija = "";
			while (list($k, $v) = @each($mass))
			{
				$ret .= "$bija $v $SortType";
				$bija = ",";
			}
			$ret .= "";
		}
		if ($KeyType == 4) {
			$mass = explode(":",$key);
			$field = $mass[4];
			$ret .= "order by $field $SortType";
		}
	}
	return $ret;
}

function getKeyType($key){
	$type = -1;
	if (substr_count($key, '::') > 0) {
		// Nodod iegūto vērtību statiskai klasei/funkcijai
		$type = 1;
	} elseif (substr_count($key, ':') >= 3) {
		// Konstrukcija....
		$mass = explode(":",$key);
		if ($mass[0] == "link") {
			$type = 2;
		}
		if ($mass[0] == "table") {
			$type = 4;
		}
	} elseif (substr_count($key, ' ') > 0) {
		// concatenate separating with space
		$type = 3;
	} else {
		// return field value
		$type = 0;
	}
	return $type;
}

function workRowCell($key,$RowData){
	$ret = "";
	if ($this->getKeyType($key) == 1) {
		// Nodod iegūto vērtību statiskai klasei/funkcijai
		$mass = explode(" ", $key);
		$funkc = $mass[0];
		$lauks = $mass[1];
		$laukaVert = $RowData[$lauks];
		eval(" \$ret .= $funkc(\"$laukaVert\"); ");
	} elseif ($this->getKeyType($key) == 2) {
		// Konstrukcija....
		$mass = explode(":",$key);
		//$ret .= drm($mass);
		if ($mass[0] == "link") {
			$varble = $mass[1];
			$field = $mass[2];
			$fieldVal = $RowData[$field];
			$text = $mass[3];
			$TAGAx = get_getN($varble);
			$ret .= "<a href=\"?$TAGAx&amp;$varble=$fieldVal\">$text</a>";
		}
	} elseif ($this->getKeyType($key) == 3) {
		// concatenate separating with space
		$mass = explode(' ',$key);
		$bija = "";
		while (list($k, $v) = @each($mass))
		{
			$ret .= $bija.$RowData[$v];
			$bija = " ";
		}
	} elseif ($this->getKeyType($key) == 4) {
		$mass = explode(":",$key);
		if ($mass[0] == "table") {
			// table:photostatuss:id:Status:uConfirmed
			$tablename = $mass[1];
			$KeyField = $mass[2];
			$ShowField = $mass[3];
			$maField = $mass[4];
			if (strLen($RowData[$maField]) > 0) {
				$ret .= db_get($ShowField,$tablename," $KeyField = '".$RowData[$maField]."' ");
			}
		}
	} else {
		// return field value
		$ret .= $RowData[$key];
	}

	return $ret;
}

function StripTags($Strip, $allowable = ""){
	if ($Strip == true) {
		$this->STRIP_TAGS = true;
		$this->STRIP_TAGS_ALLOWABLE_TAGS = $allowable;
	} else {
		$this->STRIP_TAGS = false;
	}

}

function getRowCount() {
	return $this->globalRowCount;
}

function AddRow($RowData){
	$this->globalRowCount++;
	reset($this->FieldDef);
	$this->globalRet .= "<tr class=\"inRow\" style=\"background-color:#ffffff;\" onMouseOver=\"javascript:if (highlightTableRowVersionA) { highlightTableRowVersionA(this, '#CCCCCC'); } \">";
//	onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='normal'\">
	$colNr = 0;
	while (list($keyu, $vau) = @each($this->FieldDef))
	{
		$colNr++;
		$this->globalRet .= "<td class=\"inCell\">";
		$data = notNull($this->workRowCell($keyu, $RowData));
		if ($this->STRIP_TAGS) {
			$this->globalRet .= strip_tags($data,"<a>".$this->STRIP_TAGS_ALLOWABLE_TAGS);
		} else {
			$this->globalRet .= $data;
		}

		$this->globalRet .= "</td>";
	}
	$this->globalRet .= "</tr>";
}

static function getUsedGets(){
	return Array("sort","sorttype");
}

function getTableHeadRow(){
	$ret = "";
	reset($this->FieldDef);
	$mansDef = $this->FieldDef;
	$ret .= "<tr class=\"inRowHead\">";
	$NOWW = get_getN("sort","sorttype");
	$colNr = 0;
	while (list($keyu, $vau) = @each($mansDef))
	{
		$colNr++;
		$celltext = "$vau";
		if ($this->get_ColSortable($colNr)) {
			$getType = $this->get_SortType();
			$celltext = "<a href=\"?$NOWW&amp;sort=$colNr&amp;sorttype=$getType\">$vau</a>";
		}
		$ret .= "<td class=\"inCellH\">$celltext</td>";
	}
	$ret .= "</tr>";
	return $ret;
}

function getTable(){
	$ret = "";
	$ret .= "<table  border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"inTable\">";
	$ret .= $this->getTableHeadRow();
	if ($this->globalRowCount > 0) {
		$ret .= $this->globalRet;
	}
	reset($this->FieldDef);
	$ret .= "</table>";
	//dm($this->FieldDef);
	return $ret;
}

} // class


?>