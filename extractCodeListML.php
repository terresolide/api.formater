<?php
/**
* Extract code list from http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/gmxCodelists.xml
* */
$url = "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/codelist/ML_gmxCodelists.xml";
$doc = new DOMDocument();
$doc->load($url);
// $xpath = new DOMXpath($doc);
// $ns = $doc->documentElement->namespaceURI;
// $xpath->registerNamespace("gml", $ns);
$nodes = $doc->getElementsByTagName("ML_CodeListDictionary");
function extractValues($node) {
	global $xpath;
	$definition = $node->getElementsByTagName("ML_CodeDefinition")->item(0);

	//foreach ($definitions as $definition) {
		$identifier = $definition->getElementsByTagNameNS("*","identifier")->item(0);
		$description = $definition->getElementsByTagNameNS("*","description")->item(0);
		$name = $definition->getElementsByTagNameNS("*", "name")->item(0);
		$alternative =  $definition->getElementsByTagName("CodeAlternativeExpression")->item(0);
		$description_fr = $alternative->getElementsByTagNameNS("*","description")->item(0);
		$name_fr = $alternative->getElementsByTagNameNS("*", "name")->item(0);
		var_dump($name_fr);
		// var_dump(array($identifier->nodeValue, $description->nodeValue));
		return array($identifier->nodeValue, $name->nodeValue,  $description->nodeValue, !is_null($name_fr)? $name_fr->nodeValue : "", !is_null($description_fr) ? $description_fr->nodeValue : "");
		// var_dump($tab);

	
}
$values = array();
foreach ($nodes as $codelist) {
	$list = array();
	$childs = $codelist->childNodes;
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
	var_dump($list);
	$values[$name] = $list;
 	$fp = fopen('codeListML/'.$name.'.csv', 'w');
 	fputcsv($fp, array("identifier", "label", "description", "label_fr", "description_fr"));
 	foreach ($list as $fields) {
 		fputcsv($fp, $fields);
	}
	
 	fclose($fp);
 	chmod('codeListML/'.$name.'.csv', 0777); 
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