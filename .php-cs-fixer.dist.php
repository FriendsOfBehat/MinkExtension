<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/spec')
;

return (new Config())
    ->setRules([
        '@Symfony' => true,
        'phpdoc_to_comment' => ['ignored_tags' => ['psalm-suppress', 'phpstan-ignore']],
    ])
    ->setFinder($finder)
;
