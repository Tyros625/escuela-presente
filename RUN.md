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

**.env에서 DB 비밀번호 확인** (WAMP root 비밀번호에 맞게):

```
DB_DATABASE=school
DB_USERNAME=root
DB_PASSWORD=여기에_WAMP_MySQL_비밀번호
```

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