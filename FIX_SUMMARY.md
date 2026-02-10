# 수정 내용 요약

## 문제

1. **이메일 발송 안 됨**: Job을 큐에만 넣고 워커가 없어서 실행 안 됨
2. **비활성화해도 접속됨**: `active` 속성이 `data` JSON에만 저장되고 실제 컬럼은 갱신 안 됨
3. **Vue 에러**: `grades[0]`이 없을 때 `undefined.description` 접근

---

## 수정 사항

### 1. 이메일 - 동기 발송으로 변경
**파일**: `app/Http/Controllers/TenantPublicController.php`

```php
// Job 제거, 바로 발송
Mail::to($input['email'])->send(new EmailForQueue($emailData));
Log::info('Welcome email sent', ['email' => ...]);
```

### 2. Tenant 토글 - DB 직접 업데이트
**파일**: `app/Http/Controllers/TenantController.php`

```php
// 모델 save() 대신 DB 직접 갱신
$centralConnection = config('tenancy.database.central_connection', config('database.default'));
$current = DB::connection($centralConnection)->table('tenants')->where('id', $id)->value('active');
$newActive = ! (bool) $current;
DB::connection($centralConnection)->table('tenants')->where('id', $id)->update(['active' => $newActive]);
```

**이유**: VirtualColumn 트레이트가 `getCustomColumns()`에 없는 속성은 `data` JSON으로만 저장하기 때문

### 3. Tenant 모델 - getCustomColumns 오버라이드
**파일**: `app/Models/Tenant.php`

```php
public static function getCustomColumns(): array
{
    return array_merge(parent::getCustomColumns(), ['active']);
}
```

**참고**: 현재는 DB 직접 업데이트를 쓰므로 이 부분은 선택사항이지만, 나중에 모델로 저장할 경우를 대비

### 4. 미들웨어 - 로그 추가
**파일**: `app/Http/Middleware/PreventInactiveTenantAccess.php`

- `active` 컬럼 존재 여부, 현재 값, 차단 여부 로그 추가

### 5. Vue - grades 빈 배열 가드
**파일**: `resources/js/views/starter/DashboardTenant.vue`

```javascript
grades.value = data?.data ?? [];
if (grades.value.length > 0) {
  form.grade = grades.value[0].description;
}
```

---

## 진단 방법

### 로컬에서 테스트

```bash
# 1. 마이그레이션 실행 (active 컬럼 추가)
php artisan migrate

# 2. 진단 스크립트 실행
php test-tenant-active.php

# 3. 로그 확인
tail -f storage/logs/laravel.log
```

### 배포 후 확인

1. **서버 마이그레이션 확인**
   ```bash
   ssh root@69.62.98.214
   cd /var/www/escuela-presente
   php artisan migrate:status
   ```

2. **토글 테스트**
   - 관리자 패널에서 테넌트 비활성화
   - 로그 확인: `tail -f storage/logs/laravel.log`
   - 로그에 `TenantController::toggleActive - Toggle completed` 나와야 함

3. **비활성 테넌트 접속 차단 확인**
   - 비활성화된 테넌트 도메인으로 접속
   - `tenant-disabled` 뷰 또는 API 403 응답이 나와야 함
   - 로그에 `PreventInactiveTenantAccess - blocking inactive tenant` 나와야 함

---

## 배포 명령어

```bash
cd /e/escuela-presente
git add .
git commit -m "Fix tenant active toggle with direct DB update and add diagnostic logs"
git push origin main
./deploy-remote.sh root 69.62.98.214 /var/www/escuela-presente
```

---

## 만약 여전히 안 되면

### 체크리스트

1. ☐ `tenants` 테이블에 `active` 컬럼 있는가?
   ```sql
   DESCRIBE tenants;
   ```

2. ☐ 토글 API 호출 시 에러 응답 있는가?
   - 브라우저 개발자도구 Network 탭 확인
   - "Active column not available" 에러면 마이그레이션 안 돌아간 것

3. ☐ 로그에 토글 로그 나오는가?
   - `storage/logs/laravel.log` 확인
   - 안 나오면 API가 아예 안 호출되는 것 (프론트엔드 문제)

4. ☐ 프론트엔드가 새 값을 받았는가?
   - Network 탭에서 API 응답 확인
   - `{"success": true, "active": false, ...}` 형태로 와야 함

5. ☐ 미들웨어 로그가 나오는가?
   - 테넌트 도메인 접속 시 `PreventInactiveTenantAccess - checking tenant access` 로그 확인
   - 안 나오면 미들웨어가 등록 안 된 것

---

## 핵심 원리

```
[토글]
관리자 패널 → PATCH /api/tenants/{id}/toggle-active
→ DB::connection(central)->table('tenants')->update(['active' => 0/1])
→ 실제 tenants.active 컬럼 갱신

[차단]
테넌트 도메인 접속 → PreventInactiveTenantAccess 미들웨어
→ DB::connection(central)->table('tenants')->where('id', tenant()->id)->value('active')
→ 0이면 403 + tenant-disabled.blade.php
```

모든 읽기/쓰기가 **central DB의 실제 `active` 컬럼**을 직접 사용하므로, VirtualColumn의 `data` JSON과 무관하게 동작합니다.
