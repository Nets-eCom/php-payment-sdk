<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer;
use PhpCsFixer\Fixer\Operator\NewWithBracesFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLinePromotedPropertyFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/lib',
        __DIR__ . '/tests',
    ])
    ->withPreparedSets(psr12: true, common: true)
    ->withConfiguredRule(
        NewWithBracesFixer::class,
        ['anonymous_class' => false]
    )
    ->withSkip([
        NotOperatorWithSuccessorSpaceFixer::class,
        StandaloneLinePromotedPropertyFixer::class,
        BlankLineAfterOpeningTagFixer::class,
        SelfAccessorFixer::class,
        BlankLineAfterOpeningTagFixer::class,
    ]);
