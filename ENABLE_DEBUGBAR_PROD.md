# Como habilitar o Laravel Debugbar em Produção

Para ver o Debugbar em produção, siga estes passos:

1. **Atualize o código**:
   ```bash
   git pull origin main
   ```

2. **Instale as dependências** (agora o debugbar é uma dependência de produção também):
   ```bash
   composer install --no-dev
   # OU apenas
   composer install
   ```
   *Nota: O debugbar foi movido de `require-dev` para `require` no `composer.json`.*

3. **Limpe e recrie o cache de configuração**:
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```
   *Este passo é CRUCIAL. Se você não rodar isso, o Laravel continuará usando a configuração antiga onde o Debugbar não estava instalado.*

4. **Verifique o .env**:
   Certifique-se que:
   ```env
   APP_DEBUG=true
   DEBUGBAR_ENABLED=true  # Opcional, mas força a habilitação
   ```

5. **Limpe o cache de rotas e views** (por precaução):
   ```bash
   php artisan route:clear
   php artisan view:clear
   ```

## Atenção
Deixar `APP_DEBUG=true` em produção expõe informações sensíveis em caso de erro. Use apenas temporariamente para debugging e depois desligue.
