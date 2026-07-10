#!/bin/bash

# 1. Run Flutter tests with coverage
echo "============================================="
echo "1. RUNNING FLUTTER UNIT TESTS WITH COVERAGE..."
echo "============================================="
cd foundit_app
flutter test --coverage

if [ -f "coverage/lcov.info" ]; then
    echo "Generating Flutter frontend HTML coverage..."
    genhtml coverage/lcov.info -o ../metrics/frontend
    
    cd ..
    mkdir -p metrics
    cp foundit_app/coverage/lcov.info metrics/lcov.info
    
    echo "Updating dashboard index.html..."
    python3 metrics/generate_pretty_coverage.py
else
    echo "Error: Flutter coverage/lcov.info was not generated."
    cd ..
fi

# 2. Run Laravel Backend tests
echo ""
echo "============================================="
echo "2. RUNNING LARAVEL BACKEND API TESTS..."
echo "============================================="
cd foundit_api

# Run standard PHPUnit tests
php artisan test

# Try running with coverage if driver is available
echo ""
echo "Checking PHP Code Coverage Driver..."
if php -m | grep -q -E "xdebug|pcov"; then
    echo "Coverage driver found. Generating Laravel coverage report..."
    XDEBUG_MODE=coverage php artisan test --coverage-clover=../metrics/clover.xml --coverage-html=../metrics/backend
    echo "Laravel coverage reports generated at:"
    echo " - metrics/clover.xml (XML data)"
    echo " - metrics/backend/index.html (HTML report)"
else
    echo "PHP Coverage Driver (Xdebug/PCOV) not found in CLI. Skipped generating backend HTML coverage."
    echo "Tip: Anda dapat mengambil screenshot dari output 'php artisan test' di atas sebagai bukti kelulusan uji backend."
fi

cd ..

# 3. Update Unified HTML Dashboard (requires both lcov.info and clover.xml)
echo ""
echo "============================================="
echo "3. UPDATING UNIFIED COVERAGE DASHBOARD..."
echo "============================================="
python3 metrics/generate_pretty_coverage.py

echo "============================================="
echo "ALL TESTS & REPORTS COMPLETED!"
echo "============================================="
