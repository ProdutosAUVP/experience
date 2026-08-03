#!/usr/bin/env bash
# Gera o .zip do plugin pronto para "Enviar plugin" no WordPress.
#
#   bash wordpress/package.sh
#
# Saída: dist/auvp-experience-<versão>.zip
set -euo pipefail

cd "$(dirname "$0")/.."

VERSION=$(grep -m1 "^ \* Version:" wordpress/auvp-experience/auvp-experience.php | awk '{print $3}')
OUT="dist/auvp-experience-${VERSION}.zip"

echo "→ Gerando assets do plugin"
node wordpress/build.js

echo "→ Verificando sintaxe PHP"
find wordpress/auvp-experience -name '*.php' -print0 | xargs -0 -n1 php -l > /dev/null

mkdir -p dist
rm -f "$OUT"

echo "→ Empacotando"
cd wordpress
zip -rq "../$OUT" auvp-experience \
  -x '*.DS_Store' -x '__MACOSX/*' -x '*/.*'
cd ..

echo "✓ $OUT ($(du -h "$OUT" | cut -f1))"
