<?php require 'includes/auth.php'; requireLogin();
$uid=(int)$_SESSION['user_id'];$err='';$where='';
if($_SERVER['REQUEST_METHOD']==='POST'){checkCsrf();$act=$_POST['action']??'';
 if($act==='name'){$n=trim($_POST['name']??'');$where='name';
  if($n==='')$err='Name is required';
  else{$s=$conn->prepare('UPDATE users SET name=? WHERE id=?');$s->bind_param('si',$n,$uid);$s->execute();$_SESSION['name']=$n;logAct($conn,'profile_update','name');flash('Profile updated');header('Location: profile.php');exit;}}
 if($act==='password'){$where='password';$cur=$_POST['current']??'';$new=$_POST['new']??'';$cf=$_POST['confirm']??'';
  $s=$conn->prepare('SELECT password FROM users WHERE id=?');$s->bind_param('i',$uid);$s->execute();$h=$s->get_result()->fetch_row()[0];
  if(!password_verify($cur,$h))$err='Current password is incorrect';
  elseif(strlen($new)<6)$err='New password must be at least 6 characters';
  elseif($new!==$cf)$err='Passwords do not match';
  else{$hh=password_hash($new,PASSWORD_DEFAULT);$s=$conn->prepare('UPDATE users SET password=? WHERE id=?');$s->bind_param('si',$hh,$uid);$s->execute();
   session_regenerate_id(true);logAct($conn,'password_change');flash('Password changed successfully');header('Location: profile.php');exit;}}}
$s=$conn->prepare('SELECT name,email,role,created_at FROM users WHERE id=?');$s->bind_param('i',$uid);$s->execute();$me=$s->get_result()->fetch_assoc();
$title='Profile';include 'includes/header.php'; ?>
<header class="pagehead"><div><h1>My Profile</h1><p><?=e($me['email'])?> &middot; <?=e($me['role'])?> &middot; member since <?=e(substr($me['created_at'],0,10))?></p></div><img src="public/images/products/chair.svg" alt=""></header>
<div class="dash2">
<form class="box wide" method="post"><h3>Your name</h3><?=csrfField()?><input type="hidden" name="action" value="name">
<?php if($err&&$where==='name'): ?><div class="msg err"><?=e($err)?></div><?php endif ?>
<label>Name</label><input name="name" value="<?=e($_POST['name']??$me['name'])?>"><br><br><button type="submit">Save name</button></form>
<form class="box wide" method="post"><h3>Change password</h3><?=csrfField()?><input type="hidden" name="action" value="password">
<?php if($err&&$where==='password'): ?><div class="msg err"><?=e($err)?></div><?php endif ?>
<label>Current password</label><input type="password" name="current">
<label>New password</label><input type="password" name="new">
<label>Confirm new password</label><input type="password" name="confirm"><br><br><button type="submit">Change password</button></form></div>
<div class="card"><h3>Recent sign-ins</h3><?php $li=$conn->query("SELECT created_at FROM activity_log WHERE user_id=$uid AND action='login' ORDER BY id DESC LIMIT 5"); ?>
<?php if(!$li->num_rows): ?><p class="mute">No sign-ins recorded yet.</p><?php endif ?><?php foreach($li as $x): ?><div class="att"><span><b>Signed in</b><small><?=e($x['created_at'])?></small></span></div><?php endforeach ?></div>
<?php include 'includes/footer.php'; ?>
