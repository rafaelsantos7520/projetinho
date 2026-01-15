#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PID_FILE="$ROOT_DIR/storage/mysqld.pid"

if [ ! -f "$PID_FILE" ]; then
  echo "MySQL local não parece estar rodando (pid file não existe)."
  exit 0
fi

PID="$(cat "$PID_FILE")"

if kill -0 "$PID" 2>/dev/null; then
  kill "$PID"
fi

rm -f "$PID_FILE"
echo "MySQL local parado."

