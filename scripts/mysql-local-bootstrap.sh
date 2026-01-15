#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SOCK_FILE="$ROOT_DIR/storage/mysql.sock"

mysql --socket="$SOCK_FILE" --port=3307 -uroot <<'SQL'
CREATE DATABASE IF NOT EXISTS projetinho_landlord DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'projetinho'@'%' IDENTIFIED BY 'projetinho';
GRANT ALL PRIVILEGES ON *.* TO 'projetinho'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
SQL

echo "Banco 'projetinho_landlord' e usuário 'projetinho' prontos em 127.0.0.1:3307"

