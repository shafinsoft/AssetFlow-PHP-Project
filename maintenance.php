<?php require 'includes/auth.php'; requireLogin();
$uid=(int)$_SESSION['user_id'];$admin=isAdmin();$sc=$admin?'':' AND a.user_id='.$uid;
if($_SERVER['REQUEST_METHOD']==='POST'){checkCsrf();$act=$_POST['action']??'';
 if($act==='add'){$aid=(int)($_POST['asset_id']??0);$t=trim($_POST['title']??'');$d=vdate($_POST['due_date']??'');$nt=trim($_POST['notes']??'');
  if(!$aid||!canTouch($conn,$aid)||$t===''||!$d)flash('Choose an asset, a title and a valid date','err');
  else{$s=$conn->prepare('INSERT INTO maintenance(asset_id,title,due_date,notes) VALUES(?,?,?,?)');$s->bind_param('isss',$aid,$t,$d,$nt);$s->execute();logAct($conn,'maintenance_add',$t,$aid);flash('Maintenance scheduled');}
 }elseif($act==='done'||$act==='delete'){$mid=(int)($_POST['id']??0);
  $r=$conn->query('SELECT asset_id,title FROM maintenance WHERE id='.$mid)->fetch_assoc();
  if($r&&canTouch($conn,(int)$r['asset_id'])){
   if($act==='done'){$conn->query('UPDATE maintenance SET done=1,done_at=NOW() WHERE id='.$mid);logAct($conn,'maintenance_done',$r['title'],(int)$r['asset_id']);flash('Marked as done');}
   else{$conn->query('DELETE FROM maintenance WHERE id='.$mid);logAct($conn,'maintenance_delete',$r['title'],(int)$r['asset_id']);flash('Maintenance removed');}
  }else flash('Not allowed','err');}
 header('Location: maintenance.php');exit;}
$tab=$_GET['tab']??'open';if(!in_array($tab,['open','overdue','done'],true))$tab='open';
$cond=$tab==='done'?'m.done=1':($tab==='overdue'?'m.done=0 AND m.due_date<CURDATE()':'m.done=0');
$rows=$conn->query("SELECT m.*,a.asset_name FROM maintenance m JOIN assets a ON a.id=m.asset_id WHERE $cond$sc ORDER BY m.due_date ".($tab==='done'?'DESC':'ASC').' LIMIT 100');
$assets=[];foreach($conn->query("SELECT a.id,a.asset_name FROM assets a WHERE a.status<>'Disposed'$sc ORDER BY a.asset_name") as $x)$assets[]=$x;
$pre=(int)($_GET['asset']??0);
$title='Maintenance';include 'includes/header.php'; ?>
<header class="pagehead"><div><h1>Maintenance Scheduler</h1><p>Plan upkeep before something breaks. Overdue items appear in your notifications.</p></div><img src="public/images/products/car.svg" alt=""></header>
<form class="box wide" method="post" id="maintForm"><h3>Schedule maintenance</h3><?=csrfField()?><input type="hidden" name="action" value="add"><div class="row">
<div><label>Asset *</label><select name="asset_id"><option value="">Choose an asset</option><?php foreach($assets as $x): ?><option value="<?=$x['id']?>" <?=$pre===(int)$x['id']?'selected':''?>><?=e($x['asset_name'])?></option><?php endforeach ?></select></div>
<div><label>Task *</label><input name="title" placeholder="e.g. Service, battery check"></div>
<div><label>Due date *</label><input type="date" name="due_date"></div></div>
<label>Notes</label><input name="notes"><br><br><button type="submit">Schedule</button></form>
<div class="chips"><?php foreach(['open'=>'Open','overdue'=>'Overdue','done'=>'Completed'] as $k=>$l): ?><a class="chip<?=$tab===$k?' on':''?>" href="maintenance.php?tab=<?=$k?>"><?=$l?></a><?php endforeach ?></div>
<div class="tw"><table id="maintTable"><tr><th>Due</th><th>Asset</th><th>Task</th><th>Notes</th><th>Status</th><th>Actions</th></tr>
<?php if(!$rows->num_rows): ?><tr><td colspan="6"><div class="empty"><img src="public/images/products/box.svg" alt=""><p>Nothing here.</p></div></td></tr><?php endif ?>
<?php foreach($rows as $m): $b=mBadge($m); ?><tr><td><?=e($m['due_date'])?></td><td><a href="asset-view.php?id=<?=$m['asset_id']?>"><?=e($m['asset_name'])?></a></td><td><?=e($m['title'])?></td><td><?=e($m['notes'])?></td>
<td><span class="badge <?=$b[0]?>"><?=e($b[1])?></span></td><td>
<?php if(!$m['done']): ?><form method="post"><?=csrfField()?><input type="hidden" name="action" value="done"><input type="hidden" name="id" value="<?=$m['id']?>"><button>Mark done</button></form> <?php endif ?>
<form method="post" data-confirm="Remove this maintenance item?"><?=csrfField()?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$m['id']?>"><button class="red">Delete</button></form></td></tr><?php endforeach ?></table></div>
<?php include 'includes/footer.php'; ?>
