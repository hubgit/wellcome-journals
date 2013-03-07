<?php

define('DATA_DIR', realpath(__DIR__ . '/../data'));

$xsl = new DOMDocument;
$xsl->load(__DIR__ . '/transform.xsl');

$processor = new XSLTProcessor;
$processor->importStylesheet($xsl);

$counts = array();
$titles = array();

foreach (glob(DATA_DIR . '/*.xml') as $file) {
	print "Indexing $file\n";

	$input = new DOMDocument;
	$input->load($file);

	$doc = $processor->transformToDoc($input);
	$articles = json_decode(json_encode(simplexml_import_dom($doc)));

	foreach ($articles->Article as $article) {
		$issn = $article->ISSN;
		$year = $article->Year;

		if (!is_string($issn) || !is_string($year)) {
			continue;
		}

		$counts[$year][$issn]++;

		if (!array_key_exists($issn, $titles)) {
			$titles[$issn] = (string) $article->Title;
		}
	}
}

$outputFile = DATA_DIR . '/output.csv';
$output = fopen($outputFile, 'w');
fputcsv($output, array('Year', 'ISSN', 'Journal', 'Articles'));

ksort($counts, SORT_NUMERIC);

foreach ($counts as $year => $issns) {
	arsort($issns, SORT_NUMERIC);

	foreach ($issns as $issn => $count) {
		fputcsv($output, array($year, $issn, $titles[$issn], $count));
	}
}

print "Data written to $outputFile\n";

