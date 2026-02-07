# Escuela Presente 로컬 실행 방법

## 1. DB 가져오기 (WAMP MySQL에 넣기)

프로젝트 폴더에서 실행 (Git Bash 등).

**DB 생성 (최초 1회):**

```bash
"D:/wamp64/bin/mysql/mysql8.0.31/bin/mysql.exe" -u root -p -e "CREATE DATABASE IF NOT EXISTS school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**SQL 파일 넣기** (원하는 파일 하나 선택):

```bash
# 메인 DB
"D:/wamp64/bin/mysql/mysql8.0.31/bin/mysql.exe" -u root -p school < DB/escuela_presente.sql

# 또는 데모
"D:/wamp64/bin/mysql/mysql8.0.31/bin/mysql.exe" -u root -p school < DB/escuela_presente_demo.sql

# 또는 secundaria87
"D:/wamp64/bin/mysql/mysql8.0.31/bin/mysql.exe" -u root -p school < DB/escuela_presente_secundaria87.sql
```

> SQL 안에 이미 `CREATE DATABASE` / `USE 다른DB이름` 이 있으면, 위에서 `school` 대신 그 DB 이름을 쓰거나, `school` 없이 `mysql -u root -p < DB/파일.sql` 로 실행한 뒤 `.env`의 `DB_DATABASE`를 그 이름으로 맞추세요.

---

## 2. 환경 설정

**.env 파일이 없으면:**

```bash
cp .env.example .env
php artisan key:generate
```

**.env에서 확인할 항목:**

```
DB_DATABASE=school
DB_USERNAME=root
DB_PASSWORD=여기에_WAMP_MySQL_비밀번호

# 로컬에서 등록 시 자동 처리(DB 생성, 시드, 관리자)를 위해 반드시 설정:
APP_URL_BASE=localhost
APP_URL=http://localhost
```

`APP_URL_BASE=localhost` 이어야 등록 시 도메인이 `hikaru.localhost` 형태로 저장됩니다.

---

## 3. PHP 의존성 (최초 1회)

```bash
composer install
```

---

## 4. 프론트엔드 (최초 1회 + 개발 시)

```bash
npm install
npm run dev
```

`npm run dev`는 **켜 둔 채** 두세요 (Vite 개발 서버).

### 수정되는 대로 결과 보기 (빌드 없이)

- **개발 중에는 `npm run build`를 하지 마세요.** 대신 **`npm run dev`를 한 번 실행한 뒤 그대로 켜 두세요.**
- 터미널 1: `npm run dev` → Vite 개발 서버 (HMR)
- 터미널 2: `php artisan serve` → Laravel 서버
- 브라우저에서 http://127.0.0.1:8000 (또는 테넌트 주소) 접속
- 이제 **Vue/JS/CSS 파일을 저장하면 브라우저가 자동으로 갱신**됩니다. (전체 새로고침 없이 컴포넌트만 바뀌는 경우도 많습니다.)
- 작업이 끝나고 배포할 때만 `npm run build` 한 번 실행하면 됩니다.

**한 번에 두 서버 띄우기 (선택):** 터미널 하나에서 Vite + Laravel 둘 다 실행하려면:

```bash
npm install   # 최초 1회, concurrently 설치됨
npm run dev:all
```

실행 후 브라우저에서 http://127.0.0.1:8000 접속하면 됩니다.

---

## 5. Laravel 서버 실행

**새 터미널**에서:

```bash
cd /e/escuela-presente
php artisan serve
```

브라우저에서 **[http://127.0.0.1:8000](http://127.0.0.1:8000)** 접속.

---

## 한 번에 복사해서 쓸 수 있는 순서

```bash
# 1) DB 넣기 (비밀번호 입력)
"D:/wamp64/bin/mysql/mysql8.0.31/bin/mysql.exe" -u root -p -e "CREATE DATABASE IF NOT EXISTS school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
"D:/wamp64/bin/mysql/mysql8.0.31/bin/mysql.exe" -u root -p school < DB/escuela_presente.sql

# 2) 환경 + 키
cp .env.example .env
php artisan key:generate

# 3) 의존성
composer install
npm install

# 4) 프론트 (이 터미널은 계속 켜 두기)
npm run dev
```

**다른 터미널에서:**

```bash
cd /e/escuela-presente
php artisan serve
```

이후 [http://127.0.0.1:8000](http://127.0.0.1:8000) 접속.

---

## 테넌트(학교) 주소로 접속하기 — secundaria87.localhost:8000

**학교 전용 화면**을 보려면 `secundaria87.localhost:8000` 같은 **서브도메인**으로 들어가야 합니다.

### 1. hosts 파일에 도메인 추가

`secundaria87.localhost`가 이 PC를 가리키도록 설정합니다.

**Windows** (관리자 권한으로 메모장 등으로 열기):

- 파일: `C:\Windows\System32\drivers\etc\hosts`
- 아래 한 줄 추가 후 저장:

```
127.0.0.1   secundaria87.localhost
```

**Mac/Linux:**

```bash
echo "127.0.0.1 secundaria87.localhost" | sudo tee -a /etc/hosts
```

### 2. 중앙 DB에 테넌트·도메인 등록

테넌트 DB 이름은 `.env`의 `DB_DATABASE_PREFIX` + 테넌트 id 입니다.  
예: `DB_DATABASE_PREFIX=escuelapresente_` 이면 → `escuelapresente_secundaria87`

**방법 A — 중앙 대시보드에서 등록 (권장)**

1. [http://127.0.0.1:8000](http://127.0.0.1:8000) 접속 → 로그인 (중앙 관리자).
2. 테넌트(학교) 추가 메뉴에서 **도메인**에 `secundaria87.localhost` 로 등록.  
   → 테넌트 id는 `secundaria87` 로 생성되고, 테넌트 전용 DB가 자동 생성됩니다.

**방법 B — 이미 테넌트 DB가 있을 때 (수동 등록)**

중앙 DB(`school`)에 이미 테넌트용 DB(`escuelapresente_secundaria87`)가 있고, **tenants / domains** 만 넣으면 될 때:

```sql
-- 중앙 DB(school)에서 실행
INSERT INTO tenants (id, created_at, updated_at, data) VALUES
('secundaria87', NOW(), NOW(), '{}');

INSERT INTO domains (domain, tenant_id, created_at, updated_at) VALUES
('secundaria87.localhost', 'secundaria87', NOW(), NOW());
```

테넌트 DB가 아직 없으면 먼저 생성 후 마이그레이션 또는 덤프 복원:

```bash
# MySQL에서 테넌트 DB 생성 (이름은 .env의 DB_DATABASE_PREFIX + secundaria87)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS escuelapresente_secundaria87 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 기존 덤프가 테넌트용이면 복원
mysql -u root -p escuelapresente_secundaria87 < DB/escuela_presente_secundaria87.sql
```

### 3. 접속 주소 (로컬은 http 사용)

- **중앙(랜딩/관리):** [http://127.0.0.1:8000](http://127.0.0.1:8000) 또는 [http://localhost:8000](http://localhost:8000)
- **학교(테넌트):** [http://secundaria87.localhost:8000](http://secundaria87.localhost:8000)

로컬에서는 **https** 대신 **http** 로 접속하세요. (SSL 없으면 https 는 오류 납니다.)

### 4. 정리

| 단계 | 내용 |
|------|------|
| 1 | hosts 에 `127.0.0.1 secundaria87.localhost` 추가 |
| 2 | 중앙 DB에 테넌트 id `secundaria87` + 도메인 `secundaria87.localhost` 등록 |
| 3 | 테넌트 DB `escuelapresente_secundaria87` 존재 여부 확인 (없으면 생성·마이그레이션 또는 덤프 복원) |
| 4 | `php artisan serve` + `npm run dev` 실행 후 **http://secundaria87.localhost:8000** 접속 |

### 이미 관리자 패널에 도메인이 등록되어 있는데 접속이 안 될 때

테넌트 식별은 **호스트만** 사용합니다. DB에 `https://secundaria87.localhost:8000` 처럼 **프로토콜·포트**가 들어 있으면 매칭되지 않습니다.

**DB를 직접 건드리지 않고, 프로젝트 폴더에서 아래 명령어만 실행하세요:**

```bash
php artisan tenants:fix-domain secundaria87
```

- `secundaria87` 대신 다른 학교(테넌트) ID를 쓰면 그 테넌트 도메인만 수정됩니다.
- 성공하면 "이전 / 이후" 값이 출력되고, **http://secundaria87.localhost:8000** 으로 다시 접속하면 됩니다.

앞으로 새로 등록하는 도메인은 저장 시 자동으로 호스트만 저장되도록 되어 있습니다.

### "Database escuela_presente_secundaria87 does not exist" 오류가 날 때

도메인은 등록됐지만 **테넌트 전용 DB**가 없을 때 나는 오류입니다. 아래 명령으로 DB를 만들고 마이그레이션까지 한 번에 실행하세요.

```bash
php artisan tenants:create-database secundaria87
```

- 테넌트 DB가 이미 있으면 마이그레이션만 다시 실행됩니다.
- 완료 후 **http://secundaria87.localhost:8000** (또는 등록한 도메인)으로 접속하면 됩니다.

### 테넌트(학교) 로그인 — 비밀번호를 모를 때

학교 주소(예: http://secundaria87.localhost:8000)의 로그인 화면에서는 **해당 학교 전용 계정**으로 로그인합니다.

- **이메일:** 관리자 패널에 등록한 그 학교의 이메일 (예: `secundaria87@gmail.com`).
- **비밀번호:** 관리자 패널에서 그 학교(고객)를 **추가할 때 입력한 비밀번호**입니다.

DB만 만들고 시드를 안 돌린 경우(예: `tenants:create-database`만 실행한 경우)에는 **테넌트 DB에 사용자가 없을 수 있습니다.** 그때는 아래를 실행한 뒤, 위 이메일/비밀번호로 로그인하세요.

```bash
php artisan tenants:create-admin secundaria87
```

이 명령은 해당 테넌트 DB에 역할·권한을 넣고, **중앙에 등록된 이메일/비밀번호**로 관리자 계정을 하나 만듭니다. 로그인 시 사용하는 비밀번호는 **고객(학교) 등록 시 입력한 그 비밀번호**입니다.