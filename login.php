<?php require 'includes/auth.php';
if(logged()){header('Location: dashboard.php');exit;}
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){checkCsrf();
 $email=trim($_POST['email']??'');$pw=$_POST['password']??'';$ip=$_SERVER['REMOTE_ADDR']??'';
 if($email===''||$pw==='')$err='Email and password are required';
 else{
  $ca=$conn->prepare('SELECT COUNT(*) FROM login_attempts WHERE email=? AND ip=? AND created_at>DATE_SUB(NOW(),INTERVAL 10 MINUTE)');
  $ca->bind_param('ss',$email,$ip);$ca->execute();
  if((int)$ca->get_result()->fetch_row()[0]>=5)$err='Too many failed attempts. Please try again in 10 minutes.';
  else{
   $s=$conn->prepare('SELECT id,name,password,role FROM users WHERE email=?');$s->bind_param('s',$email);$s->execute();
   $u=$s->get_result()->fetch_assoc();
   if($u&&password_verify($pw,$u['password'])){
    $d=$conn->prepare('DELETE FROM login_attempts WHERE email=? AND ip=?');$d->bind_param('ss',$email,$ip);$d->execute();
    session_regenerate_id(true);
    $_SESSION['user_id']=$u['id'];$_SESSION['name']=$u['name'];$_SESSION['role']=$u['role'];
    logAct($conn,'login');header('Location: dashboard.php');exit;}
   $i=$conn->prepare('INSERT INTO login_attempts(email,ip) VALUES(?,?)');$i->bind_param('ss',$email,$ip);$i->execute();
   $err='Invalid email or password';}}}
$title='Login';include 'includes/header.php'; ?>
<div class="auth"><aside class="auth-art"><div><h2>Welcome back</h2><p>Pick up where you left off and check on your assets.</p></div>
<img class="fl" style="--w:150px;right:6%;bottom:70px;--r:6deg;--t:8s;--dx:-10px;--dy:-14px" src="public/images/products/laptop.svg" alt="">
<img class="fl" style="--w:105px;left:10%;bottom:26px;--r:-6deg;--t:10s;--dx:12px;--dy:-10px" src="public/images/products/chair.svg" alt=""></aside><div class="auth-form"><form class="box" method="post" id="loginForm"><h2>Login</h2>
<?php if($err): ?><div class="msg err" id="error"><?=e($err)?></div><?php endif ?><?=csrfField()?>
<label>Email</label><input type="email" name="email" id="email" value="<?=e($_POST['email']??'')?>">
<label>Password</label><input type="password" name="password" id="password"><br><br>
<button type="submit" id="loginBtn">Login</button> <a href="signup.php">Create account</a></form></div></div>
<?php include 'includes/footer.php'; ?>
