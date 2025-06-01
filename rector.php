<?php

use Rector\Config\RectorConfig;

return RectorConfig::configure()->withImportNames(removeUnusedImports: true)->withRootFiles()
    ->withPaths([__DIR__ . '/src'])->withPreparedSets(
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
    );
