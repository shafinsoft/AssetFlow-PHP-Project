<?php
session_start();
require_once __DIR__ . '/../config/database.php';
// First run: create default admin if none exists
if(!$conn->query("SELECT id FROM users WHERE role='admin' LIMIT 1")->num_rows){
 $h=password_hash('Admin@123',PASSWORD_DEFAULT);$n='Admin';$em='admin@assetflow.com';
 $s=$conn->prepare("INSERT IGNORE INTO users(name,email,password,role) VALUES(?,?,?,'admin')");$s->bind_param('sss',$n,$em,$h);$s->execute();}
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function logged(){return isset($_SESSION['user_id']);}
function isAdmin(){return ($_SESSION['role']??'')==='admin';}
function requireLogin(){header('Cache-Control: no-store');if(!logged()){header('Location: login.php');exit;}}
function requireAdmin(){requireLogin();if(!isAdmin()){http_response_code(403);die('Access denied');}}
function flash($m=null,$t='ok'){if($m!==null){$_SESSION['flash']=[$m,$t];return null;}
 if(isset($_SESSION['flash'])){$f=$_SESSION['flash'];unset($_SESSION['flash']);return $f;}return null;}
function csrf(){if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(16));return $_SESSION['csrf'];}
function csrfField(){return '<input type="hidden" name="csrf" value="'.csrf().'">';}
function checkCsrf(){if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){http_response_code(400);die('Invalid request');}}
// Ownership check: admin may touch any asset, customer only own
function canTouch($conn,$id){
 if(isAdmin()){$s=$conn->prepare('SELECT id FROM assets WHERE id=?');$s->bind_param('i',$id);}
 else{$s=$conn->prepare('SELECT id FROM assets WHERE id=? AND user_id=?');$s->bind_param('ii',$id,$_SESSION['user_id']);}
 $s->execute();return $s->get_result()->num_rows>0;}
// Auto-migrate: photo column (for databases imported before photos existed)
if(!$conn->query("SHOW COLUMNS FROM assets LIKE 'image'")->num_rows)$conn->query("ALTER TABLE assets ADD image VARCHAR(255) NULL AFTER description");
// Photo upload: returns filename, null (no file) or false (invalid)
function saveImage($f){
 if(empty($f['name'])||$f['error']===UPLOAD_ERR_NO_FILE)return null;
 if($f['error']!==UPLOAD_ERR_OK||$f['size']>2*1024*1024)return false;
 $i=@getimagesize($f['tmp_name']);
 $ext=[IMAGETYPE_JPEG=>'jpg',IMAGETYPE_PNG=>'png',IMAGETYPE_WEBP=>'webp',IMAGETYPE_GIF=>'gif'][$i[2]??0]??null;
 if(!$ext)return false;
 $n=bin2hex(random_bytes(8)).'.'.$ext;
 return move_uploaded_file($f['tmp_name'],__DIR__.'/../uploads/'.$n)?$n:false;}
function delImage($n){if($n&&preg_match('/^[a-f0-9]{16}\.(jpg|png|webp|gif)$/',$n))@unlink(__DIR__.'/../uploads/'.$n);}
// Photo to show: uploaded image, or a category illustration when none
function assetImg($img,$cat){if($img)return 'uploads/'.$img;
 $m=['electronics'=>'laptop','furniture'=>'chair','vehicle'=>'car'];
 return 'public/images/products/'.($m[strtolower((string)$cat)]??'box').'.svg';}
// ---- Premium features: auto-migrations, audit log, date helper ----
$conn->query("CREATE TABLE IF NOT EXISTS activity_log(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NULL,user_name VARCHAR(100),action VARCHAR(60) NOT NULL,detail VARCHAR(255),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
$conn->query("CREATE TABLE IF NOT EXISTS login_attempts(id INT AUTO_INCREMENT PRIMARY KEY,email VARCHAR(150),ip VARCHAR(45),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX(email,ip))");
if(!$conn->query("SHOW COLUMNS FROM assets LIKE 'warranty_date'")->num_rows)$conn->query("ALTER TABLE assets ADD warranty_date DATE NULL AFTER purchase_date");
function logAct($conn,$action,$detail='',$assetId=null){$uid=$_SESSION['user_id']??null;$n=$_SESSION['name']??'Guest';
 $s=$conn->prepare('INSERT INTO activity_log(user_id,user_name,action,detail,asset_id) VALUES(?,?,?,?,?)');$s->bind_param('isssi',$uid,$n,$action,$detail,$assetId);$s->execute();}
function vdate($d){$x=DateTime::createFromFormat('Y-m-d',(string)$d);return($x&&$x->format('Y-m-d')===$d)?$d:null;}
// ---- v3: favorites, maintenance, timeline, health, depreciation, notifications ----
if(!$conn->query("SHOW COLUMNS FROM activity_log LIKE 'asset_id'")->num_rows)$conn->query("ALTER TABLE activity_log ADD asset_id INT NULL");
$conn->query("CREATE TABLE IF NOT EXISTS favorites(user_id INT NOT NULL,asset_id INT NOT NULL,PRIMARY KEY(user_id,asset_id),FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,FOREIGN KEY(asset_id) REFERENCES assets(id) ON DELETE CASCADE)");
$conn->query("CREATE TABLE IF NOT EXISTS maintenance(id INT AUTO_INCREMENT PRIMARY KEY,asset_id INT NOT NULL,title VARCHAR(150) NOT NULL,due_date DATE NOT NULL,notes TEXT,done TINYINT(1) NOT NULL DEFAULT 0,done_at DATETIME NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(asset_id) REFERENCES assets(id) ON DELETE CASCADE)");
// Health score 0-100 from status, warranty and overdue maintenance
function healthScore($status,$warranty,$overdue){
 if($status==='Disposed')return 0;
 $b=['Active'=>100,'Inactive'=>65,'Under Maintenance'=>50][$status]??50;
 if($warranty&&$warranty<date('Y-m-d'))$b-=15;
 $b-=min(30,(int)$overdue*15);return max(0,$b);}
function ringColor($s){return $s>=80?'#16a34a':($s>=50?'#d97706':'#dc2626');}
// Straight-line depreciation: 5 years down to 10% salvage value (an estimate)
function deprec($price,$date){$price=(float)$price;if(!$date||$price<=0)return $price;
 $y=max(0,(time()-strtotime($date))/31557600);return round($price*(1-0.9*min(1,$y/5)),2);}
function mBadge($m){if($m['done'])return['s-Active','Done'];
 $d=(int)floor((strtotime($m['due_date'])-strtotime(date('Y-m-d')))/86400);
 if($d<0)return['s-Disposed','Overdue'];if($d<=7)return['s-UnderMaintenance',$d===0?'Due today':'Due in '.$d.' days'];return['s-Inactive','Scheduled'];}
function notifCount($conn){$sc=isAdmin()?'':' AND a.user_id='.(int)($_SESSION['user_id']??0);
 $w=$conn->query("SELECT COUNT(*) FROM assets a WHERE a.warranty_date IS NOT NULL AND a.warranty_date<=DATE_ADD(CURDATE(),INTERVAL 30 DAY) AND a.warranty_date>=DATE_SUB(CURDATE(),INTERVAL 90 DAY) AND a.status<>'Disposed'$sc")->fetch_row()[0];
 $m=$conn->query("SELECT COUNT(*) FROM maintenance m JOIN assets a ON a.id=m.asset_id WHERE m.done=0 AND m.due_date<=DATE_ADD(CURDATE(),INTERVAL 7 DAY)$sc")->fetch_row()[0];
 return (int)$w+(int)$m;}
