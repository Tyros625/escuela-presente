# Escuela Presente — 프로젝트 전체 구조 및 기능 설명

> **Escuela Presente**는 **멀티테넌트(Multi-tenant) 학교 관리 시스템**입니다.  
> 한 개의 서비스로 여러 학교(테넌트)를 도메인별로 분리해 운영합니다.

---

## 1. 한눈에 보는 구조

```
┌─────────────────────────────────────────────────────────────────┐
│                    사용자가 접속하는 URL                          │
├─────────────────────────────┬───────────────────────────────────┤
│  메인 도메인 (localhost 등)   │  서브도메인 (학교별, 예: escuela1.xxx.com) │
│  → "중앙(Central)" 앱        │  → "테넌트(Tenant)" 앱 = 각 학교 전용     │
├─────────────────────────────┼───────────────────────────────────┤
│  • 랜딩 페이지               │  • 해당 학교 전용 대시보드                 │
│  • 중앙 로그인               │  • 학생/교사/출결/급식/결제/설정 등        │
│  • 테넌트(학교) 목록 관리     │  • 권한에 따른 메뉴 표시                  │
│  • 대시보드                  │  • 학생 등록(공개), 결제 완료/실패 페이지  │
└─────────────────────────────┴───────────────────────────────────┘
```

- **백엔드**: Laravel 10, PHP 8.1+  
- **프론트엔드**: Vue 3 + Vite + Pinia + Vue Router  
- **멀티테넌시**: `stancl/tenancy` — 도메인별로 **DB 분리** (학교마다 별도 DB)

---

## 2. 디렉터리 구조 (핵심만)

```
escuela-presente/
├── app/
│   ├── Http/Controllers/
│   │   ├── CentralLoginController, TenantController ...  ← 중앙용
│   │   └── API/  ← 테넌트용 API (학생, 출결, 결제, 설정 등)
│   ├── Models/
│   │   ├── User.php           ← 중앙 사용자
│   │   └── Tenants/           ← 학교(테넌트) 전용 모델
│   │       ├── Student, Teacher, Assist, Payment, Grade, Section ...
│   │       └── SchoolCycle, AcademicGroup, Incident, IncidentReport ...
│   ├── Providers/
│   │   └── TenancyServiceProvider.php  ← 테넌트 부트스트랩
│   └── Jobs/, Mail/, Notifications/    ← 이메일·알림 등
├── config/
│   └── tenancy.php            ← 테넌트 설정 (도메인, DB 접두사 등)
├── routes/
│   ├── web.php               ← 중앙 웹 (SPA 진입점)
│   ├── api.php               ← 중앙 API (로그인, 테넌트 CRUD)
│   └── tenant.php            ← 테넌트 전용 라우트 (출결, 결제, 학생, 설정 등)
├── resources/
│   └── js/
│       ├── main.js           ← 도메인에 따라 central vs tenant 라우터 선택
│       ├── router/
│       │   ├── central.js    ← 중앙: 랜딩, 로그인, 대시보드, 테넌트 관리
│       │   └── tenant.js     ← 학교: 대시보드, 학생/교사/출결/급식/결제/설정
│       ├── services/
│       │   └── host.js       ← isSubdomain() → 서브도메인 여부 판별
│       ├── views/           ← Vue 페이지 (central/, tenants/, auth/, roles/ 등)
│       ├── components/      ← MenuView, BaseBlock, ModalChangePassword 등
│       └── stores/           ← Pinia (central, user, template, student)
├── database/
│   ├── migrations/           ← 중앙 DB + 테넌트 DB 마이그레이션
│   └── seeders/              ← RolesSeeder, TenantsSeeder, AcademicDataSeeder 등
└── DB/                        ← 예시 SQL 덤프 (escuela_presente*.sql)
```

---

## 3. 동작 방식 (흐름)

### 3.1 사용자가 접속했을 때

1. **브라우저 URL**을 보고 `host.js`의 `isSubdomain()`로 판단합니다.  
   - `localhost`, `127.0.0.1` → **중앙(Central)**  
   - 그 외 서브도메인(예: `escuela1.ejemplo.com`) → **테넌트(Tenant)**

2. **main.js**에서:
   - 중앙이면 → `central` 라우터 사용 (랜딩, 로그인, 테넌트 관리)
   - 테넌트면 → `tenant` 라우터 사용 (학교 전용 화면)

3. **백엔드**:
   - 테넌트 도메인 요청은 `tenant.php`로 가고,  
     `InitializeTenancyByDomain` 미들웨어가 **현재 도메인에 해당하는 DB**로 자동 연결합니다.  
   - 그래서 학교 A와 학교 B의 데이터가 서로 섞이지 않습니다.

### 3.2 중앙(Central) vs 테넌트(Tenant)

| 구분 | 중앙 (Central) | 테넌트 (Tenant, 각 학교) |
|------|----------------|---------------------------|
| **역할** | 플랫폼 관리, 학교(테넌트) 생성/삭제 | 한 학교의 일상 업무 |
| **DB** | 중앙 DB 1개 | 학교마다 DB 1개 (prefix 등으로 구분) |
| **주요 화면** | 랜딩, 로그인, 대시보드, 테넌트 목록 | 대시보드, 학생/교사/출결/급식/결제/설정 |
| **인증** | Central API (`api.php`) | 테넌트 API (`tenant.php`) |

---

## 4. 주요 기능 (테넌트 = 학교 쪽)

학교(서브도메인)에 로그인했을 때 쓰는 기능들입니다.  
메뉴(`MenuView.vue`)와 라우트(`tenant.js`) 기준으로 정리했습니다.

### 4.1 대시보드
- **경로**: `/dashboard`
- 요약 정보 표시 (학교별 지표).

### 4.2 출결(Asistencia)
- **경로**: `/assists`
- 출석 관리, 출결 보고서(`/reports/assists`).

### 4.3 급식(Comedor)
- **경로**:  
  - `/dinners` — 급식 잔액/충전(Recargar Saldo)  
  - `/assists-dinner` — 급식 출석(Asistencia Comedor)

### 4.4 학생(Estudiantes)
- **경로**: `/students`, `/students/add`, `/students/:id/edit`, `/students/:id/detail`, `/students/:id/incident`
- 학생 등록·수정·상세·사고(incident) 기록.  
- **공개 등록**: `/registro` (StudentRegisterView) — 로그인 없이 학생 등록 가능.

### 4.5 교사·학술 데이터 (Datos Maestros)
- **교사**: `/teachers` (Profesores)
- **학년/반/학기/학술그룹**:  
  - Grados(학년): `/grades`  
  - Grupos(반): `/sections`  
  - Años(학기): `/school-cycles`  
  - Grupos Académicos: `/academic-groups`  
- **Especialidades(전공)**: `/specialties`  
- **Incidentes(사고 유형)**: `/incidents`

### 4.6 보고서(Reportes)
- **출결 보고서**: `/reports/assists`
- **Incidencias(사고 보고)**: `/reports/incidents`, 추가/수정 라우트 포함.

### 4.7 결제(Pagos)
- **경로**: `/payments`
- 결제 목록·등록·수정·삭제·엑셀 내보내기.  
- **MercadoPago** 연동: preference, IPN/webhook.  
- 결제 결과 페이지: `/payment/success`, `/payment/failure`, `/payment/pending`  
- OXXO 등 다른 결제 레이아웃: `/payment` (LayoutOxxo).

### 4.8 설정(Configuración)
- **일반 설정**: `/general-config` (General)
- **학술 설정**: Grados, Grupos, Años, Grupos Académicos, Especialidades, Incidentes (위와 동일 메뉴)
- **사용자**: `/users`, `/users/add`, `/users/:id/edit`
- **역할/권한**: `/roles`, 추가/수정/상세
- **계정 설정**: `/account-config` (비밀번호 등)

### 4.9 인증·기타
- 로그인: `/login`  
- 비밀번호 변경: API `change-password`  
- 프로필: `/profile`  
- 권한에 따라 메뉴 항목 표시/숨김 (`userCan('read ...')`).

---

## 5. 백엔드 API 요약 (테넌트)

`routes/tenant.php`에 정의된 API 일부입니다.

- **인증**: `POST api/login`, `POST api/register`, `PUT api/change-password`, `POST api/logout`
- **학생**: CRUD, CURP/ID 조회, 출결/사고 조회, import
- **출결(Assists)**: 목록, 등록, 조회, PDF 내보내기
- **사고(Incident reports)**: 목록, 등록, 조회, 삭제
- **결제**: 목록, 저장, 수정, 삭제, export, MercadoPago preference/IPN/webhook
- **리스트/업로드**: `lists/{type}`, `upload-file`
- **마스터 데이터**: grades, sections, school-cycles, academic-groups, payment-prices, teachers, specialties, incidents
- **사용자/역할**: users, roles, 권한 부여
- **설정**: account-configuration, general-configuration
- **대시보드**: `GET api/dashboard`

(모두 `InitializeTenancyByDomain` 등 테넌트 미들웨어 안에서 실행되므로, 요청 도메인에 해당하는 학교 DB만 사용합니다.)

---

## 6. 데이터 모델 (테넌트, app/Models/Tenants/)

| 모델 | 설명 |
|------|------|
| User | 학교 소속 사용자 |
| Student, StudentAcademic, StudentHealth, StudentRelative, StudentSocioeconomic | 학생 및 관련 정보 |
| Teacher, Specialty | 교사, 전공 |
| Grade, Section, SchoolCycle, AcademicGroup | 학년, 반, 학기, 학술 그룹 |
| Assist | 출결 |
| Balances, Dinner | 잔액/급식 |
| Payment, PaymentPrice | 결제, 결제 단가 |
| Incident, IncidentReport | 사고 유형, 사고 보고 |
| GeneralConfiguration, AccountConfiguration | 일반/계정 설정 |
| Role | 역할(권한 그룹) |
| File, Dashboard | 파일, 대시보드용 |

---

## 7. 권한(Permissions)

- **Spatie Laravel Permission** (`spatie/laravel-permission`) 사용.
- 프론트엔드: `userCan('read dashboard')`, `userCan('read students')` 등으로 메뉴/기능 노출 제어.
- 백엔드: API에서 역할/권한 검사로 접근 제한.

---

## 8. 로컬 실행 요약 (RUN.md 기준)

1. **DB**: MySQL에 `school` DB 생성 후 `DB/escuela_presente.sql` (또는 demo/secundaria87) import.
2. **환경**: `.env` 복사, `php artisan key:generate`, DB 비밀번호 설정.
3. **의존성**: `composer install`, `npm install`.
4. **프론트**: `npm run dev` (Vite 유지).
5. **서버**: 다른 터미널에서 `php artisan serve` → `http://127.0.0.1:8000`.

로컬에서는 `localhost`로 접속하므로 **항상 중앙(Central) 앱**만 보입니다.  
테넌트(학교) 화면을 보려면 도메인을 서브도메인으로 구분하거나, 테넌트 전용 URL/환경이 필요합니다.

---

## 9. 정리

- **Escuela Presente**는 **한 플랫폼에서 여러 학교를 도메인별로 분리해 운영**하는 멀티테넌트 학교 관리 시스템입니다.
- **중앙**: 랜딩, 로그인, 테넌트(학교) 관리.
- **테넌트**: 학교별로 **출결, 급식, 학생/교사, 학년·반·학기, 결제(MercadoPago 등), 보고서, 역할/권한, 설정**을 제공합니다.
- **프론트**는 URL에 따라 중앙/테넌트 라우터를 나누고, **백엔드**는 도메인으로 테넌트를 구분해 학교별 DB에 접속합니다.

이 문서는 프로젝트 루트의 `README.md`, `RUN.md`, `config/tenancy.php`, `routes/*.php`, `resources/js/router/*.js`, `resources/js/components/MenuView.vue` 등을 바탕으로 작성되었습니다.
