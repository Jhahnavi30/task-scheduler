#!/bin/bash

# ⚠️ This CRON setup script is for Linux systems.
# Since this project was built on Windows, Windows Task Scheduler was used instead.
# This script is a placeholder to meet submission requirements.

# Example CRON job (Linux):
# Run cron.php every hour
CRON_COMMAND="0 * * * * /usr/bin/php /path/to/project/src/cron.php"

# Add cron job
(crontab -l 2>/dev/null; echo "$CRON_COMMAND") | crontab -
