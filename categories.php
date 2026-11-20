<?php require 'includes/auth.php'; requireAdmin();
if($_SERVER['REQUEST_METHOD']==='POST'){checkCsrf();$id=(int)($_POST['id']??0);$act=$_POST['action']??'save';
 try{
  if($act==='delete'){$s=$conn->prepare('DELETE FROM categories WHERE id=?');$s->bind_param('i',$id);$s->execute();logAct($conn,'category_delete','#'.$id);flash('Category deleted successfully');}
  else{$n=trim($_POST['category_name']??'');$d=trim($_POST['description']??'');
   if($n===''){flash('Category name is required','err');}
   elseif($id){$s=$conn->prepare('UPDATE categories SET category_name=?,description=? WHERE id=?');$s->bind_param('ssi',$n,$d,$id);$s->execute();logAct($conn,'category_update',$n);flash('Category updated successfully');}
   else{$s=$conn->prepare('INSERT INTO categories(category_name,description) VALUES(?,?)');$s->bind_param('ss',$n,$d);$s->execute();logAct($conn,'category_add',$n);flash('Category added successfully');}}
 }catch(mysqli_sql_exception $x){flash('Category name already exists','err');}
 header('Location: categories.php');exit;}
$edit=null;if(isset($_GET['edit'])){$s=$conn->prepare('SELECT * FROM categories WHERE id=?');$eid=(int)$_GET['edit'];$s->bind_param('i',$eid);$s->execute();$edit=$s->get_result()->fetch_assoc();}
$rows=$conn->query('SELECT c.*,(SELECT COUNT(*) FROM assets WHERE category_id=c.id) n FROM categories c ORDER BY c.id');
$title='Categories';include 'includes/header.php'; ?>
<header class="pagehead"><div><h1>Category Management</h1><p>Group assets so they are easy to find.</p></div><img src="public/images/products/chair.svg" alt=""></header>
<form class="box wide" method="post" id="categoryForm"><h3><?=$edit?'Edit':'Add'?> Category</h3><?=csrfField()?><input type="hidden" name="id" value="<?=e($edit['id']??'')?>">
<label>Category Name *</label><input name="category_name" id="category_name" value="<?=e($edit['category_name']??'')?>">
<label>Description</label><input name="description" id="description" value="<?=e($edit['description']??'')?>"><br><br>
<button type="submit" id="saveCategory"><?=$edit?'Update':'Add'?></button> <?php if($edit): ?><a class="btn gray" href="categories.php">Cancel</a><?php endif ?></form>
<div class="tw"><table id="categoryTable"><tr><th>#</th><th>Name</th><th>Description</th><th>Assets</th><th>Actions</th></tr>
<?php foreach($rows as $r): ?><tr><td><?=$r['id']?></td><td><?=e($r['category_name'])?></td><td><?=e($r['description'])?></td><td><?=$r['n']?></td>
<td><a class="btn" href="categories.php?edit=<?=$r['id']?>">Edit</a>
<form method="post" data-confirm="Delete this category? Its assets will become uncategorized."><?=csrfField()?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="red">Delete</button></form></td></tr><?php endforeach ?></table></div>
<?php include 'includes/footer.php'; ?>
