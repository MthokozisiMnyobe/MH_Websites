<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');
putenv('APP_ENV=testing');
putenv('APP_KEY=test-only-key-not-for-production');
putenv('DB_HOST=(empty)');
putenv('DB_NAME=(empty)');
putenv('DB_USER=(empty)');
putenv('DB_PASSWORD=(empty)');
$_ENV['APP_ENV'] = 'testing';
$_ENV['APP_KEY'] = 'test-only-key-not-for-production';
$_ENV['DB_HOST'] = '(empty)';
$_ENV['DB_NAME'] = '(empty)';
$_ENV['DB_USER'] = '(empty)';
$_ENV['DB_PASSWORD'] = '(empty)';
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 2);
$_SERVER['REQUEST_URI'] = '/MH_Websites/tests/checkpoint5-backend.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require dirname(__DIR__) . '/config/bootstrap.php';

$assertions = 0;
function backend_check(bool $condition, string $label): void { global $assertions; $assertions++; if (!$condition) throw new RuntimeException($label . ' failed.'); }
function rejects(callable $callback, string $label): void { try { $callback(); } catch (RuntimeException $e) { backend_check(true, $label); return; } backend_check(false, $label); }
function sqlite_database(): PDO {
    $pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    $pdo->exec('CREATE TABLE quotation_requests (id INTEGER PRIMARY KEY AUTOINCREMENT, quotation_reference TEXT UNIQUE, idempotency_hash TEXT UNIQUE, confirmation_token_hash TEXT UNIQUE, customer_name TEXT, organisation TEXT, email TEXT, phone TEXT, preferred_contact_method TEXT, notes TEXT, compatibility_json TEXT, status TEXT, created_at TEXT, updated_at TEXT)');
    $pdo->exec('CREATE TABLE quotation_request_items (id INTEGER PRIMARY KEY AUTOINCREMENT, quotation_request_id INTEGER, product_id TEXT, product_code TEXT, product_name TEXT, brand TEXT, category TEXT, product_type TEXT, specification_json TEXT, quantity INTEGER CHECK(quantity BETWEEN 1 AND 99), created_at TEXT, UNIQUE(quotation_request_id, product_id))');
    $pdo->exec('CREATE TABLE enquiries (id INTEGER PRIMARY KEY AUTOINCREMENT, enquiry_reference TEXT UNIQUE, idempotency_hash TEXT UNIQUE, customer_name TEXT, organisation TEXT, email TEXT, phone TEXT, enquiry_type TEXT, preferred_contact_method TEXT, message TEXT, status TEXT, created_at TEXT, updated_at TEXT)');
    $pdo->exec('CREATE TABLE submission_rate_limits (id INTEGER PRIMARY KEY AUTOINCREMENT, scope TEXT, key_hash TEXT, window_started_at TEXT, attempt_count INTEGER, updated_at TEXT, UNIQUE(scope,key_hash,window_started_at))');
    return $pdo;
}

$customerInput = ['full_name'=>'Test Customer','organisation'=>'Example Org','email'=>'test@example.org','phone'=>'+27 43 555 0123','preferred_contact'=>'email','quotation_notes'=>'Please quote safely.','privacy_consent'=>'1'];
$customer = validate_quotation_customer($customerInput);
backend_check($customer['email'] === 'test@example.org', 'Valid quotation customer');
foreach ([['email'=>'bad'], ['full_name'=>'x'], ['phone'=>'abc'], ['preferred_contact'=>'sms']] as $change) rejects(fn()=>validate_quotation_customer(array_replace($customerInput, $change)), 'Invalid customer field rejected');
backend_check(validate_quotation_customer(array_replace($customerInput, ['phone'=>'+1 (212) 555-0100']))['phone'] !== '', 'International phone accepted');
rejects(fn()=>validate_quotation_customer(array_replace($customerInput, ['quotation_notes'=>str_repeat('x',2001)])), 'Excessive notes rejected');

$validBasket = json_encode([['id'=>'business-multifunction-printer','quantity'=>99]], JSON_THROW_ON_ERROR);
$items = validate_basket_payload($validBasket);
backend_check($items[0]['quantity'] === 99, 'Quantity boundary 99 accepted');
backend_check($items[0]['product']['name'] === 'Business Multifunction Printer', 'Authoritative catalogue reconstruction');
foreach (['[]','{bad',json_encode([['id'=>'unknown','quantity'=>1]]),json_encode([['id'=>'business-multifunction-printer','quantity'=>0]]),json_encode([['id'=>'business-multifunction-printer','quantity'=>1],['id'=>'business-multifunction-printer','quantity'=>2]]),json_encode([['id'=>'business-multifunction-printer','quantity'=>1,'name'=>'Fake']])] as $payload) rejects(fn()=>validate_basket_payload($payload), 'Invalid basket rejected');
$tooMany = array_fill(0, 51, ['id'=>'business-multifunction-printer','quantity'=>1]);
rejects(fn()=>validate_basket_payload(json_encode($tooMany)), 'Too many basket lines rejected');
rejects(fn()=>create_quotation_request(sqlite_database(), array_replace($customer, ['privacy_consent'=>false]), $items, null, str_repeat('d',64)), 'Missing quotation consent rejected');

$draft = validate_compatibility_draft(['device_type'=>'Printer','manufacturer'=>'Example','model'=>'Model A','current_component'=>'','product_id'=>'black-toner-cartridge','notes'=>'Check fit']);
backend_check($draft['product_id'] === 'black-toner-cartridge', 'Compatibility product authoritative');
rejects(fn()=>validate_compatibility_draft(['device_type'=>'Printer','manufacturer'=>'Example','model'=>'A','product_id'=>'fake']), 'Unknown compatibility product rejected');

$reference = generate_submission_reference('MHQ', new DateTimeImmutable('2026-09-22'));
backend_check((bool) preg_match('/^MHQ-20260922-[0-9A-HJKMNP-TV-Z]{16}$/', $reference), 'Quotation reference format');
backend_check(generate_submission_reference('MHE') !== generate_submission_reference('MHE'), 'References random and unique');

$token = issue_idempotency_token('quotation-test');
$hash = consume_idempotency_token('quotation-test', $token);
backend_check(strlen($hash) === 64, 'Idempotency hash');
rejects(fn()=>consume_idempotency_token('quotation-test', $token), 'Duplicate token rejected');

$pdo = sqlite_database();
$created = create_quotation_request($pdo, $customer, $items, $draft, str_repeat('a',64));
backend_check(str_starts_with($created['reference'], 'MHQ-'), 'Valid quotation persisted');
backend_check((int)$pdo->query('SELECT COUNT(*) FROM quotation_requests')->fetchColumn() === 1, 'One quotation row');
backend_check((int)$pdo->query('SELECT COUNT(*) FROM quotation_request_items')->fetchColumn() === 1, 'Authoritative item snapshot persisted');
$columns = array_keys($pdo->query('SELECT * FROM quotation_request_items')->fetch());
backend_check(!in_array('price',$columns,true) && !in_array('stock',$columns,true), 'No price or stock persisted');
try { create_quotation_request($pdo, $customer, $items, null, str_repeat('a',64)); } catch (PDOException) {}
backend_check((int)$pdo->query('SELECT COUNT(*) FROM quotation_requests')->fetchColumn() === 1, 'Database idempotency constraint prevents duplicate request');
establish_submission_grant('quotation', $created['reference'], $created['confirmation_token']);
backend_check(load_authorised_quotation($pdo)['quotation_reference'] === $created['reference'], 'Confirmation grant authorises request');
$_SESSION['_submission_grants']['quotation']['token'] = str_repeat('0',64);
backend_check(load_authorised_quotation($pdo) === null, 'Wrong confirmation token denied');

$rollback = sqlite_database();
$rollback->exec("CREATE TRIGGER fail_item BEFORE INSERT ON quotation_request_items BEGIN SELECT RAISE(FAIL, 'forced'); END");
try { create_quotation_request($rollback, $customer, $items, null, str_repeat('b',64)); } catch (Throwable) {}
backend_check((int)$rollback->query('SELECT COUNT(*) FROM quotation_requests')->fetchColumn() === 0, 'Transaction rollback removes request');

$limited = sqlite_database();
for ($i=0;$i<6;$i++) enforce_submission_rate_limit($limited,'quotation',new DateTimeImmutable('2026-09-22 12:00:00'));
try { enforce_submission_rate_limit($limited,'quotation',new DateTimeImmutable('2026-09-22 12:00:00')); backend_check(false,'Rate limit'); } catch (RateLimitExceededException) { backend_check(true,'Rate limit enforced'); }
backend_check(rate_limit_key('quotation','127.0.0.1') !== rate_limit_key('enquiry','127.0.0.1'), 'Rate scopes separated');

$interest = ['general'=>'General Enquiry'];
$enquiry = validate_enquiry(['full_name'=>'Test Person','organisation'=>'','email'=>'person@example.org','phone'=>'','enquiry_type'=>'general','preferred_contact'=>'email','project_summary'=>'A sufficiently detailed general enquiry.','privacy_consent'=>'yes'],$interest);
$enquiryDb = sqlite_database();
$enquiryReference = create_enquiry($enquiryDb,$enquiry,str_repeat('c',64));
backend_check(str_starts_with($enquiryReference,'MHE-'), 'Enquiry persisted separately');
rejects(fn()=>validate_enquiry(array_replace($enquiry,['preferred_contact'=>'phone','phone'=>'']),$interest), 'Phone required when preferred');

$nullMailer = new NullMailer();
attempt_submission_notifications($nullMailer,'quotation',$created['reference']);
backend_check(true, 'Disabled mail succeeds safely');
$failingMailer = new class implements MailerInterface { public function send(string $notificationType,string $reference,array $context=[]):void { throw new RuntimeException('mail failure'); } };
attempt_submission_notifications($failingMailer,'quotation',$created['reference']);
backend_check((int)$pdo->query('SELECT COUNT(*) FROM quotation_requests')->fetchColumn() === 1, 'Mail failure does not roll back committed request');

$_SERVER['REQUEST_METHOD']='GET';
try { require_post_request(); backend_check(false,'GET rejected'); } catch (RuntimeException) { backend_check(http_response_code()===405,'GET rejected'); }
backend_check(!csrf_is_valid(str_repeat('0',64)), 'Invalid CSRF rejected');
$_SERVER['CONTENT_LENGTH']='70000'; backend_check(!request_body_within_limit(), 'Oversized body rejected');
backend_check(e('<script>alert(1)</script>') === '&lt;script&gt;alert(1)&lt;/script&gt;', 'Malicious text escaped on output');
backend_check(database_connection() === null, 'Unconfigured database fails safely');
$confirmationSource = (string) file_get_contents(dirname(__DIR__) . '/store/quote-confirmation.php');
$printSource = (string) file_get_contents(dirname(__DIR__) . '/store/print-request.php');
backend_check(!str_contains($confirmationSource, '$_GET') && !str_contains($printSource, '$_GET'), 'No confirmation identifier or PII accepted in URLs');
backend_check(str_contains($printSource, 'SUBMITTED QUOTATION REQUEST SUMMARY') && str_contains($printSource, 'not an issued or priced quotation'), 'Print summary boundary is explicit');

session_write_close();
echo "Checkpoint 5 backend tests passed ({$assertions} assertions)." . PHP_EOL;
