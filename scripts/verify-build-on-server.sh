#!/bin/bash
# Run on server to debug why new menu does not show after deploy
# Usage: cd /var/www/escuela-presente && bash scripts/verify-build-on-server.sh

set -e
cd "$(dirname "$0")/.."

echo "=========================================="
echo "1. Latest commit (is server code up to date?)"
echo "=========================================="
git log -1 --oneline 2>/dev/null || true

echo ""
echo "=========================================="
echo "2. Does built main.js contain new menu code?"
echo "   (getMenuNodesForRole / menuByRole / __ESCUELA_TENANT_APP)"
echo "=========================================="
MAIN_JS=$(ls public/build/assets/main-*.js 2>/dev/null | head -1)
if [ -z "$MAIN_JS" ]; then
  echo "FAIL: public/build/assets/main-*.js not found. Run npm run build."
else
  if grep -q "getMenuNodesForRole\|menuByRole\|__ESCUELA_TENANT_APP" "$MAIN_JS" 2>/dev/null; then
    echo "OK: New menu code present: $MAIN_JS"
  else
    echo "FAIL: New menu code not found (old build). Run npm run build again."
  fi
fi

echo ""
echo "=========================================="
echo "3. Build file timestamp"
echo "=========================================="
ls -la public/build/assets/main-*.js 2>/dev/null || echo "main-*.js not found"

echo ""
echo "=========================================="
echo "4. Is __TENANT_APP injection in Blade?"
echo "=========================================="
if grep -q "__TENANT_APP" resources/views/app.blade.php 2>/dev/null; then
  echo "OK: app.blade.php contains __TENANT_APP"
else
  echo "FAIL: app.blade.php has no __TENANT_APP. Check deploy/code."
fi

echo ""
echo "=========================================="
echo "5. Check in browser (demo.escuelapresente.com)"
echo "=========================================="
echo "  - F12 -> Console, type: __ESCUELA_TENANT_APP"
echo "    -> true = tenant app loaded; undefined = central app (old menu)"
echo "  - View page source (Ctrl+U), search for '__TENANT_APP'"
echo "    -> if present, server is signaling tenant context"
echo "  - Hard refresh: Ctrl+Shift+R"
echo ""
