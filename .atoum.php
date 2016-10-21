<?php

use \mageekguy\atoum;

$report = $script->addDefaultReport();

$coverageField = new atoum\report\fields\runner\coverage\html('Trampoline', './reports/');
$report->addField($coverageField);

$runner->addTestsFromDirectory('./tests');
