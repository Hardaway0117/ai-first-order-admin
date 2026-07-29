# AI-First Order Admin

一個以「AI-First 開發流程」打造的 SaaS 訂單管理後台，作為求職作品集與面試展示專案。所有由 AI 產生的程式碼均經過人工審查與自動化測試驗證。

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

## 開發階段規劃

- [x] 第一階段：專案骨架（Breeze 登入、儀表板、導覽列、Docker、CI、測試）
- [ ] 第二階段：商品／客戶／訂單 CRUD 與統計數據
- [ ] 第三階段：REST API 與權限控管
