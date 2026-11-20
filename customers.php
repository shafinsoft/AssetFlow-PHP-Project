<?php require 'includes/auth.php'; requireAdmin();
if($_SERVER['REQUEST_METHOD']==='POST'){checkCsrf();$id=(int)($_POST['id']??0);
 foreach($conn->query('SELECT image FROM assets WHERE user_id='.$id." AND EXISTS(SELECT 1 FROM users WHERE id=$id AND role='customer')") as $im)delImage($im['image']);
 $s=$conn->prepare("DELETE FROM users WHERE id=? AND role='customer'");$s->bind_param('i',$id);$s->execute();
 if($s->affected_rows)logAct($conn,'customer_delete','#'.$id);flash($s->affected_rows?'Customer deleted successfully':'Customer not found','ok');header('Location: customers.php');exit;}
$rows=$conn->query("SELECT u.*,(SELECT COUNT(*) FROM assets WHERE user_id=u.id) n FROM users u WHERE role='customer' ORDER BY u.id DESC");
$title='Customers';include 'includes/header.php'; ?>
<header class="pagehead"><div><h1>Customers</h1><p>Everyone who has signed up.</p></div><img src="public/images/products/car.svg" alt=""></header><div class="tw"><table><tr><th>#</th><th>Name</th><th>Email</th><th>Assets</th><th>Joined</th><th>Action</th></tr>
<?php if(!$rows->num_rows): ?><tr><td colspan="6"><div class="empty"><img src="public/images/products/chair.svg" alt=""><p>No customers yet.</p></div></td></tr><?php endif ?>
<?php foreach($rows as $r): ?><tr><td><?=$r['id']?></td><td><?=e($r['name'])?></td><td><?=e($r['email'])?></td><td><?=$r['n']?></td><td><?=e($r['created_at'])?></td>
<td><form method="post" data-confirm="Delete this customer and all of their assets? This cannot be undone."><?=csrfField()?><input type="hidden" name="id" value="<?=$r['id']?>"><button class="red">Delete</button></form></td></tr><?php endforeach ?></table></div>
<?php include 'includes/footer.php'; ?>
