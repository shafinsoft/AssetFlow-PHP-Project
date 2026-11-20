<?php if(!empty($shell)): ?></main><footer class="app-foot">&copy; <?=date('Y')?> AssetFlow</footer></div>
<?php else: ?></main><footer class="foot"><div class="foot-in"><div><b>AssetFlow</b><p>Know what you own, where it is, and what shape it's in.</p></div>
<div><b>Explore</b><a href="index.php">Home</a><a href="index.php#features">Features</a><a href="index.php#faq">Questions</a></div>
<div><b>Account</b><?php if(logged()): ?><a href="dashboard.php">Dashboard</a><a href="assets.php">Assets</a><?php else: ?><a href="login.php">Login</a><a href="signup.php">Sign up</a><?php endif ?></div></div>
<div class="foot-bot">&copy; <?=date('Y')?> AssetFlow. Built with PHP and MySQL.</div></footer><?php endif ?>
<script src="public/js/app.js"></script></body></html>
