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
    // Approved (users)
    $q2 = $conn->query("SELECT type, COUNT(*) c FROM users GROUP BY type");
    while ($r = $q2->fetch_assoc()) {
        if ($r['type'] === 'organization') $counts['approved_organizations'] = (int)$r['c'];
        if ($r['type'] === 'institution') $counts['approved_institutions'] = (int)$r['c'];
    }
    // Rejected
    $q3 = $conn->query("SELECT COUNT(*) c FROM registrations WHERE status='rejected'");
    $counts['rejected_total'] = (int)($q3->fetch_assoc()['c'] ?? 0);
    // Derived totals
    $counts['total_organizations'] = $counts['pending_organizations'] + $counts['approved_organizations'];
    $counts['total_institutions'] = $counts['pending_institutions'] + $counts['approved_institutions'];
    $counts['pending_total'] = $counts['pending_organizations'] + $counts['pending_institutions'];
    $counts['approved_total'] = $counts['approved_organizations'] + $counts['approved_institutions'];
    $counts['total_verifications'] = $counts['approved_total'];

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
        respond_json(['error' => 'unauthorized'], 401);
    }

    $data = require_json();
    $extracted_data = $data['extracted_fields'] ?? [];
    $confidence = floatval($data['confidence'] ?? 0);
    $file_path = trim($data['file_path'] ?? '');
    $file_name = trim($data['file_name'] ?? '');

    if (empty($extracted_data) || empty($file_path) || empty($file_name)) {
        respond_json(['error' => 'missing required data'], 400);
    }

    // Validate confidence value
    if ($confidence < 0 || $confidence > 1) {
        $confidence = 0.6; // Default fallback
    }

    // Map extracted fields to database columns
    $student_name = $extracted_data['StudentName'] ?? null;
    $hall_ticket_no = $extracted_data['HallTicketNo'] ?? null;
    $certificate_no = $extracted_data['SerialNo'] ?? null;
    $branch = $extracted_data['Branch'] ?? null;
    $exam_type = $extracted_data['ExamType'] ?? null;
    $exam_month_year = $extracted_data['ExamMonthYear'] ?? ($extracted_data['ExamMonthYear'] ?? null);
    $total_marks = isset($extracted_data['TotalMarks']) ? intval($extracted_data['TotalMarks']) : null;
    $total_credits = isset($extracted_data['TotalCredits']) ? floatval($extracted_data['TotalCredits']) : null;
    $sgpa = isset($extracted_data['SGPA']) ? floatval($extracted_data['SGPA']) : null;
    $cgpa = isset($extracted_data['CGPA']) ? floatval($extracted_data['CGPA']) : null;
    $date_of_issue = null;
    if (!empty($extracted_data['IssueDate'])) {
        $date_of_issue = date('Y-m-d', strtotime($extracted_data['IssueDate']));
    }
    $file_hash = hash_file('sha256', $file_path);
    $original_file_path = $file_path;
    $status = 'pending';

    // Additional fields for ocr_saved_details table
    $university = $extracted_data['University'] ?? null;
    $college = $extracted_data['College'] ?? null;
    $course = $extracted_data['Course'] ?? null;
    $medium = $extracted_data['Medium'] ?? null;
    $pass_status = $extracted_data['PassStatus'] ?? null;
    $aggregate = $extracted_data['Aggregate'] ?? null;
    $achievement = $extracted_data['Achievement'] ?? null;
    $raw_extracted_fields = json_encode($extracted_data);

    $conn = db();
    $stmt = $conn->prepare("INSERT INTO ocr_saved_details (institution_id, student_name, hall_ticket_no, certificate_no, branch, exam_type, exam_month_year, total_marks, total_credits, sgpa, cgpa, date_of_issue, file_hash, original_file_path, status, confidence, university, college, course, medium, pass_status, aggregate, achievement, raw_extracted_fields) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        respond_json(['error' => 'database prepare failed', 'detail' => $conn->error], 500);
    }

    $stmt->bind_param(
        'issssssiiiddssssdsssssss',
        $user_id,
        $student_name,
        $hall_ticket_no,
        $certificate_no,
        $branch,
        $exam_type,
        $exam_month_year,
        $total_marks,
        $total_credits,
        $sgpa,
        $cgpa,
        $date_of_issue,
        $file_hash,
        $original_file_path,
        $status,
        $confidence,
        $university,
        $college,
        $course,
        $medium,
        $pass_status,
        $aggregate,
        $achievement,
        $raw_extracted_fields
    );

    if (!$stmt->execute()) {
        respond_json(['error' => 'database insert failed', 'detail' => $stmt->error], 500);
    }

    respond_json(['ok' => true, 'id' => $conn->insert_id, 'message' => 'Data saved to ocr_saved_details successfully']);
}

?>
