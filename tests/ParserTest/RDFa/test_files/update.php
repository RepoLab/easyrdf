<?php

/**
 * Script to update test cases from rdfa.info
 *
 * @copyright  Copyright (c) 2012-2014 Nicholas J Humfrey
 * @license    https://www.opensource.org/licenses/bsd-license.php
 */
require_once realpath(__DIR__.'/../../..').'/vendor/autoload.php';

$RDFA_VERSION = 'rdfa1.1';
$HOST_LANGUAGE = 'xhtml5';
$REFERENCE_DISTILLER = 'http://www.w3.org/2012/pyRdfa/extract?format=nt&rdfagraph=output&uri=';
$FIXTURE_DIR = __DIR__;

EasyRdf\RdfNamespace::set('test', 'http://www.w3.org/2006/03/test-description#');
EasyRdf\RdfNamespace::set('rdfatest', 'http://rdfa.info/vocabs/rdfa-test#');

$client = new EasyRdf\Http\Client();

$manifest = EasyRdf\Graph::newAndLoad('http://rdfa.info/test-suite/manifest.ttl');
foreach ($manifest->allOfType('test:TestCase') as $test) {
    if (!in_array($RDFA_VERSION, $test->all('rdfatest:rdfaVersion'))) {
        continue;
    }
    if (!in_array($HOST_LANGUAGE, $test->all('rdfatest:hostLanguage'))) {
        continue;
    }
    if ('test:required' != $test->get('test:classification')->shorten()) {
        continue;
    }

    $id = $test->localName();
    $title = $test->get('dc:title');
    $escapedTitle = addcslashes($title, '\'');

    // Download the test input
    $inputUri = "http://rdfa.info/test-suite/test-cases/$RDFA_VERSION/$HOST_LANGUAGE/$id.xhtml";
    $client->setUri("$inputUri");
    $response = $client->request();
    file_put_contents("$FIXTURE_DIR/$id.xhtml", $response->getBody());

    // Download the expected output
    $client->setUri($REFERENCE_DISTILLER.urlencode($inputUri));
    $response = $client->request();
    $lines = array_filter(preg_split("/[\r\n]+/", $response->getBody()));
    if (count($lines)) {
        $data = implode("\n", $lines)."\n";
    } else {
        $data = '';
    }
    file_put_contents("$FIXTURE_DIR/$id.nt", $data);

    // Output code for PHPUnit
    echo "    public function testCase$id()\n";
    echo "    {\n";
    if (strlen($title) < 80) {
        echo "        \$this->rdfaTestCase('$id', '$escapedTitle');\n";
    } else {
        echo "        \$this->rdfaTestCase(\n";
        echo "            '$id',\n";
        echo "            '$escapedTitle'\n";
        echo "        );\n";
    }
    echo "    }\n\n";
}
