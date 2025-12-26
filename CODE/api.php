<?php
require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['action'] ?? '';

if ($method === 'POST' && $path === 'register') return handle_register();
if ($method === 'POST' && $path === 'admin_login') return handle_admin_login();
if ($method === 'POST' && $path === 'admin_verify_otp') return handle_admin_verify_otp();
if ($method === 'GET'  && $path === 'admin_summary') return handle_admin_summary();
if ($method === 'GET'  && $path === 'pending') return handle_list_pending();
if ($method === 'POST' && $path === 'approve') return handle_approve();
if ($method === 'POST' && $path === 'reject') return handle_reject();
if ($method === 'POST' && $path === 'login_start') return handle_login_start();
if ($method === 'POST' && $path === 'verify_otp') return handle_verify_otp();
if ($method === 'GET'  && $path === 'session_redirect') return handle_session_redirect();
if ($method === 'GET'  && $path === 'user_detail') return handle_user_detail();
if ($method === 'GET'  && $path === 'registration_detail') return handle_registration_detail();
if ($method === 'POST' && $path === 'ocr_text') return handle_ocr_text();
if ($method === 'POST' && $path === 'save_ocr_data') return handle_save_ocr_data();
if ($method === 'POST' && $path === 'validate_certificate') return handle_validate_certificate();
if ($method === 'POST' && $path === 'bulk_upload_csv') return handle_bulk_upload_csv();
if ($method === 'GET'  && $path === 'get_records') return handle_get_records();
if ($method === 'POST' && $path === 'delete_record') return handle_delete_record();
if ($method === 'GET'  && $path === 'institute_dashboard_stats') return handle_institute_dashboard_stats();
if ($method === 'POST' && $path === 'save_organization_validation') return handle_save_organization_validation();
if ($method === 'GET'  && $path === 'get_organization_validations') return handle_get_organization_validations();
if ($method === 'GET'  && $path === 'organization_dashboard_stats') return handle_organization_dashboard_stats();

respond_json(['error' => 'Not found'], 404);

function require_json(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function handle_register(): void {
    $data = $_POST;
    $type = $data['type'] ?? '';
    if (!in_array($type, ['organization','institution'])) respond_json(['error'=>'invalid type'], 400);
    $name = trim($data['name'] ?? '');
    $org_type = trim($data['org_type'] ?? '');
    $inst_type = trim($data['inst_type'] ?? '');
    $inst_code = trim($data['inst_code'] ?? '');
    $inst_university = trim($data['inst_university'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $website = trim($data['website'] ?? '');
    $address_line1 = trim($data['address_line1'] ?? '');
    $address_line2 = trim($data['address_line2'] ?? '');
    $city = trim($data['city'] ?? '');
    $state = trim($data['state'] ?? '');
    $district = trim($data['district'] ?? '');
    $pincode = trim($data['pincode'] ?? '');
    $country = trim($data['country'] ?? 'India');
    $password = $data['password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        respond_json(['error' => 'validation failed'], 422);
    }
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Prevent same email being used across types or duplicate pending records
    $conn = db();
    $chkUser = $conn->prepare("SELECT type FROM users WHERE email=? LIMIT 1");
    $chkUser->bind_param('s', $email);
    $chkUser->execute();
    $userRow = $chkUser->get_result()->fetch_assoc();
    if ($userRow) {
        if ($userRow['type'] !== $type) {
            respond_json(['error' => 'email already registered as '. $userRow['type']], 409);
        }
        respond_json(['error' => 'email already registered'], 409);
    }
    $chkReg = $conn->prepare("SELECT type,status FROM registrations WHERE email=? AND status IN ('pending','approved') ORDER BY id DESC LIMIT 1");
    $chkReg->bind_param('s', $email);
    $chkReg->execute();
    $regRow = $chkReg->get_result()->fetch_assoc();
    if ($regRow) {
        if ($regRow['type'] !== $type) {
            respond_json(['error' => 'email already submitted under '. $regRow['type']], 409);
        }
        respond_json(['error' => 'email already submitted, current status: '.$regRow['status']], 409);
    }

    // Document upload (optional)
    $document_path = null;
    if (!empty($_FILES['document']['name'])) {
        $uploadDir = __DIR__ . '/uploads';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $basename = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/','_', $_FILES['document']['name']);
        $target = $uploadDir . '/' . $basename;
        if (!move_uploaded_file($_FILES['document']['tmp_name'], $target)) {
            respond_json(['error' => 'file upload failed'], 500);
        }
        $document_path = 'uploads/' . $basename;
    }

    $stmt = db()->prepare("INSERT INTO registrations(type,name,org_type,inst_type,inst_code,inst_university,email,phone,website,address_line1,address_line2,city,state,district,pincode,country,password_hash,document_path) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    if (!$stmt) {
        respond_json(['error'=>'prepare failed','detail'=>db()->error], 500);
    }
    $stmt->bind_param('ssssssssssssssssss', $type,$name,$org_type,$inst_type,$inst_code,$inst_university,$email,$phone,$website,$address_line1,$address_line2,$city,$state,$district,$pincode,$country,$password_hash,$document_path);
    if (!$stmt->execute()) {
        respond_json(['error'=>'insert failed','detail'=>$stmt->error], 500);
    }
    respond_json(['ok' => true]);
}

function handle_admin_login(): void {
    $data = require_json();
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $stmt = db()->prepare("SELECT id,password_hash FROM admins WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    if (!$row || !password_verify($password, $row['password_hash'])) {
        respond_json(['error'=>'invalid credentials'], 401);
    }
    // generate OTP for admin
    $code = strval(random_int(100000, 999999));
    $expires = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');
    $ins = db()->prepare("INSERT INTO admin_otps(admin_id, code, expires_at) VALUES(?,?,?)");
    $ins->bind_param('iss', $row['id'], $code, $expires);
    $ins->execute();
    $_SESSION['pending_admin_id'] = $row['id'];
    send_otp_email($email, $code);
    respond_json(['ok'=>true, 'otp'=>true]);
}

function handle_admin_verify_otp(): void {
    $data = require_json();
    $code = $data['code'] ?? '';
    $aid = $_SESSION['pending_admin_id'] ?? 0;
    if (!$aid) respond_json(['error'=>'session expired'], 401);
    $stmt = db()->prepare("SELECT id,expires_at FROM admin_otps WHERE admin_id=? AND code=? AND used=0 ORDER BY id DESC LIMIT 1");
    $stmt->bind_param('is', $aid, $code);
    $stmt->execute();
    $res = $stmt->get_result();
    $otp = $res->fetch_assoc();
    if (!$otp) respond_json(['error'=>'invalid code'], 400);
    if (new DateTime($otp['expires_at']) < new DateTime()) respond_json(['error'=>'expired'], 400);
    db()->query("UPDATE admin_otps SET used=1 WHERE id=" . (int)$otp['id']);
    $_SESSION['admin_id'] = $aid;
    unset($_SESSION['pending_admin_id']);
    respond_json(['ok'=>true]);
}

function handle_admin_summary(): void {
    ensure_admin();
    $conn = db();
    $counts = [
        'pending_organizations' => 0,
        'pending_institutions' => 0,
        'approved_organizations' => 0,
        'approved_institutions' => 0,
        'rejected_total' => 0,
        'total_organizations' => 0,
        'total_institutions' => 0,
        'total_verifications' => 0,
    ];
    // Pending
    $q1 = $conn->query("SELECT type, COUNT(*) c FROM registrations WHERE status='pending' GROUP BY type");
    while ($r = $q1->fetch_assoc()) {
        if ($r['type'] === 'organization') $counts['pending_organizations'] = (int)$r['c'];
        if ($r['type'] === 'institution') $counts['pending_institutions'] = (int)$r['c'];
    }
    // Count institutions from users table
    $instResult = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE type = 'institution'");
    if ($instResult) {
        $row = $instResult->fetch_assoc();
        $counts['approved_institutions'] = (int)($row['cnt'] ?? 0);
    }
    
    // Count organizations from users table
    $orgResult = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE type = 'organization'");
    if ($orgResult) {
        $row = $orgResult->fetch_assoc();
        $counts['approved_organizations'] = (int)($row['cnt'] ?? 0);
    }
    // Rejected
    $q3 = $conn->query("SELECT COUNT(*) c FROM registrations WHERE status='rejected'");
    $counts['rejected_total'] = (int)($q3->fetch_assoc()['c'] ?? 0);
    // Derived totals
    $counts['total_organizations'] = $counts['pending_organizations'] + $counts['approved_organizations'];
    $counts['total_institutions'] = $counts['pending_institutions'] + $counts['approved_institutions'];
    $counts['pending_total'] = $counts['pending_organizations'] + $counts['pending_institutions'];
    $counts['approved_total'] = $counts['approved_organizations'] + $counts['approved_institutions'];
    
    // Total Verifications = sum of institutions and organizations
    $counts['total_verifications'] = $counts['approved_institutions'] + $counts['approved_organizations'];

    // Approved lists
    $lists = ['institutions'=>[], 'organizations'=>[]];
    $resI = $conn->query("SELECT id,name,email,created_at FROM users WHERE type='institution' ORDER BY name ASC");
    while ($row = $resI->fetch_assoc()) $lists['institutions'][] = $row;
    $resO = $conn->query("SELECT id,name,email,created_at FROM users WHERE type='organization' ORDER BY name ASC");
    while ($row = $resO->fetch_assoc()) $lists['organizations'][] = $row;

    // Monthly approvals last 10 months
    $labels = [];
    $data = [];
    for ($i = 9; $i >= 0; $i--) {
        $label = date('M', strtotime("-{$i} months"));
        $start = date('Y-m-01 00:00:00', strtotime("-{$i} months"));
        $end = date('Y-m-t 23:59:59', strtotime("-{$i} months"));
        $stmt = $conn->prepare("SELECT COUNT(*) c FROM users WHERE created_at BETWEEN ? AND ?");
        $stmt->bind_param('ss', $start, $end);
        $stmt->execute();
        $c = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0);
        $labels[] = $label;
        $data[] = $c;
    }

    // Recent pending registrations
    $recent = [];
    $rres = $conn->query("SELECT id,type,name,email,created_at FROM registrations WHERE status='pending' ORDER BY created_at DESC LIMIT 10");
    while ($r = $rres->fetch_assoc()) $recent[] = $r;

    respond_json([
        'counts' => $counts,
        'lists' => $lists,
        'monthly' => ['labels'=>$labels, 'data'=>$data],
        'recent_pending' => $recent,
        'status_breakdown' => [
            'valid' => $counts['approved_total'],
            'suspicious' => $counts['pending_total'],
            'invalid' => $counts['rejected_total'],
        ]
    ]);
}

function ensure_admin(): void {
    if (empty($_SESSION['admin_id'])) respond_json(['error'=>'unauthorized'], 401);
}

function handle_list_pending(): void {
    ensure_admin();
    $type = $_GET['type'] ?? 'both';
    $where = "status='pending'";
    if ($type === 'institutions') $where .= " AND type='institution'";
    if ($type === 'organizations') $where .= " AND type='organization'";
    $result = db()->query("SELECT id,type,name,email,phone,created_at FROM registrations WHERE $where ORDER BY created_at DESC");
    $rows = [];
    while ($r = $result->fetch_assoc()) $rows[] = $r;
    respond_json(['items'=>$rows]);
}

function handle_approve(): void {
    ensure_admin();
    $data = require_json();
    $id = (int)($data['id'] ?? 0);
    if ($id <= 0) respond_json(['error'=>'invalid id'], 400);
    $conn = db();
    $conn->begin_transaction();
    $res = $conn->query("SELECT * FROM registrations WHERE id=$id FOR UPDATE");
    $row = $res->fetch_assoc();
    if (!$row) { $conn->rollback(); respond_json(['error'=>'not found'], 404); }
    $conn->query("UPDATE registrations SET status='approved' WHERE id=$id");
    // Prevent conflicting email type changes
    $exists = $conn->prepare("SELECT type FROM users WHERE email=? LIMIT 1");
    $exists->bind_param('s', $row['email']);
    $exists->execute();
    $e = $exists->get_result()->fetch_assoc();
    if ($e && $e['type'] !== $row['type']) {
        $conn->rollback();
        respond_json(['error' => 'email already exists under different type'], 409);
    }
    // create or update same-type user
    $stmt = $conn->prepare("INSERT INTO users(type,name,email,phone,password_hash) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name), phone=VALUES(phone), password_hash=VALUES(password_hash)");
    $stmt->bind_param('sssss', $row['type'],$row['name'],$row['email'],$row['phone'],$row['password_hash']);
    $stmt->execute();
    $conn->commit();
    respond_json(['ok'=>true]);
}

function handle_reject(): void {
    ensure_admin();
    $data = require_json();
    $id = (int)($data['id'] ?? 0);
    $reason = trim($data['reason'] ?? '');
    if ($id <= 0) respond_json(['error'=>'invalid id'], 400);
    $stmt = db()->prepare("UPDATE registrations SET status='rejected' WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    if ($reason !== '') {
        error_log('Registration '.$id.' rejected: '.$reason);
    }
    respond_json(['ok'=>true]);
}

function handle_login_start(): void {
    $data = require_json();
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $selectedType = $data['type'] ?? null;
    $stmt = db()->prepare("SELECT id,password_hash,type,name FROM users WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        respond_json(['error'=>'invalid credentials or not approved'], 401);
    }
    if ($selectedType && in_array($selectedType, ['organization','institution']) && $selectedType !== $user['type']) {
        respond_json(['error' => 'This email is registered as '.$user['type']], 400);
    }
    // generate OTP
    $code = strval(random_int(100000, 999999));
    $expires = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');
    $ins = db()->prepare("INSERT INTO otps(user_id, code, expires_at) VALUES(?,?,?)");
    $ins->bind_param('iss', $user['id'], $code, $expires);
    $ins->execute();
    // send email via PHPMailer
    send_otp_email($email, $code);
    $_SESSION['pending_user_id'] = $user['id'];
    respond_json(['ok'=>true]);
}

function handle_verify_otp(): void {
    $data = require_json();
    $code = $data['code'] ?? '';
    $uid = $_SESSION['pending_user_id'] ?? 0;
    if (!$uid) respond_json(['error'=>'session expired'], 401);
    $stmt = db()->prepare("SELECT id,expires_at FROM otps WHERE user_id=? AND code=? AND used=0 ORDER BY id DESC LIMIT 1");
    $stmt->bind_param('is', $uid, $code);
    $stmt->execute();
    $res = $stmt->get_result();
    $otp = $res->fetch_assoc();
    if (!$otp) respond_json(['error'=>'invalid code'], 400);
    if (new DateTime($otp['expires_at']) < new DateTime()) respond_json(['error'=>'expired'], 400);
    db()->query("UPDATE otps SET used=1 WHERE id=" . (int)$otp['id']);
    $_SESSION['user_id'] = $uid;
    unset($_SESSION['pending_user_id']);
    // Include user type and computed redirect to ensure correct landing page
    $u = db()->query("SELECT type FROM users WHERE id=" . (int)$uid)->fetch_assoc();
    $userType = $u['type'] ?? null;
    $redirect = $userType === 'institution' ? 'institute_dashboard.php' : 'organisation_dashboard.php';
    // persist for server-side checks if needed later
    $_SESSION['user_type'] = $userType;
    respond_json(['ok'=>true, 'type'=>$userType, 'redirect'=>$redirect]);
}

function handle_session_redirect(): void {
    $uid = $_SESSION['user_id'] ?? 0;
    if (!$uid) respond_json(['error'=>'unauthorized'], 401);
    $u = db()->prepare("SELECT type FROM users WHERE id=? LIMIT 1");
    $u->bind_param('i', $uid);
    $u->execute();
    $res = $u->get_result()->fetch_assoc();
    $type = $res['type'] ?? null;
    if (!$type) respond_json(['error'=>'user not found'], 404);
    $_SESSION['user_type'] = $type;
    $redirect = $type === 'institution' ? 'institute_dashboard.php' : 'organisation_dashboard.php';
    respond_json(['ok'=>true, 'type'=>$type, 'redirect'=>$redirect]);
}

function handle_user_detail(): void {
    ensure_admin();
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $email = trim($_GET['email'] ?? '');
    $type = $_GET['type'] ?? null; // optional filter
    if ($type && !in_array($type, ['organization','institution'])) {
        respond_json(['error' => 'invalid type'], 400);
    }
    if ($id > 0) {
        if ($type) {
            $stmt = db()->prepare("SELECT id,type,name,email,phone,NULL as website,created_at FROM users WHERE id=? AND type=? LIMIT 1");
            $stmt->bind_param('is', $id, $type);
        } else {
            $stmt = db()->prepare("SELECT id,type,name,email,phone,NULL as website,created_at FROM users WHERE id=? LIMIT 1");
            $stmt->bind_param('i', $id);
        }
    } else {
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            respond_json(['error' => 'invalid email'], 400);
        }
        if ($type) {
            $stmt = db()->prepare("SELECT id,type,name,email,phone,NULL as website,created_at FROM users WHERE email=? AND type=? LIMIT 1");
            $stmt->bind_param('ss', $email, $type);
        } else {
            $stmt = db()->prepare("SELECT id,type,name,email,phone,NULL as website,created_at FROM users WHERE email=? LIMIT 1");
            $stmt->bind_param('s', $email);
        }
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    if (!$user) respond_json(['error' => 'not found'], 404);
    respond_json(['ok'=>true, 'item'=>$user]);
}

function handle_registration_detail(): void {
    ensure_admin();
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // user id (approved) if provided
    $regId = isset($_GET['reg_id']) ? (int)$_GET['reg_id'] : 0; // explicit registration id (pending)
    $type = $_GET['type'] ?? null; // organization|institution
    $email = trim($_GET['email'] ?? '');
    if ($type && !in_array($type, ['organization','institution'])) respond_json(['error'=>'invalid type'], 400);

    $conn = db();
    // If reg_id is provided, fetch directly from registrations (works for pending)
    if ($regId > 0) {
        $stmt = $conn->prepare("SELECT NULL as user_id, type, name, email, phone, website, created_at,
            id as registration_id, org_type, inst_type, inst_code, inst_university, website as reg_website,
            address_line1, address_line2, city, state, district, pincode, country, document_path, created_at as submitted_at
            FROM registrations WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $regId);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        if (!$row) respond_json(['error'=>'not found'], 404);
        // Normalize website field preference
        if (empty($row['website']) && !empty($row['reg_website'])) { $row['website'] = $row['reg_website']; }
        respond_json(['ok'=>true, 'item'=>$row]);
    }

    // Otherwise prefer users match, then join to latest registration for extended fields
    if ($id > 0 && $type) {
        $stmt = $conn->prepare("SELECT u.id as user_id,u.type,u.name,u.email,u.phone,u.created_at,
            r.id as registration_id,r.org_type,r.inst_type,r.inst_code,r.inst_university,r.website,r.address_line1,r.address_line2,r.city,r.state,r.district,r.pincode,r.country,r.document_path,r.created_at as submitted_at
            FROM users u LEFT JOIN registrations r ON r.email=u.email AND r.type=u.type AND r.status IN ('approved','pending')
            WHERE u.id=? AND u.type=? ORDER BY r.id DESC LIMIT 1");
        $stmt->bind_param('is', $id, $type);
    } else if ($email !== '' && $type) {
        $stmt = $conn->prepare("SELECT u.id as user_id,u.type,u.name,u.email,u.phone,u.created_at,
            r.id as registration_id,r.org_type,r.inst_type,r.inst_code,r.inst_university,r.website,r.address_line1,r.address_line2,r.city,r.state,r.district,r.pincode,r.country,r.document_path,r.created_at as submitted_at
            FROM users u LEFT JOIN registrations r ON r.email=u.email AND r.type=u.type AND r.status IN ('approved','pending')
            WHERE u.email=? AND u.type=? ORDER BY r.id DESC LIMIT 1");
        $stmt->bind_param('ss', $email, $type);
    } else {
        respond_json(['error'=>'missing id/email/type'], 400);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    if (!$row) respond_json(['error'=>'not found'], 404);
    respond_json(['ok'=>true, 'item'=>$row]);
}

function handle_ocr_text(): void {
    // Accepts multipart/form-data with 'file' (image or PDF). Returns JSON {text, confidence, extracted_fields}
    if (empty($_FILES['file']['name'])) {
        respond_json(['error' => 'no file uploaded'], 400);
    }
    $tmp = $_FILES['file']['tmp_name'];
    $origName = $_FILES['file']['name'];
    $size = (int)($_FILES['file']['size'] ?? 0);
    if ($size <= 0) respond_json(['error' => 'empty file'], 400);

    $uploadsDir = __DIR__ . '/uploads/ocr_images';
    if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0777, true);
    $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/','_', $origName);
    $target = $uploadsDir . '/' . $safeName;

    // If PDF, keep as-is for future, but for now only images are OCR-ed through PIL script.
    $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
    if (!move_uploaded_file($tmp, $target)) {
        respond_json(['error' => 'failed to save file'], 500);
    }

    // Step 1: Run Python OCR script to extract text
    $python = 'python';
    $script = __DIR__ . '/workers/pil_ocr_extractor.py';
    $cmd = $python . ' ' . escapeshellarg($script) . ' --image ' . escapeshellarg($target);
    $output = shell_exec($cmd);
    if ($output === null) {
        respond_json(['error' => 'OCR process failed to start'], 500);
    }
    $ocr_result = json_decode($output, true);
    if (!is_array($ocr_result)) {
        // Fallback: treat raw output as text
        $ocr_result = ['text' => trim($output), 'confidence' => 0.6];
    }

    // Step 2: Run Python certificate extractor to get structured fields
    $extracted_fields = null;
    if (!empty($ocr_result['text'])) {
        $cert_script = __DIR__ . '/workers/certificate_ocr_extractor.py';
        $temp_file = tempnam(sys_get_temp_dir(), 'ocr_text_');
        file_put_contents($temp_file, $ocr_result['text']);
        
        $cert_cmd = $python . ' ' . escapeshellarg($cert_script) . ' --text ' . escapeshellarg($temp_file);
        $cert_output = shell_exec($cert_cmd);
        unlink($temp_file); // Clean up temp file
        
        if ($cert_output !== null) {
            $extracted_fields = json_decode($cert_output, true);
        }
    }

    // Return both OCR text and extracted fields
    $result = [
        'text' => $ocr_result['text'] ?? '',
        'confidence' => $ocr_result['confidence'] ?? 0.6,
        'extracted_fields' => $extracted_fields
    ];
    
    respond_json($result);
}

function send_otp_email(string $to, string $code): void {
    // Prefer Composer autoload if available
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
    } else {
        require_once __DIR__ . '/vendor/PHPMailer.php';
        require_once __DIR__ . '/vendor/SMTP.php';
        require_once __DIR__ . '/vendor/Exception.php';
    }
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'libraryseatmanagement@gmail.com';
        $mail->Password = 'jgrx rble igqm alvm';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('no-reply@academiaauth.com', 'Academia Authenticator');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = 'Your Login OTP';
        $mail->Body = '<p>Your OTP is <strong>' . htmlspecialchars($code) . '</strong>. It expires in 10 minutes.</p>';
        $mail->AltBody = 'Your OTP is ' . $code;
        $mail->send();
    } catch (Exception $e) {
        error_log('Mailer error: ' . $e->getMessage());
    }
}

function handle_save_ocr_data(): void {
    // Save OCR extracted data to ocr_saved_details table with mapped fields
    $user_id = $_SESSION['user_id'] ?? 0;
    if (!$user_id) {
        respond_json(['error' => 'unauthorized - please login again'], 401);
    }

    // Get the raw input first for debugging
    $raw_input = file_get_contents('php://input');
    error_log("Raw input received: " . $raw_input);
    
    $data = json_decode($raw_input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        respond_json(['error' => 'Invalid JSON: ' . json_last_error_msg()], 400);
    }
    
    if (!$data) {
        respond_json(['error' => 'No data received'], 400);
    }

    $extracted_data = $data['extracted_fields'] ?? [];
    $confidence = floatval($data['confidence'] ?? 0);
    $file_name = trim($data['file_name'] ?? '');

    if (empty($extracted_data)) {
        respond_json(['error' => 'No extracted fields data'], 400);
    }

    if (empty($file_name)) {
        respond_json(['error' => 'No file name provided'], 400);
    }

    // Validate confidence value
    if ($confidence < 0 || $confidence > 1) {
        $confidence = 0.6;
    }

    // Simple field mapping - use the exact field names from your OCR extraction
    $fields = [
        'institution_id' => $user_id,
        'student_name' => $extracted_data['StudentName'] ?? $extracted_data['Student Name'] ?? null,
        'hall_ticket_no' => $extracted_data['HallTicketNo'] ?? $extracted_data['Hall Ticket No'] ?? null,
        'certificate_no' => $extracted_data['SerialNo'] ?? $extracted_data['CertificateSerialNo'] ?? null,
        'branch' => $extracted_data['Branch'] ?? null,
        'university' => $extracted_data['UniversityName'] ?? $extracted_data['University Name'] ?? null,
        'college' => $extracted_data['CollegeName'] ?? $extracted_data['College Name'] ?? null,
        'course' => $extracted_data['Course'] ?? null,
        'exam_month_year' => $extracted_data['ExamMonthYear'] ?? $extracted_data['ExaminationMonthYear'] ?? null,
        'total_marks' => isset($extracted_data['TotalMarks']) ? intval($extracted_data['TotalMarks']) : null,
        'sgpa' => isset($extracted_data['SGPA']) ? floatval($extracted_data['SGPA']) : null,
        'cgpa' => isset($extracted_data['CGPA']) ? floatval($extracted_data['CGPA']) : null,
        'date_of_issue' => !empty($extracted_data['IssueDate']) ? date('Y-m-d', strtotime($extracted_data['IssueDate'])) : null,
        'file_hash' => hash('sha256', $file_name . time()),
        'original_file_path' => 'uploads/ocr_images/' . $file_name,
        'status' => 'pending',
        'confidence' => $confidence,
        'pass_status' => $extracted_data['Result'] ?? $extracted_data['PassStatus'] ?? null,
        'aggregate' => $extracted_data['AggregateInWords'] ?? null,
        'achievement' => $extracted_data['Achievement'] ?? null,
        'raw_extracted_fields' => json_encode($extracted_data)
    ];

    // Build SQL dynamically
    $columns = [];
    $placeholders = [];
    $values = [];
    $types = '';
    
    foreach ($fields as $column => $value) {
        $columns[] = "`$column`";
        $placeholders[] = '?';
        $values[] = $value;
        
        // Determine parameter type
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
    }
    
    $sql = "INSERT INTO ocr_saved_details (" . implode(', ', $columns) . ") 
            VALUES (" . implode(', ', $placeholders) . ")";
    
    error_log("SQL: $sql");
    error_log("Types: $types");
    error_log("Values: " . print_r($values, true));
    
    $conn = db();
    if (!$conn) {
        respond_json(['error' => 'Database connection failed'], 500);
    }
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        respond_json(['error' => 'Database prepare failed', 'detail' => $conn->error], 500);
    }
    
    // Bind parameters
    $stmt->bind_param($types, ...$values);
    
    if ($stmt->execute()) {
        $insert_id = $conn->insert_id;
        error_log("Insert successful, ID: $insert_id");
        respond_json([
            'ok' => true, 
            'id' => $insert_id, 
            'message' => 'OCR data saved successfully to database'
        ]);
    } else {
        error_log("Execute error: " . $stmt->error);
        respond_json([
            'error' => 'Database insert failed', 
            'detail' => $stmt->error
        ], 500);
    }
}

function handle_validate_certificate(): void {
    // Validate OCR extracted details against saved database values
    $user_id = $_SESSION['user_id'] ?? 0;
    if (!$user_id) {
        respond_json(['error' => 'unauthorized - please login again'], 401);
    }

    $payload = require_json();
    $fields = $payload['extracted_fields'] ?? [];
    if (!is_array($fields) || empty($fields)) {
        respond_json(['error' => 'no extracted fields provided'], 400);
    }

    // Map incoming common OCR keys to DB columns in ocr_saved_details
    $ht = trim($fields['HallTicketNo'] ?? $fields['Hall Ticket No'] ?? '');
    $serial = trim($fields['SerialNo'] ?? $fields['CertificateSerialNo'] ?? '');
    $student = trim($fields['StudentName'] ?? $fields['Student Name'] ?? '');
    $college = trim($fields['CollegeName'] ?? $fields['College Name'] ?? '');
    $university = trim($fields['UniversityName'] ?? $fields['University Name'] ?? '');

    $conn = db();
    if (!$conn) respond_json(['error' => 'db connection failed'], 500);

    // Build strict WHERE: require BOTH hall_ticket_no AND certificate_no for selection
    $params = [];
    $types = '';
    if ($ht !== '' && $serial !== '') {
        $sql = 'SELECT * FROM ocr_saved_details WHERE hall_ticket_no = ? AND certificate_no = ? ORDER BY id DESC LIMIT 1';
        $types = 'ss';
        $params = [$ht, $serial];
    } else {
        // If either identifier missing, immediately return invalid due to insufficient identifiers
        respond_json(['ok'=>true,'verdict'=>'INVALID','valid'=>false,'reason'=>'missing identifiers']);
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        respond_json(['error' => 'prepare failed', 'detail' => $conn->error, 'sql' => $sql], 500);
    }
    if ($types !== '') { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $res = $stmt->get_result();
    $best = $res->fetch_assoc();
    if (!$best) {
        respond_json(['ok' => true, 'match' => null, 'verdict' => 'NOT FOUND', 'valid' => false]);
    }

    // Early fail for CGPA/SGPA mismatch when both sides present
    $exCgpa = trim((string)($fields['CGPA'] ?? ''));
    $exSgpa = trim((string)($fields['SGPA'] ?? ''));
    $dbCgpa = isset($best['cgpa']) ? trim((string)$best['cgpa']) : '';
    $dbSgpa = isset($best['sgpa']) ? trim((string)$best['sgpa']) : '';
    if ($exCgpa !== '' && $dbCgpa !== '') {
        if (is_numeric($exCgpa) && is_numeric($dbCgpa)) {
            if (abs(floatval($exCgpa) - floatval($dbCgpa)) > 0.05) {
                respond_json(['ok'=>true, 'match'=>$best, 'verdict'=>'INVALID', 'valid'=>false, 'reason'=>'CGPA mismatch']);
            }
        } else if (strcasecmp($exCgpa, $dbCgpa) !== 0) {
            respond_json(['ok'=>true, 'match'=>$best, 'verdict'=>'INVALID', 'valid'=>false, 'reason'=>'CGPA mismatch']);
        }
    }
    if ($exSgpa !== '' && $dbSgpa !== '') {
        if (is_numeric($exSgpa) && is_numeric($dbSgpa)) {
            if (abs(floatval($exSgpa) - floatval($dbSgpa)) > 0.05) {
                respond_json(['ok'=>true, 'match'=>$best, 'verdict'=>'INVALID', 'valid'=>false, 'reason'=>'SGPA mismatch']);
            }
        } else if (strcasecmp($exSgpa, $dbSgpa) !== 0) {
            respond_json(['ok'=>true, 'match'=>$best, 'verdict'=>'INVALID', 'valid'=>false, 'reason'=>'SGPA mismatch']);
        }
    }

    // Build comparison map
    $compare = [
        'Student Name' => [$student, $best['student_name'] ?? null],
        'Hall Ticket No' => [$ht, $best['hall_ticket_no'] ?? null],
        'Serial No' => [$serial, $best['certificate_no'] ?? null],
        'College Name' => [$college, $best['college'] ?? null],
        'University Name' => [$university, $best['university'] ?? null],
        'Branch' => [$fields['Branch'] ?? '', $best['branch'] ?? null],
        'Exam Month Year' => [$fields['ExamMonthYear'] ?? $fields['ExaminationMonthYear'] ?? '', $best['exam_month_year'] ?? null],
        'Course' => [$fields['Course'] ?? '', $best['course'] ?? null],
        'Medium' => [$fields['Medium'] ?? '', $best['medium'] ?? null],
        'Total Marks' => [$fields['TotalMarks'] ?? '', (string)($best['total_marks'] ?? '')],
        'Total Credits' => [$fields['TotalCredits'] ?? '', (string)($best['total_credits'] ?? '')],
        'CGPA' => [$fields['CGPA'] ?? '', (string)($best['cgpa'] ?? '')],
        'SGPA' => [$fields['SGPA'] ?? '', (string)($best['sgpa'] ?? '')],
        'Pass Status' => [$fields['PassStatus'] ?? $fields['Result'] ?? '', $best['pass_status'] ?? null],
        'Aggregate' => [$fields['AggregateInWords'] ?? '', $best['aggregate'] ?? null],
        'Achievement' => [$fields['Achievement'] ?? '', $best['achievement'] ?? null],
        'Date of Issue' => [$fields['IssueDate'] ?? '', $best['date_of_issue'] ?? null],
        'Confidence' => [strval($payload['confidence'] ?? ''), strval($best['confidence'] ?? '')]
    ];

    $mismatches = [];
    $matches = [];
    foreach ($compare as $label => [$left, $right]) {
        $l = trim((string)$left);
        $r = trim((string)($right ?? ''));
        if ($l === '' && $r === '') continue;
        if ($l !== '' && $r !== '' && strcasecmp($l, $r) === 0) {
            $matches[] = $label;
        } else if ($l !== '' && $r !== '') {
            $mismatches[] = $label;
        }
    }

    // Early fail if any of the critical trio mismatch (when both present)
    $criticals = ['Hall Ticket No','Serial No','Student Name'];
    foreach ($criticals as $key) {
        $l = trim((string)($compare[$key][0] ?? ''));
        $r = trim((string)($compare[$key][1] ?? ''));
        if ($l !== '' && $r !== '' && strcasecmp($l, $r) !== 0) {
            respond_json([
                'ok' => true,
                'match' => $best,
                'compare' => $compare,
                'verdict' => 'INVALID',
                'valid' => false,
                'score' => 0
            ]);
        }
    }

    // Scoring per provided weighting with normalization and null handling
    $score = 0.0; $appliedMax = 0.0;
    $addScore = function(float $weight, $l, $r) use (&$score, &$appliedMax) {
        $l = trim((string)($l ?? ''));
        $r = trim((string)($r ?? ''));
        if ($l === '' && $r === '') return; // both null -> ignore
        $appliedMax += $weight; // count this attribute in the denominator
        if ($l === '' && $r !== '') { $score += 0.5 * $weight; return; } // lose 50%
        if ($l !== '' && $r === '') { $score += 0.5 * $weight; return; }
        // case/whitespace-insensitive
        if (strcasecmp($l, $r) === 0) { $score += $weight; return; }
        // fuzzy
        $sim = 0.0; similar_text(strtoupper($l), strtoupper($r), $sim); $sim = $sim / 100.0;
        if ($sim >= 0.85) { $score += $weight * $sim; return; }
        // mismatch -> 0 credit
    };

    // Critical identifiers (50% total)
    $addScore(12.5, $compare['Hall Ticket No'][0], $compare['Hall Ticket No'][1]);
    $addScore(12.5, $compare['Serial No'][0], $compare['Serial No'][1]);
    $addScore(12.5, $compare['Student Name'][0], $compare['Student Name'][1]);
    // institution_id proxy via college+university
    $addScore(6.25, $compare['College Name'][0], $compare['College Name'][1]);
    $addScore(6.25, $compare['University Name'][0], $compare['University Name'][1]);

    // Academic attributes (25%)
    $addScore(8.33, $compare['Branch'][0], $compare['Branch'][1]);
    $addScore(8.33, $compare['Exam Month Year'][0], $compare['Exam Month Year'][1]);
    $addScore(8.34, $compare['Course'][0], $compare['Course'][1]);

    // Performance attributes (15%)
    $addScore(5.0, $compare['CGPA'][0], $compare['CGPA'][1]);
    $addScore(5.0, $compare['SGPA'][0], $compare['SGPA'][1]);
    $addScore(5.0, $compare['Pass Status'][0], $compare['Pass Status'][1]);

    // System/metadata (10%)
    $addScore(5.0, $compare['Aggregate'][0], $compare['Aggregate'][1]);
    $addScore(5.0, $compare['Achievement'][0], $compare['Achievement'][1]);

    $scorePercent = $appliedMax > 0 ? ($score / $appliedMax) * 100.0 : 0.0;
    $valid = ($scorePercent >= 80.0);
    $verdict = $valid ? 'VALID' : 'INVALID';

    respond_json([
        'ok' => true,
        'match' => $best,
        'compare' => $compare,
        'verdict' => $verdict,
        'valid' => $valid,
        'score' => round($scorePercent, 2)
    ]);
}

function validate_csv_row_against_db(array $csvFields, mysqli $conn): array {
    // Validate CSV row data against existing database records (similar to handle_validate_certificate)
    // Map CSV fields to validation format
    $ht = trim($csvFields['hall_ticket_no'] ?? '');
    $serial = trim($csvFields['certificate_no'] ?? '');
    $student = trim($csvFields['student_name'] ?? '');
    $college = trim($csvFields['college'] ?? '');
    $university = trim($csvFields['university'] ?? '');

    // Build WHERE clause: require BOTH hall_ticket_no AND certificate_no for selection
    if ($ht === '' || $serial === '') {
        // No existing record to compare against
        return ['valid' => true, 'score' => 100.0, 'match' => null, 'verdict' => 'NEW_RECORD'];
    }

    $sql = 'SELECT * FROM ocr_saved_details WHERE hall_ticket_no = ? AND certificate_no = ? ORDER BY id DESC LIMIT 1';
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return ['valid' => true, 'score' => 100.0, 'match' => null, 'verdict' => 'NEW_RECORD'];
    }
    
    $stmt->bind_param('ss', $ht, $serial);
    $stmt->execute();
    $res = $stmt->get_result();
    $best = $res->fetch_assoc();
    $stmt->close();
    
    if (!$best) {
        // No existing record found
        return ['valid' => true, 'score' => 100.0, 'match' => null, 'verdict' => 'NEW_RECORD'];
    }

    // Build comparison map (similar to handle_validate_certificate)
    $compare = [
        'Student Name' => [$student, $best['student_name'] ?? null],
        'Hall Ticket No' => [$ht, $best['hall_ticket_no'] ?? null],
        'Serial No' => [$serial, $best['certificate_no'] ?? null],
        'College Name' => [$college, $best['college'] ?? null],
        'University Name' => [$university, $best['university'] ?? null],
        'Branch' => [$csvFields['branch'] ?? '', $best['branch'] ?? null],
        'Exam Month Year' => [$csvFields['exam_month_year'] ?? '', $best['exam_month_year'] ?? null],
        'Course' => [$csvFields['course'] ?? '', $best['course'] ?? null],
        'Medium' => [$csvFields['medium'] ?? '', $best['medium'] ?? null],
        'Total Marks' => [isset($csvFields['total_marks']) ? (string)$csvFields['total_marks'] : '', (string)($best['total_marks'] ?? '')],
        'Total Credits' => [isset($csvFields['total_credits']) ? (string)$csvFields['total_credits'] : '', (string)($best['total_credits'] ?? '')],
        'CGPA' => [isset($csvFields['cgpa']) ? (string)$csvFields['cgpa'] : '', (string)($best['cgpa'] ?? '')],
        'SGPA' => [isset($csvFields['sgpa']) ? (string)$csvFields['sgpa'] : '', (string)($best['sgpa'] ?? '')],
        'Pass Status' => [$csvFields['pass_status'] ?? '', $best['pass_status'] ?? null],
        'Aggregate' => [$csvFields['aggregate'] ?? '', $best['aggregate'] ?? null],
        'Achievement' => [$csvFields['achievement'] ?? '', $best['achievement'] ?? null],
        'Date of Issue' => [$csvFields['date_of_issue'] ?? '', $best['date_of_issue'] ?? null],
    ];

    // Early fail for CGPA/SGPA mismatch when both sides present
    $exCgpa = trim((string)($csvFields['cgpa'] ?? ''));
    $exSgpa = trim((string)($csvFields['sgpa'] ?? ''));
    $dbCgpa = isset($best['cgpa']) ? trim((string)$best['cgpa']) : '';
    $dbSgpa = isset($best['sgpa']) ? trim((string)$best['sgpa']) : '';
    
    if ($exCgpa !== '' && $dbCgpa !== '') {
        if (is_numeric($exCgpa) && is_numeric($dbCgpa)) {
            if (abs(floatval($exCgpa) - floatval($dbCgpa)) > 0.05) {
                return ['valid' => false, 'score' => 0.0, 'match' => $best, 'verdict' => 'INVALID', 'reason' => 'CGPA mismatch'];
            }
        } else if (strcasecmp($exCgpa, $dbCgpa) !== 0) {
            return ['valid' => false, 'score' => 0.0, 'match' => $best, 'verdict' => 'INVALID', 'reason' => 'CGPA mismatch'];
        }
    }
    
    if ($exSgpa !== '' && $dbSgpa !== '') {
        if (is_numeric($exSgpa) && is_numeric($dbSgpa)) {
            if (abs(floatval($exSgpa) - floatval($dbSgpa)) > 0.05) {
                return ['valid' => false, 'score' => 0.0, 'match' => $best, 'verdict' => 'INVALID', 'reason' => 'SGPA mismatch'];
            }
        } else if (strcasecmp($exSgpa, $dbSgpa) !== 0) {
            return ['valid' => false, 'score' => 0.0, 'match' => $best, 'verdict' => 'INVALID', 'reason' => 'SGPA mismatch'];
        }
    }

    // Early fail if any of the critical trio mismatch (when both present)
    $criticals = ['Hall Ticket No', 'Serial No', 'Student Name'];
    foreach ($criticals as $key) {
        $l = trim((string)($compare[$key][0] ?? ''));
        $r = trim((string)($compare[$key][1] ?? ''));
        if ($l !== '' && $r !== '' && strcasecmp($l, $r) !== 0) {
            return ['valid' => false, 'score' => 0.0, 'match' => $best, 'verdict' => 'INVALID', 'reason' => "$key mismatch"];
        }
    }

    // Scoring per provided weighting with normalization and null handling
    $score = 0.0;
    $appliedMax = 0.0;
    $addScore = function(float $weight, $l, $r) use (&$score, &$appliedMax) {
        $l = trim((string)($l ?? ''));
        $r = trim((string)($r ?? ''));
        if ($l === '' && $r === '') return; // both null -> ignore
        $appliedMax += $weight; // count this attribute in the denominator
        if ($l === '' && $r !== '') { $score += 0.5 * $weight; return; } // lose 50%
        if ($l !== '' && $r === '') { $score += 0.5 * $weight; return; }
        // case/whitespace-insensitive
        if (strcasecmp($l, $r) === 0) { $score += $weight; return; }
        // fuzzy
        $sim = 0.0; similar_text(strtoupper($l), strtoupper($r), $sim); $sim = $sim / 100.0;
        if ($sim >= 0.85) { $score += $weight * $sim; return; }
        // mismatch -> 0 credit
    };

    // Critical identifiers (50% total)
    $addScore(12.5, $compare['Hall Ticket No'][0], $compare['Hall Ticket No'][1]);
    $addScore(12.5, $compare['Serial No'][0], $compare['Serial No'][1]);
    $addScore(12.5, $compare['Student Name'][0], $compare['Student Name'][1]);
    // institution_id proxy via college+university
    $addScore(6.25, $compare['College Name'][0], $compare['College Name'][1]);
    $addScore(6.25, $compare['University Name'][0], $compare['University Name'][1]);

    // Academic attributes (25%)
    $addScore(8.33, $compare['Branch'][0], $compare['Branch'][1]);
    $addScore(8.33, $compare['Exam Month Year'][0], $compare['Exam Month Year'][1]);
    $addScore(8.34, $compare['Course'][0], $compare['Course'][1]);

    // Performance attributes (15%)
    $addScore(5.0, $compare['CGPA'][0], $compare['CGPA'][1]);
    $addScore(5.0, $compare['SGPA'][0], $compare['SGPA'][1]);
    $addScore(5.0, $compare['Pass Status'][0], $compare['Pass Status'][1]);

    // System/metadata (10%)
    $addScore(5.0, $compare['Aggregate'][0], $compare['Aggregate'][1]);
    $addScore(5.0, $compare['Achievement'][0], $compare['Achievement'][1]);

    $scorePercent = $appliedMax > 0 ? ($score / $appliedMax) * 100.0 : 100.0;
    $valid = ($scorePercent >= 80.0);
    $verdict = $valid ? 'VALID' : 'INVALID';

    return [
        'valid' => $valid,
        'score' => round($scorePercent, 2),
        'match' => $best,
        'verdict' => $verdict,
        'compare' => $compare
    ];
}

function handle_bulk_upload_csv(): void {
    // Check authentication
    $user_id = $_SESSION['user_id'] ?? 0;
    if (!$user_id) {
        respond_json(['error' => 'unauthorized - please login again'], 401);
    }

    // Check user type
    $conn = db();
    $userCheck = $conn->prepare("SELECT type FROM users WHERE id = ?");
    $userCheck->bind_param('i', $user_id);
    $userCheck->execute();
    $userResult = $userCheck->get_result()->fetch_assoc();
    $userCheck->close();
    
    if (!$userResult) {
        respond_json(['error' => 'user not found'], 404);
    }
    
    $is_organization = ($userResult['type'] === 'organization');

    // Check if file was uploaded
    if (empty($_FILES['file']['name'])) {
        respond_json(['error' => 'no file uploaded'], 400);
    }

    $tmp = $_FILES['file']['tmp_name'];
    $origName = $_FILES['file']['name'];
    
    // Validate file type
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    if ($ext !== 'csv') {
        respond_json(['error' => 'only CSV files are supported'], 400);
    }

    // Read CSV file
    if (($handle = fopen($tmp, 'r')) === false) {
        respond_json(['error' => 'failed to read CSV file'], 500);
    }

    // Read header row
    $headers = fgetcsv($handle);
    if ($headers === false || empty($headers)) {
        fclose($handle);
        respond_json(['error' => 'CSV file is empty or invalid'], 400);
    }

    // Normalize headers (trim, lowercase, remove spaces)
    $headers = array_map(function($h) {
        return strtolower(trim(str_replace([' ', '_', '-'], '_', $h)));
    }, $headers);

    // Map CSV columns to database columns
    $columnMapping = [
        'student_name' => ['student_name', 'studentname', 'name', 'candidate_name'],
        'hall_ticket_no' => ['hall_ticket_no', 'hallticketno', 'hall_ticket', 'roll_number', 'roll_no', 'rollno'],
        'certificate_no' => ['certificate_no', 'certificateno', 'certificate_id', 'certificateid', 'serial_no', 'serialno'],
        'branch' => ['branch', 'department', 'dept'],
        'course' => ['course', 'degree', 'program'],
        'university' => ['university', 'university_name'],
        'college' => ['college', 'college_name', 'institution'],
        'exam_type' => ['exam_type', 'examtype'],
        'exam_month_year' => ['exam_month_year', 'exammonthyear', 'exam_date', 'date'],
        'total_marks' => ['total_marks', 'totalmarks', 'marks'],
        'total_credits' => ['total_credits', 'totalcredits', 'credits'],
        'sgpa' => ['sgpa'],
        'cgpa' => ['cgpa'],
        'date_of_issue' => ['date_of_issue', 'dateofissue', 'issue_date', 'issued_date'],
        'medium' => ['medium', 'language'],
        'pass_status' => ['pass_status', 'passstatus', 'result', 'status'],
        'aggregate' => ['aggregate', 'percentage'],
        'achievement' => ['achievement', 'grade']
    ];

    // Find column indices
    $columnIndices = [];
    foreach ($columnMapping as $dbColumn => $possibleNames) {
        foreach ($possibleNames as $possibleName) {
            $index = array_search($possibleName, $headers);
            if ($index !== false) {
                $columnIndices[$dbColumn] = $index;
                break;
            }
        }
    }

    // Check required columns
    if (empty($columnIndices['student_name'])) {
        fclose($handle);
        respond_json(['error' => 'CSV must contain a student_name column'], 400);
    }

    $inserted = 0;
    $errors = [];
    $rowNum = 1; // Start at 1 (header is row 0)

    // Read and process each row
    while (($data = fgetcsv($handle)) !== false) {
        $rowNum++;
        
        // Skip empty rows
        if (empty(array_filter($data))) {
            continue;
        }

        // Map CSV data to database fields
        $fields = [
            'student_name' => isset($columnIndices['student_name']) && isset($data[$columnIndices['student_name']]) ? trim($data[$columnIndices['student_name']]) : null,
            'hall_ticket_no' => isset($columnIndices['hall_ticket_no']) && isset($data[$columnIndices['hall_ticket_no']]) ? trim($data[$columnIndices['hall_ticket_no']]) : null,
            'certificate_no' => isset($columnIndices['certificate_no']) && isset($data[$columnIndices['certificate_no']]) ? trim($data[$columnIndices['certificate_no']]) : null,
            'branch' => isset($columnIndices['branch']) && isset($data[$columnIndices['branch']]) ? trim($data[$columnIndices['branch']]) : null,
            'course' => isset($columnIndices['course']) && isset($data[$columnIndices['course']]) ? trim($data[$columnIndices['course']]) : null,
            'university' => isset($columnIndices['university']) && isset($data[$columnIndices['university']]) ? trim($data[$columnIndices['university']]) : null,
            'college' => isset($columnIndices['college']) && isset($data[$columnIndices['college']]) ? trim($data[$columnIndices['college']]) : null,
            'exam_type' => isset($columnIndices['exam_type']) && isset($data[$columnIndices['exam_type']]) ? trim($data[$columnIndices['exam_type']]) : null,
            'exam_month_year' => isset($columnIndices['exam_month_year']) && isset($data[$columnIndices['exam_month_year']]) ? trim($data[$columnIndices['exam_month_year']]) : null,
            'total_marks' => isset($columnIndices['total_marks']) && isset($data[$columnIndices['total_marks']]) && is_numeric($data[$columnIndices['total_marks']]) ? intval($data[$columnIndices['total_marks']]) : null,
            'total_credits' => isset($columnIndices['total_credits']) && isset($data[$columnIndices['total_credits']]) && is_numeric($data[$columnIndices['total_credits']]) ? floatval($data[$columnIndices['total_credits']]) : null,
            'sgpa' => isset($columnIndices['sgpa']) && isset($data[$columnIndices['sgpa']]) && is_numeric($data[$columnIndices['sgpa']]) ? floatval($data[$columnIndices['sgpa']]) : null,
            'cgpa' => isset($columnIndices['cgpa']) && isset($data[$columnIndices['cgpa']]) && is_numeric($data[$columnIndices['cgpa']]) ? floatval($data[$columnIndices['cgpa']]) : null,
            'date_of_issue' => null,
            'medium' => isset($columnIndices['medium']) && isset($data[$columnIndices['medium']]) ? trim($data[$columnIndices['medium']]) : null,
            'pass_status' => isset($columnIndices['pass_status']) && isset($data[$columnIndices['pass_status']]) ? trim($data[$columnIndices['pass_status']]) : null,
            'aggregate' => isset($columnIndices['aggregate']) && isset($data[$columnIndices['aggregate']]) ? trim($data[$columnIndices['aggregate']]) : null,
            'achievement' => isset($columnIndices['achievement']) && isset($data[$columnIndices['achievement']]) ? trim($data[$columnIndices['achievement']]) : null,
        ];

        // Parse date_of_issue if present
        if (isset($columnIndices['date_of_issue']) && isset($data[$columnIndices['date_of_issue']])) {
            $dateStr = trim($data[$columnIndices['date_of_issue']]);
            if (!empty($dateStr)) {
                $parsedDate = date('Y-m-d', strtotime($dateStr));
                if ($parsedDate && $parsedDate !== '1970-01-01') {
                    $fields['date_of_issue'] = $parsedDate;
                }
            }
        }

        // Skip if student_name is empty
        if (empty($fields['student_name'])) {
            $errors[] = "Row $rowNum: Student name is required";
            continue;
        }

        // Handle organizations: validate and save to organization_validations
        if ($is_organization) {
            // Validate against database
            $validation = validate_csv_row_against_db($fields, $conn);
            
            // Map CSV fields to extracted_fields format (similar to OCR extraction)
            $extracted_fields = [
                'Student Name' => $fields['student_name'],
                'Hall Ticket No' => $fields['hall_ticket_no'] ?? '',
                'SerialNo' => $fields['certificate_no'] ?? '',
                'College Name' => $fields['college'] ?? '',
                'University Name' => $fields['university'] ?? '',
                'Branch' => $fields['branch'] ?? '',
                'Exam Month Year' => $fields['exam_month_year'] ?? '',
                'Course' => $fields['course'] ?? '',
                'Medium' => $fields['medium'] ?? '',
                'Total Marks' => $fields['total_marks'] ?? '',
                'Total Credits' => $fields['total_credits'] ?? '',
                'CGPA' => $fields['cgpa'] ?? '',
                'SGPA' => $fields['sgpa'] ?? '',
                'Result' => $fields['pass_status'] ?? '',
                'AggregateInWords' => $fields['aggregate'] ?? '',
                'Achievement' => $fields['achievement'] ?? '',
                'IssueDate' => $fields['date_of_issue'] ?? '',
            ];

            // Save to organization_validations table
            $sql = "INSERT INTO organization_validations (
                organization_id, student_name, hall_ticket_no, certificate_no, 
                college_name, university_name, branch, cgpa, sgpa, pass_status, 
                aggregate, confidence, validation_score, validation_status, extracted_fields
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $errors[] = "Row $rowNum: Database prepare failed - " . $conn->error;
                continue;
            }

            $college_name = $fields['college'] ?? null;
            $university_name = $fields['university'] ?? null;
            $branch = $fields['branch'] ?? null;
            $cgpa = $fields['cgpa'] ?? null;
            $sgpa = $fields['sgpa'] ?? null;
            $pass_status = $fields['pass_status'] ?? null;
            $aggregate = $fields['aggregate'] ?? null;
            $confidence = null; // CSV uploads don't have OCR confidence
            $validation_score = $validation['score'];
            $validation_status = $validation['valid'] ? 'VALID' : 'INVALID';
            $extracted_fields_json = json_encode($extracted_fields);

            $stmt->bind_param(
                'issssssddssddss',
                $user_id,
                $fields['student_name'],
                $fields['hall_ticket_no'],
                $fields['certificate_no'],
                $college_name,
                $university_name,
                $branch,
                $cgpa,
                $sgpa,
                $pass_status,
                $aggregate,
                $confidence,
                $validation_score,
                $validation_status,
                $extracted_fields_json
            );

            if ($stmt->execute()) {
                $inserted++;
            } else {
                $errors[] = "Row $rowNum: Insert failed - " . $stmt->error;
            }
            $stmt->close();
        } else {
            // Handle institutions: save to ocr_saved_details (existing behavior)
            $fields['institution_id'] = $user_id;
            $fields['file_hash'] = hash('sha256', $origName . time() . $rowNum);
            $fields['original_file_path'] = 'bulk_upload/' . time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $origName);
            $fields['status'] = 'pending';
            $fields['confidence'] = null;
            $fields['raw_extracted_fields'] = json_encode(array_combine($headers, $data));

            // Build SQL insert statement
            $columns = [];
            $placeholders = [];
            $values = [];
            $types = '';

            foreach ($fields as $column => $value) {
                $columns[] = "`$column`";
                $placeholders[] = '?';
                $values[] = $value;
                
                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_float($value)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }

            $sql = "INSERT INTO ocr_saved_details (" . implode(', ', $columns) . ") 
                    VALUES (" . implode(', ', $placeholders) . ")";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $errors[] = "Row $rowNum: Database prepare failed - " . $conn->error;
                continue;
            }

            $stmt->bind_param($types, ...$values);
            if ($stmt->execute()) {
                $inserted++;
            } else {
                $errors[] = "Row $rowNum: Insert failed - " . $stmt->error;
            }
            $stmt->close();
        }
    }

    fclose($handle);

    respond_json([
        'ok' => true,
        'inserted' => $inserted,
        'errors' => $errors,
        'message' => "Successfully inserted $inserted record(s)" . (count($errors) > 0 ? " with " . count($errors) . " error(s)" : "")
    ]);
}

function handle_get_records(): void {
    // Check authentication
    $user_id = $_SESSION['user_id'] ?? 0;
    if (!$user_id) {
        respond_json(['error' => 'unauthorized - please login again'], 401);
    }

    $search = trim($_GET['search'] ?? '');
    $conn = db();

    // Build query with search functionality - exclude: file_hash, original_file_path, status, file_name, confidence
    $sql = "SELECT id, student_name, hall_ticket_no, certificate_no, branch, exam_type, exam_month_year, 
                   total_marks, total_credits, sgpa, cgpa, date_of_issue, university, college, course, 
                   medium, pass_status, aggregate, achievement, raw_extracted_fields, created_at, updated_at
            FROM ocr_saved_details 
            WHERE institution_id = ?";

    $params = [$user_id];
    $types = 'i';

    // Add search condition if search term provided
    if (!empty($search)) {
        $searchPattern = '%' . $search . '%';
        $sql .= " AND (
            student_name LIKE ? OR
            hall_ticket_no LIKE ? OR
            certificate_no LIKE ? OR
            branch LIKE ? OR
            course LIKE ? OR
            university LIKE ? OR
            college LIKE ? OR
            exam_type LIKE ? OR
            exam_month_year LIKE ? OR
            medium LIKE ? OR
            pass_status LIKE ? OR
            aggregate LIKE ? OR
            achievement LIKE ? OR
            CAST(total_marks AS CHAR) LIKE ? OR
            CAST(total_credits AS CHAR) LIKE ? OR
            CAST(sgpa AS CHAR) LIKE ? OR
            CAST(cgpa AS CHAR) LIKE ? OR
            CONCAT(student_name, ' ', COALESCE(hall_ticket_no, ''), ' ', COALESCE(certificate_no, ''), ' ', COALESCE(course, ''), ' ', COALESCE(university, '')) LIKE ?
        )";
        $params = array_merge($params, array_fill(0, 17, $searchPattern));
        $types .= str_repeat('s', 17);
    }

    $sql .= " ORDER BY created_at DESC LIMIT 1000";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        respond_json(['error' => 'database prepare failed', 'detail' => $conn->error], 500);
    }

    $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) {
        respond_json(['error' => 'database query failed', 'detail' => $stmt->error], 500);
    }

    $result = $stmt->get_result();
    $records = [];

    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }

    $stmt->close();

    respond_json([
        'ok' => true,
        'records' => $records,
        'count' => count($records)
    ]);
}

function handle_delete_record(): void {
    // Check authentication
    $user_id = $_SESSION['user_id'] ?? 0;
    if (!$user_id) {
        respond_json(['error' => 'unauthorized - please login again'], 401);
    }

    $data = require_json();
    $record_id = intval($data['id'] ?? 0);

    if ($record_id <= 0) {
        respond_json(['error' => 'invalid record ID'], 400);
    }

    $conn = db();

    // Verify that the record belongs to the current institution
    $checkStmt = $conn->prepare("SELECT id FROM ocr_saved_details WHERE id = ? AND institution_id = ?");
    if (!$checkStmt) {
        respond_json(['error' => 'database prepare failed', 'detail' => $conn->error], 500);
    }

    $checkStmt->bind_param('ii', $record_id, $user_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 0) {
        $checkStmt->close();
        respond_json(['error' => 'record not found or access denied'], 404);
    }

    $checkStmt->close();

    // Delete the record
    $deleteStmt = $conn->prepare("DELETE FROM ocr_saved_details WHERE id = ? AND institution_id = ?");
    if (!$deleteStmt) {
        respond_json(['error' => 'database prepare failed', 'detail' => $conn->error], 500);
    }

    $deleteStmt->bind_param('ii', $record_id, $user_id);
    
    if ($deleteStmt->execute()) {
        $deleteStmt->close();
        respond_json([
            'ok' => true,
            'message' => 'Record deleted successfully'
        ]);
    } else {
        $error = $deleteStmt->error;
        $deleteStmt->close();
        respond_json(['error' => 'database delete failed', 'detail' => $error], 500);
    }
}

function handle_institute_dashboard_stats(): void {
    // Check authentication
    $user_id = $_SESSION['user_id'] ?? 0;
    if (!$user_id) {
        respond_json(['error' => 'unauthorized - please login again'], 401);
    }

    $conn = db();

    // Get total certificates count for this institution
    $totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM ocr_saved_details WHERE institution_id = ?");
    $totalStmt->bind_param('i', $user_id);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $totalRow = $totalResult->fetch_assoc();
    $totalCertificates = (int)($totalRow['total'] ?? 0);
    $totalStmt->close();

    // Get certificates issued today
    $todayStart = date('Y-m-d 00:00:00');
    $todayEnd = date('Y-m-d 23:59:59');
    $todayStmt = $conn->prepare("SELECT COUNT(*) as total FROM ocr_saved_details WHERE institution_id = ? AND created_at BETWEEN ? AND ?");
    $todayStmt->bind_param('iss', $user_id, $todayStart, $todayEnd);
    $todayStmt->execute();
    $todayResult = $todayStmt->get_result();
    $todayRow = $todayResult->fetch_assoc();
    $todayCertificates = (int)($todayRow['total'] ?? 0);
    $todayStmt->close();

    // Get certificates issued this year
    $yearStart = date('Y-01-01 00:00:00');
    $yearEnd = date('Y-12-31 23:59:59');
    $yearStmt = $conn->prepare("SELECT COUNT(*) as total FROM ocr_saved_details WHERE institution_id = ? AND created_at BETWEEN ? AND ?");
    $yearStmt->bind_param('iss', $user_id, $yearStart, $yearEnd);
    $yearStmt->execute();
    $yearResult = $yearStmt->get_result();
    $yearRow = $yearResult->fetch_assoc();
    $yearCertificates = (int)($yearRow['total'] ?? 0);
    $yearStmt->close();

    // Get monthly trend data for the last 12 months
    $labels = [];
    $data = [];
    
    for ($i = 11; $i >= 0; $i--) {
        $monthStart = date('Y-m-01 00:00:00', strtotime("-{$i} months"));
        $monthEnd = date('Y-m-t 23:59:59', strtotime("-{$i} months"));
        $label = date('M Y', strtotime("-{$i} months"));
        
        $monthStmt = $conn->prepare("SELECT COUNT(*) as count FROM ocr_saved_details WHERE institution_id = ? AND created_at BETWEEN ? AND ?");
        $monthStmt->bind_param('iss', $user_id, $monthStart, $monthEnd);
        $monthStmt->execute();
        $monthResult = $monthStmt->get_result();
        $monthRow = $monthResult->fetch_assoc();
        $count = (int)($monthRow['count'] ?? 0);
        $monthStmt->close();
        
        $labels[] = $label;
        $data[] = $count;
    }

    respond_json([
        'ok' => true,
        'total_certificates' => $totalCertificates,
        'today_certificates' => $todayCertificates,
        'year_certificates' => $yearCertificates,
        'monthly_trend' => [
            'labels' => $labels,
            'data' => $data
        ]
    ]);
}

function handle_save_organization_validation(): void {
    // Save organization validation results to organization_validations table
    $user_id = $_SESSION['user_id'] ?? 0;
    if (!$user_id) {
        respond_json(['error' => 'unauthorized - please login again'], 401);
    }

    // Verify user is an organization
    $conn = db();
    $userCheck = $conn->prepare("SELECT type FROM users WHERE id = ?");
    $userCheck->bind_param('i', $user_id);
    $userCheck->execute();
    $userResult = $userCheck->get_result()->fetch_assoc();
    if (!$userResult || $userResult['type'] !== 'organization') {
        respond_json(['error' => 'only organizations can save validations'], 403);
    }

    $payload = require_json();
    $extracted_fields = $payload['extracted_fields'] ?? [];
    $confidence = isset($payload['confidence']) ? floatval($payload['confidence']) : null;
    $validation_score = isset($payload['validation_score']) ? floatval($payload['validation_score']) : null;
    $validation_status = isset($payload['validation_status']) ? trim($payload['validation_status']) : null;

    if (empty($extracted_fields)) {
        respond_json(['error' => 'no extracted fields provided'], 400);
    }

    // Map extracted fields to database columns
    $student_name = trim($extracted_fields['Student Name'] ?? $extracted_fields['StudentName'] ?? '');
    $hall_ticket_no = trim($extracted_fields['Hall Ticket No'] ?? $extracted_fields['HallTicketNo'] ?? '');
    $certificate_no = trim($extracted_fields['SerialNo'] ?? $extracted_fields['CertificateSerialNo'] ?? $extracted_fields['Serial No'] ?? '');
    $college_name = trim($extracted_fields['College Name'] ?? $extracted_fields['CollegeName'] ?? '');
    $university_name = trim($extracted_fields['University Name'] ?? $extracted_fields['UniversityName'] ?? '');
    $branch = trim($extracted_fields['Branch'] ?? '');
    $cgpa = isset($extracted_fields['CGPA']) && $extracted_fields['CGPA'] !== '' ? floatval($extracted_fields['CGPA']) : null;
    $sgpa = isset($extracted_fields['SGPA']) && $extracted_fields['SGPA'] !== '' ? floatval($extracted_fields['SGPA']) : null;
    $pass_status = trim($extracted_fields['Result'] ?? $extracted_fields['PassStatus'] ?? '');
    $aggregate = trim($extracted_fields['AggregateInWords'] ?? $extracted_fields['Aggregate'] ?? '');

    // Confidence is already sent as percentage (0-100) from JavaScript
    // Store confidence as percentage in database (DECIMAL(5,2) allows 0.00 to 999.99)

    // Store extracted_fields as JSON
    $extracted_fields_json = json_encode($extracted_fields);

    $sql = "INSERT INTO organization_validations (
        organization_id, student_name, hall_ticket_no, certificate_no, 
        college_name, university_name, branch, cgpa, sgpa, pass_status, 
        aggregate, confidence, validation_score, validation_status, extracted_fields
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare error: " . $conn->error);
        respond_json(['error' => 'database prepare failed', 'detail' => $conn->error], 500);
    }

    $stmt->bind_param(
        'issssssddsssdss',
        $user_id,
        $student_name,
        $hall_ticket_no,
        $certificate_no,
        $college_name,
        $university_name,
        $branch,
        $cgpa,
        $sgpa,
        $pass_status,
        $aggregate,
        $confidence,
        $validation_score,
        $validation_status,
        $extracted_fields_json
    );

    if ($stmt->execute()) {
        $stmt->close();
        respond_json([
            'ok' => true,
            'id' => $conn->insert_id,
            'message' => 'Validation saved successfully'
        ]);
    } else {
        error_log("Execute error: " . $stmt->error);
        $stmt->close();
        respond_json([
            'error' => 'database insert failed',
            'detail' => $stmt->error
        ], 500);
    }
}

function handle_get_organization_validations(): void {
    // Get organization validations with filters
    $user_id = $_SESSION['user_id'] ?? 0;
    if (!$user_id) {
        respond_json(['error' => 'unauthorized - please login again'], 401);
    }

    // Verify user is an organization
    $conn = db();
    $userCheck = $conn->prepare("SELECT type FROM users WHERE id = ?");
    $userCheck->bind_param('i', $user_id);
    $userCheck->execute();
    $userResult = $userCheck->get_result()->fetch_assoc();
    $userCheck->close();
    
    if (!$userResult || $userResult['type'] !== 'organization') {
        respond_json(['error' => 'only organizations can view validations'], 403);
    }

    // Get filter parameters
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $score_min = isset($_GET['score_min']) ? floatval($_GET['score_min']) : null;
    $score_max = isset($_GET['score_max']) ? floatval($_GET['score_max']) : null;
    $date_from = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
    $date_to = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = isset($_GET['per_page']) ? min(100, max(1, intval($_GET['per_page']))) : 20;
    $offset = ($page - 1) * $per_page;

    // Build WHERE clause
    $where_conditions = ['organization_id = ?'];
    $params = [$user_id];
    $types = 'i';

    if ($status !== '' && in_array(strtoupper($status), ['VALID', 'INVALID'])) {
        $where_conditions[] = 'validation_status = ?';
        $params[] = strtoupper($status);
        $types .= 's';
    }

    if ($score_min !== null) {
        $where_conditions[] = 'validation_score >= ?';
        $params[] = $score_min;
        $types .= 'd';
    }

    if ($score_max !== null) {
        $where_conditions[] = 'validation_score <= ?';
        $params[] = $score_max;
        $types .= 'd';
    }

    // Date filtering: if both dates provided, use range; if only date_from, use single date
    if ($date_from !== '' && $date_to !== '') {
        $where_conditions[] = 'DATE(created_at) >= ? AND DATE(created_at) <= ?';
        $params[] = $date_from;
        $params[] = $date_to;
        $types .= 'ss';
    } elseif ($date_from !== '') {
        $where_conditions[] = 'DATE(created_at) = ?';
        $params[] = $date_from;
        $types .= 's';
    }

    $where_clause = implode(' AND ', $where_conditions);

    // Get total count (use separate copy of params array)
    $countParams = array_merge([], $params); // Create a copy
    $countTypes = $types;
    $countSql = "SELECT COUNT(*) as total FROM organization_validations WHERE $where_clause";
    $countStmt = $conn->prepare($countSql);
    if (!$countStmt) {
        error_log("Count prepare error: " . $conn->error);
        respond_json(['error' => 'database prepare failed', 'detail' => $conn->error], 500);
    }
    
    if (count($countParams) > 0) {
        $countStmt->bind_param($countTypes, ...$countParams);
    }
    $countStmt->execute();
    $totalResult = $countStmt->get_result()->fetch_assoc();
    $total = (int)($totalResult['total'] ?? 0);
    $countStmt->close();

    // Get records (add pagination params)
    $sql = "SELECT id, student_name, hall_ticket_no, certificate_no, college_name, university_name, 
                   validation_score, validation_status, created_at
            FROM organization_validations 
            WHERE $where_clause 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Records prepare error: " . $conn->error);
        respond_json(['error' => 'database prepare failed', 'detail' => $conn->error], 500);
    }

    $params[] = $per_page;
    $params[] = $offset;
    $types .= 'ii';
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    $stmt->close();

    respond_json([
        'ok' => true,
        'records' => $records,
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'total_pages' => ceil($total / $per_page)
    ]);
}

function handle_organization_dashboard_stats(): void {
    // Get organization dashboard statistics from organization_validations table
    $user_id = $_SESSION['user_id'] ?? 0;
    if (!$user_id) {
        respond_json(['error' => 'unauthorized - please login again'], 401);
    }

    // Verify user is an organization
    $conn = db();
    $userCheck = $conn->prepare("SELECT type FROM users WHERE id = ?");
    $userCheck->bind_param('i', $user_id);
    $userCheck->execute();
    $userResult = $userCheck->get_result()->fetch_assoc();
    $userCheck->close();
    
    if (!$userResult || $userResult['type'] !== 'organization') {
        respond_json(['error' => 'only organizations can view dashboard stats'], 403);
    }

    // Get total verifications
    $totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM organization_validations WHERE organization_id = ?");
    $totalStmt->bind_param('i', $user_id);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result()->fetch_assoc();
    $total = (int)($totalResult['total'] ?? 0);
    $totalStmt->close();

    // Get valid certificates count
    $validStmt = $conn->prepare("SELECT COUNT(*) as total FROM organization_validations WHERE organization_id = ? AND validation_status = 'VALID'");
    $validStmt->bind_param('i', $user_id);
    $validStmt->execute();
    $validResult = $validStmt->get_result()->fetch_assoc();
    $valid = (int)($validResult['total'] ?? 0);
    $validStmt->close();

    // Get invalid certificates count
    $invalidStmt = $conn->prepare("SELECT COUNT(*) as total FROM organization_validations WHERE organization_id = ? AND validation_status = 'INVALID'");
    $invalidStmt->bind_param('i', $user_id);
    $invalidStmt->execute();
    $invalidResult = $invalidStmt->get_result()->fetch_assoc();
    $invalid = (int)($invalidResult['total'] ?? 0);
    $invalidStmt->close();

    respond_json([
        'ok' => true,
        'total_verifications' => $total,
        'valid_certificates' => $valid,
        'invalid_certificates' => $invalid
    ]);
}
?>


