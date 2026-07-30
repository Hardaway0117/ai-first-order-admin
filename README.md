# AI-First Order Admin

一個以「AI-First 開發流程」打造的 SaaS 訂單管理後台，作為求職作品集與面試展示專案。所有由 AI 產生的程式碼均經過人工審查與自動化測試驗證。

## 線上 Demo

- 網址：**https://45.76.223.190.sslip.io**
- 管理員帳號：`admin@example.com`／密碼：`password`（完整權限）
- 員工帳號：`staff@example.com`／密碼：`password`（可編輯商品與處理訂單；新增／刪除商品客戶、使用者管理為管理員專屬）
- 支援繁體中文、简体中文、English 三種語言切換

## 專案目的

- 展示 Laravel、MySQL、REST API、權限控管、自動化測試、Docker、Git、CI/CD 的實作能力。
- 展示「AI 輔助開發 + 人工驗證」的工程流程（詳見 [docs/AI_WORKFLOW.md](docs/AI_WORKFLOW.md)）。
- 第一階段完成專案骨架：登入系統、儀表板、導覽列與 placeholder 頁面；商品／客戶／訂單 CRUD 於後續階段實作。

## 技術棧

| 類別 | 技術 |
|------|------|
| 後端 | PHP 8.3、Laravel 13 |
| 認證 | Laravel Breeze（Blade 模式） |
| 前端 | Blade、Bootstrap 5、Vite |
| 資料庫 | MySQL 8（Docker）／SQLite（本機與測試） |
| 測試 | PHPUnit |
| 容器化 | Docker Compose（app + nginx + mysql） |
| CI/CD | GitHub Actions |

## 本機啟動方式

需求：PHP 8.3、Composer、Node.js 18+。

```bash
# 1. 安裝相依套件
composer install
npm install

# 2. 建立環境設定
cp .env.example .env        # Windows PowerShell: Copy-Item .env.example .env
php artisan key:generate

# 3. 建立資料庫並執行 migration（預設使用 SQLite）
php artisan migrate

# 4. 編譯前端資產
npm run build               # 開發時可改用 npm run dev

# 5. 啟動開發伺服器
php artisan serve
```

開啟 http://127.0.0.1:8000 ，註冊帳號後即可進入儀表板。

## Docker 啟動方式

需求：Docker Desktop（含 Docker Compose）。

```bash
# 1. 建立環境設定，並將 .env 中的 DB 區段改為 MySQL（參考 .env.example 內註解）
cp .env.example .env

# 2. 建置並啟動容器（app + nginx + mysql）
docker compose up -d --build

# 3. 在容器內安裝相依套件與初始化
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate

# 4. 前端資產（在本機執行）
npm install && npm run build
```

開啟 http://localhost:8080 。連接埠與資料庫帳密可透過 `.env` 的 `APP_PORT`、`DB_DATABASE`、`DB_USERNAME`、`DB_PASSWORD` 覆寫；預設值僅供開發使用，請勿將真實密碼提交至版本控制。

> 效能說明：容器內 OPcache 設定為 `validate_timestamps=0`（Windows bind mount 檔案存取慢，關閉重複檢查後回應時間自 2s 降至 0.1s）。因此**修改 PHP 程式後需執行 `docker compose restart app`** 才會生效；日常開發建議直接使用 `php artisan serve`。

## 測試方式

測試使用 SQLite in-memory 資料庫（見 `phpunit.xml`），不需額外設定：

```bash
php artisan test
```

第一階段測試涵蓋：未登入者無法進入儀表板、已登入者可進入儀表板，以及 Breeze 內建的認證流程測試。

## CI

每次 push 與 pull request 會由 GitHub Actions（[.github/workflows/ci.yml](.github/workflows/ci.yml)）自動執行：安裝相依套件 → 建立 `.env` 與 App Key → 編譯前端資產 → 執行 migration（SQLite）→ 執行 PHPUnit。

## AI-First 開發流程

本專案採用「需求拆解 → AI 產生初稿 → 人工審查 → 測試驗證 → 修正 → Commit」的循環，完整說明見 [docs/AI_WORKFLOW.md](docs/AI_WORKFLOW.md)。

## 主要功能

- 儀表板：商品／客戶／訂單／待處理訂單即時統計
- 商品管理：搜尋、分頁、上下架、庫存管理，有訂單紀錄的商品禁止刪除
- 客戶管理：搜尋、分頁，有訂單的客戶禁止刪除
- 訂單管理：動態多品項建單（交易鎖定庫存、快照商品名與單價）、狀態機流轉（待處理→處理中→已出貨→已完成／取消），取消自動歸還庫存
- 權限控管：admin／staff 兩種角色，以 Laravel Policy 實作（staff 可編輯商品、操作訂單；新增／刪除與使用者管理限 admin；後端授權 + 前端按鈕隱藏雙重防護）
- 使用者管理：admin 可新增／編輯／刪除使用者與指派角色，防自刪與自我降級
- REST API：Sanctum Token 認證的 `/api/v1` 端點（商品／客戶 CRUD、訂單建立與狀態流轉），與 Web 介面共用同一套授權與業務邏輯
- 多語系：繁體中文／简体中文／English
- 彩蛋：右下角的貓咪會盯著你的滑鼠 🐱

## REST API

所有端點皆在 `/api/v1` 之下，以 Sanctum Bearer Token 認證：

```bash
# 1. 取得 Token
curl -X POST https://45.76.223.190.sslip.io/api/v1/auth/token \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password","device_name":"curl"}'

# 2. 帶 Token 呼叫 API
curl https://45.76.223.190.sslip.io/api/v1/products \
  -H "Accept: application/json" -H "Authorization: Bearer <TOKEN>"
```

| Method | 端點 | 說明 |
|--------|------|------|
| POST | `/api/v1/auth/token` | 以帳密換取 API Token |
| GET | `/api/v1/user` | 目前使用者資訊 |
| GET／POST | `/api/v1/products` | 商品列表（`search`、`per_page`）／新增（admin） |
| GET／PUT／DELETE | `/api/v1/products/{id}` | 商品明細／更新（admin）／刪除（admin，有訂單回 409） |
| GET／POST | `/api/v1/customers` | 客戶列表／新增（admin） |
| GET／PUT／DELETE | `/api/v1/customers/{id}` | 客戶明細／更新（admin）／刪除（admin，有訂單回 409） |
| GET／POST | `/api/v1/orders` | 訂單列表（`status`、`search`）／建立訂單（含品項） |
| GET | `/api/v1/orders/{id}` | 訂單明細（含品項與客戶） |
| PATCH | `/api/v1/orders/{id}/status` | 狀態流轉（非法轉換回 422） |

## 開發階段規劃

- [x] 第一階段：專案骨架（Breeze 登入、儀表板、導覽列、Docker、CI、測試）
- [x] 第二階段：商品／客戶／訂單 CRUD 與統計數據、多語系、正式部署
- [x] 第三階段：REST API（Sanctum）與角色權限控管（Policy）
