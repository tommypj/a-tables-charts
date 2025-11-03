#!/bin/bash
# WP-CLI Table Commands Test Script
#
# Tests all table-related WP-CLI commands

set -e  # Exit on error

echo "======================================"
echo "WP-CLI Table Commands Test Suite"
echo "======================================"
echo ""

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test counter
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0

# Test result function
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

# Start tests
echo "Starting WP-CLI table command tests..."
echo ""

# Test 1: List tables
echo "Test 1: wp atables table list"
wp atables table list --format=table > /dev/null 2>&1
test_result "List tables (table format)"

# Test 2: List tables as JSON
echo "Test 2: wp atables table list --format=json"
TABLES_JSON=$(wp atables table list --format=json 2>&1)
echo "$TABLES_JSON" | jq . > /dev/null 2>&1
test_result "List tables (JSON format)"

# Test 3: Get table count
echo "Test 3: wp atables table list --format=count"
TABLE_COUNT=$(wp atables table list --format=count 2>&1)
echo "  Table count: $TABLE_COUNT"
[ -n "$TABLE_COUNT" ]
test_result "Get table count"

# Test 4: Create table
echo "Test 4: wp atables table create"
TABLE_ID=$(wp atables table create "CLI Test Table" --description="Created via WP-CLI test" --porcelain 2>&1)
echo "  Created table ID: $TABLE_ID"
[ -n "$TABLE_ID" ] && [ "$TABLE_ID" -gt 0 ]
test_result "Create table"

# Test 5: Get table details
if [ -n "$TABLE_ID" ]; then
    echo "Test 5: wp atables table get $TABLE_ID"
    wp atables table get "$TABLE_ID" --format=json > /dev/null 2>&1
    test_result "Get table details"
else
    echo -e "${YELLOW}⊘ SKIP${NC}: Test 5 (no table ID)"
fi

# Test 6: Update table
if [ -n "$TABLE_ID" ]; then
    echo "Test 6: wp atables table update $TABLE_ID"
    wp atables table update "$TABLE_ID" --title="Updated CLI Test Table" --description="Updated via WP-CLI" > /dev/null 2>&1
    test_result "Update table"
else
    echo -e "${YELLOW}⊘ SKIP${NC}: Test 6 (no table ID)"
fi

# Test 7: Duplicate table
if [ -n "$TABLE_ID" ]; then
    echo "Test 7: wp atables table duplicate $TABLE_ID"
    DUPLICATE_ID=$(wp atables table duplicate "$TABLE_ID" --title="Duplicated CLI Test Table" --porcelain 2>&1)
    echo "  Duplicated table ID: $DUPLICATE_ID"
    [ -n "$DUPLICATE_ID" ] && [ "$DUPLICATE_ID" -gt 0 ]
    test_result "Duplicate table"
else
    echo -e "${YELLOW}⊘ SKIP${NC}: Test 7 (no table ID)"
fi

# Test 8: List tables with search
echo "Test 8: wp atables table list --search=\"CLI Test\""
SEARCH_RESULTS=$(wp atables table list --search="CLI Test" --format=count 2>&1)
echo "  Search results count: $SEARCH_RESULTS"
[ -n "$SEARCH_RESULTS" ] && [ "$SEARCH_RESULTS" -gt 0 ]
test_result "Search tables"

# Test 9: List tables with status filter
echo "Test 9: wp atables table list --status=active"
wp atables table list --status=active --format=table > /dev/null 2>&1
test_result "Filter tables by status"

# Test 10: Get table with specific fields
if [ -n "$TABLE_ID" ]; then
    echo "Test 10: wp atables table get $TABLE_ID --fields=id,title"
    wp atables table get "$TABLE_ID" --fields=id,title --format=json > /dev/null 2>&1
    test_result "Get table with specific fields"
else
    echo -e "${YELLOW}⊘ SKIP${NC}: Test 10 (no table ID)"
fi

# Test 11: Delete duplicate table
if [ -n "$DUPLICATE_ID" ]; then
    echo "Test 11: wp atables table delete $DUPLICATE_ID --yes"
    wp atables table delete "$DUPLICATE_ID" --yes > /dev/null 2>&1
    test_result "Delete duplicate table"
else
    echo -e "${YELLOW}⊘ SKIP${NC}: Test 11 (no duplicate ID)"
fi

# Test 12: Delete original test table
if [ -n "$TABLE_ID" ]; then
    echo "Test 12: wp atables table delete $TABLE_ID --yes"
    wp atables table delete "$TABLE_ID" --yes > /dev/null 2>&1
    test_result "Delete original test table"
else
    echo -e "${YELLOW}⊘ SKIP${NC}: Test 12 (no table ID)"
fi

# Test 13: Verify deletion
if [ -n "$TABLE_ID" ]; then
    echo "Test 13: Verify table deletion"
    ! wp atables table get "$TABLE_ID" > /dev/null 2>&1
    test_result "Verify table deleted"
else
    echo -e "${YELLOW}⊘ SKIP${NC}: Test 13 (no table ID)"
fi

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
