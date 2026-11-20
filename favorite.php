<?php require 'includes/auth.php'; requireLogin();
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: assets.php');exit;}
checkCsrf();$id=(int)($_POST['id']??0);$uid=(int)$_SESSION['user_id'];
if($id&&canTouch($conn,$id)){
 $s=$conn->prepare('SELECT 1 FROM favorites WHERE user_id=? AND asset_id=?');$s->bind_param('ii',$uid,$id);$s->execute();
 if($s->get_result()->num_rows){$d=$conn->prepare('DELETE FROM favorites WHERE user_id=? AND asset_id=?');$d->bind_param('ii',$uid,$id);$d->execute();flash('Removed from favorites');}
 else{$d=$conn->prepare('INSERT INTO favorites(user_id,asset_id) VALUES(?,?)');$d->bind_param('ii',$uid,$id);$d->execute();flash('Added to favorites');}}
$b=$_POST['back']??'';
if(!preg_match('/^(assets|asset-view)\.php(\?[A-Za-z0-9=&%_.+\-]*)?\z/',$b))$b='assets.php';
header('Location: '.$b);
