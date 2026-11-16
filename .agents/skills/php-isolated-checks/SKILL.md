---
name: php-isolated-checks
description: Run all Nexi Checkout SDK quality checks in a fresh PHP 8.2 Docker environment using the current local source, fresh dependencies, and no host caches.
---

# Fresh Docker checks

1. Use the current checkout, including local code changes. Do not clone it. Default
   to `php:8.2-cli` unless another PHP version is explicitly requested.
2. Keep the Dockerfile inline. Run these commands from the repository root,
   sequentially, retaining the check result before cleanup:

   ```sh
   docker build --pull --no-cache -t nexi-check -f - . <<'DOCKERFILE'
   FROM php:8.2-cli
   COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
   RUN apt-get update \
       && apt-get install -y --no-install-recommends unzip \
       && rm -rf /var/lib/apt/lists/*
   WORKDIR /workspace
   COPY composer.json Makefile phpunit.xml.dist phpstan.neon.dist ecs.php rector.php ./
   COPY lib/ ./lib/
   COPY tests/ ./tests/
   COPY examples/ ./examples/
   DOCKERFILE
   docker run --rm nexi-check sh -c 'php --version && composer install --no-cache --no-interaction --prefer-dist --no-scripts --no-plugins || exit $?; make -k check-all; checks=$?; composer audit --locked --no-interaction; audit=$?; test "$checks" -eq 0 && test "$audit" -eq 0'
   docker image rm nexi-check
   ```

   Run the container only if the build succeeds. Remove the image even if checks
   fail; a successful cleanup does not mean checks passed. The fixed image tag is
   for sequential runs. Do not create a separate Dockerfile or wrapper script.
3. Copy only `composer.json`, `Makefile`, `phpunit.xml.dist`, `phpstan.neon.dist`,
   `ecs.php`, `rector.php`, `lib/`, `tests/`, and `examples/` from the checkout.
   Copy examples for static analysis, but do not execute example scripts as tests.
   Leave analysis configurations unchanged.
   Adapt this list if project configuration changes. Never copy host `vendor/`,
   tool caches, or Git metadata, and do not mount host directories.
4. The Dockerfile includes Composer 2 and unzip. Check existing PHP modules before
   installing any additional extensions.
   `-k` runs the remaining checks even when one fails. This covers PHPStan,
   Rector dry-run, ECS, and PHPUnit. Also run the dependency audit in the same
   container, even if a quality check fails; preserve both results.
   Never copy or use the host `composer.lock`; always resolve fresh dependencies
   inside the container and audit its newly generated lock. Do not bypass platform checks
   or change dependency constraints to make installation pass.
5. Report actual PHP version and each check's result, including PHPUnit counts.
   Dependency installation failures block checks. Remove this run's temporary
   image on success or failure; `--rm` removes the container and its workspace.
   Leave local source, dependencies, and Composer files unchanged.
