<?php require 'includes/auth.php'; requireAdmin();
$f=$_GET['action']??'';$acts=[];foreach($conn->query('SELECT DISTINCT action FROM activity_log ORDER BY action') as $x)$acts[]=$x['action'];
if(in_array($f,$acts,true)){$s=$conn->prepare('SELECT * FROM activity_log WHERE action=? ORDER BY id DESC LIMIT 100');$s->bind_param('s',$f);$s->execute();$rows=$s->get_result();}
else{$f='';$rows=$conn->query('SELECT * FROM activity_log ORDER BY id DESC LIMIT 100');}
$label=fn($a)=>ucfirst(str_replace('_',' ',$a));
$title='Activity';include 'includes/header.php'; ?>
<header class="pagehead"><div><h1>Activity Log</h1><p>A record of who did what. Showing the latest 100 events.</p></div><img src="public/images/products/laptop.svg" alt=""></header>
<form class="bar" method="get"><select name="action" onchange="this.form.submit()"><option value="">All actions</option>
<?php foreach($acts as $a): ?><option value="<?=e($a)?>" <?=$f===$a?'selected':''?>><?=e($label($a))?></option><?php endforeach ?></select><noscript><button>Filter</button></noscript></form>
<div class="tw"><table id="activityTable"><tr><th>When</th><th>User</th><th>Action</th><th>Details</th></tr>
<?php if(!$rows->num_rows): ?><tr><td colspan="4"><div class="empty"><img src="public/images/products/box.svg" alt=""><p>No activity yet.</p></div></td></tr><?php endif ?>
<?php foreach($rows as $r): ?><tr><td><?=e($r['created_at'])?></td><td><?=e($r['user_name'])?></td><td><span class="badge s-Inactive"><?=e($label($r['action']))?></span></td><td><?=e($r['detail'])?></td></tr><?php endforeach ?></table></div>
<?php include 'includes/footer.php'; ?>
