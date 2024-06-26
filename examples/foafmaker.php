<?php
/**
 * Construct a FOAF document with a choice of serialisations
 *
 * This example is similar in concept to Leigh Dodds' FOAF-a-Matic.
 * The fields in the HTML form are inserted into an empty
 * EasyRdf\Graph and then serialised to the chosen format.
 *
 * @copyright  Copyright (c) 2009-2014 Nicholas J Humfrey
 * @license    http://unlicense.org/
 */
require_once realpath(__DIR__.'/..').'/vendor/autoload.php';
require_once __DIR__.'/html_tag_helpers.php';

if (isset($_REQUEST['enable_arc']) && $_REQUEST['enable_arc']) {
    EasyRdf\Format::registerSerialiser('ntriples', 'EasyRdf\Serialiser\Arc');
    EasyRdf\Format::registerSerialiser('posh', 'EasyRdf\Serialiser\Arc');
    EasyRdf\Format::registerSerialiser('rdfxml', 'EasyRdf\Serialiser\Arc');
    EasyRdf\Format::registerSerialiser('turtle', 'EasyRdf\Serialiser\Arc');
}

if (isset($_REQUEST['enable_rapper']) && $_REQUEST['enable_rapper']) {
    EasyRdf\Format::registerSerialiser('dot', 'EasyRdf\Serialiser\Rapper');
    EasyRdf\Format::registerSerialiser('rdfxml', 'EasyRdf\Serialiser\Rapper');
    EasyRdf\Format::registerSerialiser('turtle', 'EasyRdf\Serialiser\Rapper');
}

$format_options = [];
foreach (EasyRdf\Format::getFormats() as $format) {
    if ($format->getSerialiserClass()) {
        $format_options[$format->getLabel()] = $format->getName();
    }
}
?>
<html>
<head><title>EasyRdf FOAF Maker Example</title></head>
<body>
<h1>EasyRdf FOAF Maker Example</h1>

<?php echo form_tag(null, ['method' => 'POST']); ?>

<h2>Your Identifier</h2>
<?php echo labeled_text_field_tag('uri', 'http://www.example.com/joe#me', ['size' => 40]); ?><br />

<h2>Your details</h2>
<?php echo labeled_text_field_tag('title', 'Mr', ['size' => 8]); ?><br />
<?php echo labeled_text_field_tag('given_name', 'Joseph'); ?><br />
<?php echo labeled_text_field_tag('family_name', 'Bloggs'); ?><br />
<?php echo labeled_text_field_tag('nickname', 'Joe'); ?><br />
<?php echo labeled_text_field_tag('email', 'joe@example.com'); ?><br />
<?php echo labeled_text_field_tag('homepage', 'http://www.example.com/', ['size' => 40]); ?><br />

<h2>People you know</h2>
<?php echo labeled_text_field_tag('person_1', 'http://www.example.com/dave#me', ['size' => 40]); ?><br />
<?php echo labeled_text_field_tag('person_2', '', ['size' => 40]); ?><br />
<?php echo labeled_text_field_tag('person_3', '', ['size' => 40]); ?><br />
<?php echo labeled_text_field_tag('person_4', '', ['size' => 40]); ?><br />

<h2>Output</h2>
Enable Arc 2? <?php echo check_box_tag('enable_arc'); ?><br />
Enable Rapper? <?php echo check_box_tag('enable_rapper'); ?><br />
<?php echo label_tag('format').select_tag('format', $format_options, 'rdfxml'); ?><br />

<?php echo submit_tag(); ?>
<?php echo form_end_tag(); ?>


<?php
if (isset($_REQUEST['uri'])) {
    $graph = new EasyRdf\Graph();

    // 1st Technique
    $me = $graph->resource($_REQUEST['uri'], 'foaf:Person');
    $me->set('foaf:name', $_REQUEST['title'].' '.$_REQUEST['given_name'].' '.$_REQUEST['family_name']);
    if ($_REQUEST['email']) {
        $email = $graph->resource('mailto:'.$_REQUEST['email']);
        $me->add('foaf:mbox', $email);
    }
    if ($_REQUEST['homepage']) {
        $homepage = $graph->resource($_REQUEST['homepage']);
        $me->add('foaf:homepage', $homepage);
    }

    // 2nd Technique
    $graph->addLiteral($_REQUEST['uri'], 'foaf:title', $_REQUEST['title']);
    $graph->addLiteral($_REQUEST['uri'], 'foaf:givenname', $_REQUEST['given_name']);
    $graph->addLiteral($_REQUEST['uri'], 'foaf:family_name', $_REQUEST['family_name']);
    $graph->addLiteral($_REQUEST['uri'], 'foaf:nick', $_REQUEST['nickname']);

    // Add friends
    for ($i = 1; $i <= 4; ++$i) {
        if ($_REQUEST["person_$i"]) {
            $person = $graph->resource($_REQUEST["person_$i"]);
            $graph->add($me, 'foaf:knows', $person);
        }
    }

    // Finally output the graph
    $data = $graph->serialise($_REQUEST['format']);
    if (!is_scalar($data)) {
        $data = var_export($data, true);
    }
    echo '<pre>'.htmlspecialchars($data).'</pre>';
}

?>

</body>
</html>
