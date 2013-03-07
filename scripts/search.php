<?php

$params = array(
	'db' => 'pubmed',
	'retmode' => 'xml',
	'term' => 'wellcome[GR]',
	'retstart' => 0,
	'retmax' => 0,
	'usehistory' => 'y',
);

$url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?' . http_build_query($params);
print "$url\n";

$dom = new DOMDocument;
$dom->load($url);

$result = array(
	'count' => $dom->getElementsByTagName('Count')->item(0)->textContent,
	'webenv' => $dom->getElementsByTagName('WebEnv')->item(0)->textContent,
	'querykey' => $dom->getElementsByTagName('QueryKey')->item(0)->textContent,
);

$file = sprintf(__DIR__ . '/../data/result-%d.json', time());
file_put_contents($file, json_encode($result));

print_r($result);
print "Saved to $file\n";
