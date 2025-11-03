#!/bin/bash
# WP-CLI Cache Commands Test Script

set -e

echo "======================================"
echo "WP-CLI Cache Commands Test Suite"
echo "======================================"
echo ""

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0

test_result() {
    TOTAL_TESTS=$((TOTAL_TESTS + 1))
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ PASS${NC}: $1"
        PASSED_TESTS=$((PASSED_TESTS + 1))
    else
        echo -e "${RED}✗ FAIL${NC}: $1"
        FAILED_TESTS=$((FAILED_TESTS + 1))
    fi
}

echo "Starting WP-CLI cache command tests..."
echo ""

# Test 1: Get cache stats
echo "Test 1: wp atables cache stats"
wp atables cache stats --format=table > /dev/null 2>&1
test_result "Get cache statistics"

# Test 2: Get cache stats as JSON
echo "Test 2: wp atables cache stats --format=json"
STATS_JSON=$(wp atables cache stats --format=json 2>&1)
echo "$STATS_JSON" | jq . > /dev/null 2>&1
test_result "Get cache stats (JSON format)"

# Test 3: List cached tables
echo "Test 3: wp atables cache list"
wp atables cache list --format=table > /dev/null 2>&1
test_result "List cached tables"

# Test 4: Clear all cache
echo "Test 4: wp atables cache clear --yes"
wp atables cache clear --yes > /dev/null 2>&1
test_result "Clear all cache"

# Test 5: Verify cache cleared
echo "Test 5: Verify cache cleared"
CACHE_COUNT=$(wp atables cache list --format=count 2>&1 || echo "0")
echo "  Cached tables count: $CACHE_COUNT"
[ "$CACHE_COUNT" -eq 0 ] || [ "$CACHE_COUNT" = "0" ]
test_result "Verify cache cleared"

# Summary
echo ""
echo "======================================"
echo "Test Summary"
echo "======================================"
echo "Total Tests:  $TOTAL_TESTS"
echo -e "${GREEN}Passed:${NC}       $PASSED_TESTS"
echo -e "${RED}Failed:${NC}       $FAILED_TESTS"
echo "======================================"

if [ $FAILED_TESTS -eq 0 ]; then
    echo -e "${GREEN}All tests passed!${NC}"
    exit 0
else
    echo -e "${RED}Some tests failed!${NC}"
    exit 1
fi
