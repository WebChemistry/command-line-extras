<?php

use WebChemistry\CommandLineExtras\CommandLineParser;

require __DIR__ . '/vendor/autoload.php';

$args = (new CommandLineParser('
	-x  description
	-a
', ['type' => []]));
var_dump($args->parse());
