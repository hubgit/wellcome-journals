<?php

if (!isset($argv[1])) {
	exit("Usage: {$argv[0]} result-file.json\n");
}

if (!file_exists(__DIR__ . '/../data')) {
	throw new Exception('Data directory at ../data/ does not exist');
}

$result = json_decode(file_get_contents($argv[1]), true);
print_r($result);

if (!is_array($result) || !$result['count']) {
	exit('Result file not found at ' . $argv[1]);
}

$total = $result['count'];

$curl = curl_init();
curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$limit = 1000;

for ($offset = 0; $offset <= $total; $offset += $limit) {
	print "\tFetching: $offset of $total\n";

	$params = array(
		'db' => 'pubmed',
		'retmode' => 'xml',
		'query_key' => $result['querykey'],
		'webenv' => $result['webenv'],
		'retstart' => $offset,
		'retmax' => $limit,
	);

	// prepare
	$url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?' . http_build_query($params);
	$file = sprintf(__DIR__ . '/../data/%s.xml', md5($url));

	if (file_exists($file) && filesize($file)) {
		return;
	}

	// fetch
	$output = fopen($file, 'w');

	print "Copying\t$url\nto\t$file\n";
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_FILE, $output);
	curl_exec($curl);

	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	if (200 !== $code) {
		print "\tUnexpected code $code for $url\n";
		exit();
		// TODO: retry if failed
	}

	fclose($output);

	// validate
	libxml_use_internal_errors(true);

	$dom = new DOMDocument;
	$dom->validateOnParse = true;

	if (!$dom->load($file)) {
		rename($file, $file . '.txt');

		foreach (libxml_get_errors() as $error) {
			print "Error {$error->code} in {$error->file} on line {$error->line}: {$error->message}\n";
		}

		// TODO: refetch if invalid
	}

	libxml_clear_errors();
	libxml_use_internal_errors(false);
}
