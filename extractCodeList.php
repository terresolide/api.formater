<?php
/**
* Extract code list from http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/gmxCodelists.xml
* */
$url = "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/gmxCodelists.xml";
$doc = new DOMDocument();
$doc->load($url);
$nodes = $doc->getElementsByTagName("CodeListDictionary");
function extractValues($node) {
	$definition = $node->getElementsByTagName("CodeDefinition")->item(0);

	//foreach ($definitions as $definition) {
		$identifier = $definition->getElementsByTagNameNS("*","identifier")->item(0);
		$description = $definition->getElementsByTagNameNS("*","description")->item(0);
		// var_dump(array($identifier->nodeValue, $description->nodeValue));
		return array($identifier->nodeValue, $description->nodeValue);
		// var_dump($tab);

	
}
$values = array();
foreach ($nodes as $codelist) {
	$childs = $codelist->childNodes;
	$list = array();
	foreach($childs as $child) {
	  switch($child->nodeName) {
	  	case 'gml:identifier':
	  		$name = $child->nodeValue;
	  		break;
	  	case 'codeEntry':
	  		$list [] = extractValues($child);
	  		break;
	  }
	}

	$values[$name] = $list;
	$fp = fopen('codeList/'.$name.'.csv', 'w');
	fputcsv($fp, array("identifier", "description_en"));
	foreach ($list as $fields) {
		fputcsv($fp, $fields);
	}
	
	fclose($fp);
	chmod('codeList/'.$name.'.csv', 0777); 
}
var_dump($values);

// function XML2Array(SimpleXMLElement $parent)
// {
// 	$array = array();
	
// 	foreach ($parent as $name => $element) {
// 		($node = & $array[$name])
// 		&& (1 === count($node) ? $node = array($node) : 1)
// 		&& $node = & $node[];
		
// 		$node = $element->count() ? XML2Array($element) : trim($element);
// 	}
	
// 	return $array;
// }

// $xml   = simplexml_load_file($url);
// $array = XML2Array($xml);

// var_dump($array['codelistItem']);