#!/bin/bash
# Comprehensive Test Runner
# Executes all test suites and generates final report

set +e  # Don't exit on error, we want to run all tests

# Navigate to WordPress root
cd "$(dirname "$0")/../../.." || exit 1

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Result counters
TOTAL_SUITES=0
PASSED_SUITES=0
FAILED_SUITES=0

echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║  A-Tables & Charts - Master Test Runner                   ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo -e "${BLUE}Starting comprehensive test execution...${NC}"
echo ""

# Test Suite 1: Unit Tests (PHP)
echo "┌────────────────────────────────────────────────────────────┐"
echo "│  TEST SUITE 1: Unit Tests (PHP)                           │"
echo "└────────────────────────────────────────────────────────────┘"
TOTAL_SUITES=$((TOTAL_SUITES + 1))
if php wp-content/plugins/a-tables-charts/tests/run-all-tests.php; then
    PASSED_SUITES=$((PASSED_SUITES + 1))
    echo -e "${GREEN}✓ Unit Tests: PASSED${NC}"
else
    FAILED_SUITES=$((FAILED_SUITES + 1))
    echo -e "${RED}✗ Unit Tests: FAILED${NC}"
fi
echo ""

# Test Suite 2: Integration Tests
echo "┌────────────────────────────────────────────────────────────┐"
echo "│  TEST SUITE 2: Integration Tests                          │"
echo "└────────────────────────────────────────────────────────────┘"
TOTAL_SUITES=$((TOTAL_SUITES + 1))
if php wp-content/plugins/a-tables-charts/tests/integration/test-scheduled-refresh.php; then
    PASSED_SUITES=$((PASSED_SUITES + 1))
    echo -e "${GREEN}✓ Integration Tests: PASSED${NC}"
else
    FAILED_SUITES=$((FAILED_SUITES + 1))
    echo -e "${RED}✗ Integration Tests: FAILED${NC}"
fi
echo ""

# Test Suite 3: WP-CLI Table Commands
echo "┌────────────────────────────────────────────────────────────┐"
echo "│  TEST SUITE 3: WP-CLI Table Commands                      │"
echo "└────────────────────────────────────────────────────────────┘"
TOTAL_SUITES=$((TOTAL_SUITES + 1))
if bash wp-content/plugins/a-tables-charts/tests/cli/test-table-commands.sh; then
    PASSED_SUITES=$((PASSED_SUITES + 1))
    echo -e "${GREEN}✓ WP-CLI Table Commands: PASSED${NC}"
else
    FAILED_SUITES=$((FAILED_SUITES + 1))
    echo -e "${RED}✗ WP-CLI Table Commands: FAILED${NC}"
fi
echo ""

# Test Suite 4: WP-CLI Cache Commands
echo "┌────────────────────────────────────────────────────────────┐"
echo "│  TEST SUITE 4: WP-CLI Cache Commands                      │"
echo "└────────────────────────────────────────────────────────────┘"
TOTAL_SUITES=$((TOTAL_SUITES + 1))
if bash wp-content/plugins/a-tables-charts/tests/cli/test-cache-commands.sh; then
    PASSED_SUITES=$((PASSED_SUITES + 1))
    echo -e "${GREEN}✓ WP-CLI Cache Commands: PASSED${NC}"
else
    FAILED_SUITES=$((FAILED_SUITES + 1))
    echo -e "${RED}✗ WP-CLI Cache Commands: FAILED${NC}"
fi
echo ""

# Final Summary
echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║  FINAL TEST SUMMARY                                        ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo "  Total Test Suites:  $TOTAL_SUITES"
echo -e "  ${GREEN}Passed Suites:${NC}      $PASSED_SUITES"
echo -e "  ${RED}Failed Suites:${NC}      $FAILED_SUITES"
echo ""

if [ $FAILED_SUITES -eq 0 ]; then
    echo -e "${GREEN}"
    echo "╔════════════════════════════════════════════════════════════╗"
    echo "║  ✓ ALL TEST SUITES PASSED!                                ║"
    echo "╚════════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
    echo ""
    exit 0
else
    echo -e "${RED}"
    echo "╔════════════════════════════════════════════════════════════╗"
    echo "║  ✗ SOME TEST SUITES FAILED                                ║"
    echo "╚════════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
    echo ""
    exit 1
fi
