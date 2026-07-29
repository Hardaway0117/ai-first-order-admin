# AI-First Order Admin

Laravel 13 SaaS 訂單管理後台（求職作品集）。技術棧固定為 **Blade + Bootstrap 5**（勿引入 React／Tailwind）。頁面文字繁體中文，程式碼命名英文。

## 常用指令

```bash
php artisan test          # 跑全部測試（任何改動後必跑）
php artisan serve         # 本機開發伺服器（SQLite）
docker compose up -d      # Docker 環境（MySQL，http://localhost:8080）
npm run build             # 重新編譯前端資產（改 CSS/JS 後）
php artisan migrate       # 執行資料庫遷移
```

正式站部署（Vultr，程式在 /var/www/app）：

```bash
ssh root@45.76.223.190 "cd /var/www/app && git pull --ff-only && composer install --optimize-autoloader -n -q && npm ci --silent && npm run build --silent && php artisan migrate --force && php artisan config:cache -q && php artisan view:cache -q && chown -R www-data:www-data storage bootstrap/cache"
```

## 專案慣例

- **多語系**：所有 UI 文字用 `__('English key')`，翻譯同步維護 `lang/zh_TW.json` 與 `lang/zh_CN.json` 兩檔。
- **授權**：用 Policy（staff 對商品／客戶唯讀）；Web 與 API 共用同一套 FormRequest 驗證與 Policy。
- **訂單業務規則**：建單走 `app/Actions/CreateOrder.php`（交易＋鎖庫存＋價格快照）；狀態流轉走 `Order::transitionTo()` 狀態機。勿在控制器重複實作。
- **route:cache 不可用**（routes 內有 closure），部署只跑 config:cache 與 view:cache。
- 測試全綠才 commit；commit 訊息說明做了什麼與為什麼。
