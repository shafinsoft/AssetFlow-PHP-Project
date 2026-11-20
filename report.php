<?php require 'includes/auth.php'; requireLogin();
$admin=isAdmin();$sc=$admin?'':' AND a.user_id='.(int)$_SESSION['user_id'];
$rows=[];$tot=0;$est=0;$by=['Active'=>0,'Inactive'=>0,'Under Maintenance'=>0,'Disposed'=>0];
foreach($conn->query("SELECT a.*,c.category_name,u.name owner FROM assets a LEFT JOIN categories c ON c.id=a.category_id JOIN users u ON u.id=a.user_id WHERE 1=1$sc ORDER BY a.asset_name") as $r){
 $r['est']=$r['status']==='Disposed'?0:deprec($r['price'],$r['purchase_date']);$rows[]=$r;$tot+=$r['price'];$est+=$r['est'];$by[$r['status']]++;}
$title='Reports';include 'includes/header.php'; ?>
<header class="pagehead"><div><h1>Asset Report</h1><p>Generated <?=date('Y-m-d H:i')?> for <?=e($_SESSION['name'])?>. Use Print to save it as a PDF.</p></div><img src="public/images/products/chair.svg" alt=""></header>
<p class="noprint"><button type="button" onclick="window.print()">Print / Save as PDF</button> <a class="btn gray" href="export.php">Download CSV</a></p>
<div class="grid"><div class="card stat">Assets<b><?=count($rows)?></b></div><div class="card stat">Purchase value<b><?=number_format($tot,2)?></b></div><div class="card stat">Estimated value<b><?=number_format($est,2)?></b></div></div>
<p class="mute">Status: <?php foreach($by as $k=>$n): ?><?=e($k)?> <?=$n?> &nbsp; <?php endforeach ?>&middot; Estimated value uses straight-line depreciation over 5 years with 10% salvage.</p>
<div class="tw"><table id="reportTable"><tr><th>Name</th><th>Category</th><th>Status</th><th>Purchased</th><th>Warranty</th><th>Price</th><th>Est. value</th><?php if($admin): ?><th>Owner</th><?php endif ?></tr>
<?php if(!$rows): ?><tr><td colspan="8"><div class="empty"><img src="public/images/products/box.svg" alt=""><p>No assets to report.</p></div></td></tr><?php endif ?>
<?php foreach($rows as $r): ?><tr><td><?=e($r['asset_name'])?></td><td><?=e($r['category_name']??'-')?></td><td><?=e($r['status'])?></td><td><?=e($r['purchase_date'])?></td><td><?=e($r['warranty_date'])?></td><td><?=number_format($r['price'],2)?></td><td><?=number_format($r['est'],2)?></td><?php if($admin): ?><td><?=e($r['owner'])?></td><?php endif ?></tr><?php endforeach ?></table></div>
<?php include 'includes/footer.php'; ?>
