<?php

/*
 * Files and directories left out of the TER artefact.
 * Used by .github/workflows/publish-ter.yml via TYPO3_EXCLUDE_FROM_PACKAGING.
 * Replaces the default list of typo3/tailor.
 * Directories match at the beginning of the path, files at the end of the file name.
 */
return [
    'directories' => [
        '.Build',
        '.ddev',
        '.git',
        '.github',
        '.idea',
        'tailor-version-artefact',
        'tailor-version-upload',
        'Tests',
        'docs',
        'var',
        'vendor',
    ],
    'files' => [
        '.editorconfig',
        '.gitattributes',
        '.gitignore',
        '.php-cs-fixer.dist.php',
        '.readthedocs.yaml',
        '.travis.yml',
        'CLAUDE.md',
        'CODE_OF_CONDUCT.md',
        'composer.lock',
        'fractor.php',
        'mkdocs.yml',
        'phpstan.neon',
        'rector.php',
    ],
];
