<?php

list($command, $db, $term) = $argv;

if (!$db || !$term) {
	exit("Usage: $command db term\n");
}

$params = array(
	'db' => $db,
	'term' => $term, // e.g. 'wellcome[GR]'
	'retmax' => 0,
	'retstart' => 0,
	'retmode' => 'xml',
	'usehistory' => 'y',
);

$url = 'http://eutils.be-md.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?' . http_build_query($params);
print "$url\n";

$dom = new DOMDocument;
$dom->load($url);

$result = array(
	'db' => $db,
	'count' => $dom->getElementsByTagName('Count')->item(0)->textContent,
	'webenv' => $dom->getElementsByTagName('WebEnv')->item(0)->textContent,
	'querykey' => $dom->getElementsByTagName('QueryKey')->item(0)->textContent,
);

$file = sprintf(__DIR__ . '/../data/result-%d.json', time());
file_put_contents($file, json_encode($result));

print_r($result);
print "Saved to $file\n";
