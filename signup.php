<?php require 'includes/auth.php';
if(logged()){header('Location: dashboard.php');exit;}
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){checkCsrf();
 $n=trim($_POST['name']??'');$email=trim($_POST['email']??'');$pw=$_POST['password']??'';$c=$_POST['confirm']??'';
 if($n===''||$email===''||$pw==='')$err='All fields are required';
 elseif(!filter_var($email,FILTER_VALIDATE_EMAIL))$err='Invalid email format';
 elseif(strlen($pw)<6)$err='Password must be at least 6 characters';
 elseif($pw!==$c)$err='Passwords do not match';
 else{$s=$conn->prepare('SELECT id FROM users WHERE email=?');$s->bind_param('s',$email);$s->execute();
  if($s->get_result()->num_rows)$err='Email already exists';
  else{$h=password_hash($pw,PASSWORD_DEFAULT);
   $s=$conn->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,'customer')");
   $s->bind_param('sss',$n,$email,$h);$s->execute();
   logAct($conn,'signup',$email);flash('Signup successful. Please login.');header('Location: login.php');exit;}}}
$title='Sign Up';include 'includes/header.php'; ?>
<div class="auth"><aside class="auth-art"><div><h2>Start in a minute</h2><p>Create an account and add your first asset today.</p></div>
<img class="fl" style="--w:150px;right:6%;bottom:70px;--r:6deg;--t:8s;--dx:-10px;--dy:-14px" src="public/images/products/laptop.svg" alt="">
<img class="fl" style="--w:105px;left:10%;bottom:26px;--r:-6deg;--t:10s;--dx:12px;--dy:-10px" src="public/images/products/chair.svg" alt=""></aside><div class="auth-form"><form class="box" method="post" id="signupForm"><h2>Sign Up</h2>
<?php if($err): ?><div class="msg err" id="error"><?=e($err)?></div><?php endif ?><?=csrfField()?>
<label>Name</label><input name="name" id="name" value="<?=e($_POST['name']??'')?>">
<label>Email</label><input type="email" name="email" id="email" value="<?=e($_POST['email']??'')?>">
<label>Password</label><input type="password" name="password" id="password">
<label>Confirm Password</label><input type="password" name="confirm" id="confirm"><br><br>
<button type="submit" id="signupBtn">Sign Up</button> <a href="login.php">Already have an account?</a></form></div></div>
<?php include 'includes/footer.php'; ?>
