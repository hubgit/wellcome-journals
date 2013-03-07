<?php

$xsl = new DOMDocument;
$xsl->load(__DIR__ . '/transform.xsl');

$processor = new XSLTProcessor;
$processor->importStylesheet($xsl);

$counts = array();
$titles = array();

foreach (glob(__DIR__ . '/../data/*.xml') as $file) {
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

$output = fopen(__DIR__ . '/../data/output.csv', 'w');

ksort($counts, SORT_NUMERIC);

foreach ($counts as $year => $issns) {
	arsort($issns, SORT_NUMERIC);

	foreach ($issns as $issn => $count) {
		fputcsv($output, array($count, $year, $issn, $titles[$issn]));
	}
}

