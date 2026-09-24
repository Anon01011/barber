#!/usr/bin/env bash

# ==============================================================================
# Salon CMS Deployment Script
# ==============================================================================

set -e

# ------------------------------------------------------------------------------
# Colors
# ------------------------------------------------------------------------------
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
BOLD='\033[1m'
NC='\033[0m'

PHP_CMD=${PHP_BINARY:-php}

# ------------------------------------------------------------------------------
# Helper Functions (FIXED SYNTAX)
# ------------------------------------------------------------------------------
print_line() {
    echo -e "${CYAN}----------------------------------------------------------------------${NC}"
}

info() {
    echo -e "${BLUE}${BOLD}[INFO]${NC} $1"
}

success() {
    echo -e "${GREEN}${BOLD}[SUCCESS]${NC} $1"
}

warning() {
    echo -e "${YELLOW}${BOLD}[WARNING]${NC} $1"
}

error() {
    echo -e "${RED}${BOLD}[ERROR]${NC} $1"
}

step() {
    echo -e "\n${MAGENTA}${BOLD}==>${NC} ${BOLD}$1${NC}"
}

handle_error() {
    local line_no=$1
    local command=$2
    print_line
    error "Deployment failed at line $line_no: command '$command'"
    if [ -f "storage/framework/down" ]; then
        warning "Attempting to bring application back up..."
        $PHP_CMD artisan up || true
    fi
    print_line
    exit 1
}

trap 'handle_error ${LINENO} "$BASH_COMMAND"' ERR

# ------------------------------------------------------------------------------
# Pre-flight Checks
# ------------------------------------------------------------------------------
for cmd in git composer npm; do
    if ! command -v $cmd &>/dev/null; then
        error "$cmd is not installed or not in PATH"
        exit 1
    fi
done

# ------------------------------------------------------------------------------
# Banner
# ------------------------------------------------------------------------------
echo -e "${CYAN}"
echo "  _  _________ _____  _    _   _ "
echo " | |/ /  ____|_   _|/ \  | \ | |"
echo " | ' /| |__    | | / _ \ |  \| |"
echo " |  < |  __|   | |/ ___ \| |\  |"
echo " |_|\_\|______ |_/_/   \_\_| \_|"
echo "      PREMIUM DEPLOYMENT ENGINE  "
echo -e "${NC}"
print_line

info "Deployment started at $(date)"
info "Using PHP binary: $($PHP_CMD -v | head -n 1)"

# ------------------------------------------------------------------------------
# Confirmation
# ------------------------------------------------------------------------------
CONFIRM=true
while getopts "y" opt; do
  case $opt in
    y) CONFIRM=false ;;
  esac
done

if $CONFIRM; then
    read -p "Are you sure you want to deploy to production? [y/N] " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        info "Deployment cancelled."
        exit 0
    fi
fi

# ------------------------------------------------------------------------------
# Deployment Steps
# ------------------------------------------------------------------------------

step "Step 1/7: Maintenance mode"
$PHP_CMD artisan down --render="errors::503" || true
success "Maintenance mode enabled"

step "Step 2/7: Pulling latest code"
git fetch origin master
git reset --hard origin/master
success "Code updated"

step "Step 3/7: Installing dependencies"
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci
npm run build
success "Dependencies installed"

step "Step 4/7: Running migrations"
$PHP_CMD artisan migrate --force
success "Database migrated"

step "Step 5/7: Optimizing caches"
$PHP_CMD artisan optimize:clear
$PHP_CMD artisan config:cache
$PHP_CMD artisan route:cache
$PHP_CMD artisan view:cache
$PHP_CMD artisan event:cache
success "Caches optimized"

step "Step 6/7: Restarting queue"
if $PHP_CMD artisan list | grep -q "queue:restart"; then
    $PHP_CMD artisan queue:restart
    success "Queue restarted"
else
    warning "Queue restart not available"
fi

step "Step 7/7: Bringing application up"
$PHP_CMD artisan up
success "Application LIVE"

print_line
success "Deployment finished successfully at $(date)"
print_line
