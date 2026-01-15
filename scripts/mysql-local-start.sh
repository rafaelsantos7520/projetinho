#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DATA_DIR="$ROOT_DIR/storage/mysql-data"
TMP_DIR="$ROOT_DIR/storage/mysql-tmp"
SOCK_FILE="$ROOT_DIR/storage/mysql.sock"
PID_FILE="$ROOT_DIR/storage/mysqld.pid"
LOG_FILE="$ROOT_DIR/storage/logs/mysqld.log"
INIT_LOG_FILE="$ROOT_DIR/storage/logs/mysqld-init.log"

mkdir -p "$DATA_DIR" "$TMP_DIR" "$ROOT_DIR/storage/logs"

if [ ! -f "$DATA_DIR/auto.cnf" ]; then
  mysqld --initialize-insecure \
    --datadir="$DATA_DIR" \
    --tmpdir="$TMP_DIR" \
    --log-error="$INIT_LOG_FILE"
fi

if [ -f "$PID_FILE" ] && kill -0 "$(cat "$PID_FILE")" 2>/dev/null; then
  echo "MySQL já está rodando (pid $(cat "$PID_FILE"))"
  exit 0
fi

nohup mysqld \
  --datadir="$DATA_DIR" \
  --socket="$SOCK_FILE" \
  --port=3307 \
  --bind-address=127.0.0.1 \
  --pid-file="$PID_FILE" \
  --tmpdir="$TMP_DIR" \
  --log-error="$LOG_FILE" \
  --mysqlx=0 \
  >/dev/null 2>&1 &

for _ in $(seq 1 30); do
  if mysqladmin --socket="$SOCK_FILE" --port=3307 -uroot ping >/dev/null 2>&1; then
    echo "MySQL local rodando em 127.0.0.1:3307 (socket $SOCK_FILE)"
    exit 0
  fi
  sleep 0.2
done

echo "Falha ao subir MySQL local. Veja logs: $LOG_FILE"
exit 1

