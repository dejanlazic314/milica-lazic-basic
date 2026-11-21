<?php

declare(strict_types=1);

namespace Deployer;

/*
 * Deployment with Deployer 7 (Contao recipe). Run it from the DDEV container:
 *
 *   ddev auth ssh            (once per DDEV start, adds the SSH key)
 *   ddev exec vendor/bin/dep deploy
 *
 * Hosts (server, SSH user, paths) are kept in the private repository, in
 * private/deploy/hosts.yml, so that they are not public.
 */

require 'recipe/contao.php';

import(__DIR__.'/private/deploy/hosts.yml');

set('repository', 'https://github.com/dejanlazic314/milica-lazic-basic.git');
set('keep_releases', 5);

// Configuration comes from .env.local only (the recipe also shares config/parameters.yml)
set('shared_files', ['.env.local']);

// PHP-FPM runs as the site user, who also owns the files: nothing to make writable
set('writable_mode', 'skip');

after('deploy:failed', 'deploy:unlock');
