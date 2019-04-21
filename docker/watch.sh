#!/usr/bin/env bash
set -ex

# Rerun the tests when any php file changes
find . -name '*.php' | entr ./docker/run_tests.sh
