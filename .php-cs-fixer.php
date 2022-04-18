<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->in('tests')
    ->append([__FILE__])
;

$config = new PhpCsFixer\Config();

return $config
    ->setRules(
        [
            '@PSR12' => true,
            '@Symfony' => true,
            '@PhpCsFixer' => true,
            '@DoctrineAnnotation' => true,
            '@PHP80Migration:risky' => true,
            '@PHP81Migration' => true,
            'declare_strict_types' => false,
            'ordered_class_elements' => false,
            'no_superfluous_phpdoc_tags' => false,
            'strict_param' => true,
            'array_syntax' => ['syntax' => 'short'],
            'concat_space' => ['spacing' => 'one'],
            'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
            'phpdoc_tag_type' => [
                'tags' => ['inheritDoc' => 'annotation'],
            ],
            'phpdoc_tag_casing' => [
                'tags' => ['inheritDoc'],
            ],
        ]
    )
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
