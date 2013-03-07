wellcome-journals
=================

1. php eutils/search.php db term -> history parameters in JSON file
1. php eutils/fetch.php {result-file}.json -> fetch all XML to data/
1. xmllint --loaddtd --noout --valid data/*.xml
1. php scripts/analyse.php -> convert data/*.xml to JSON, output as CSV
