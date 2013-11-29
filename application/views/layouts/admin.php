<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">

	<title>Admin</title>
	<?=HTML::style('bootstrap/css/bootstrap.min.css')?>
	<?=HTML::style('bootstrap/css/bootstrap-responsive.min.css')?>
	<style>
		body {
			padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
		}
		input[type="text"] {
			height: auto;
		}
	</style>
</head>
<body>
	<?=View::factory('layouts/admin_head')?>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="/">yarmarka.biz</a>
				<div class="nav-collapse collapse navbar-inverse-collapse">
					<ul class="nav">
						<?php if (Auth::instance()->have_access_to('user')) : ?>
							<li class="dropdown <?=($module_name == 'user') ? 'active' : ''?>">
								<a href="#"  class="dropdown-toggle" data-toggle="dropdown">Users <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?=Url::site('khbackend/users/index')?>">List</a></li>
								</ul>
							</li>
						<?php endif; ?>

						<?php if (Auth::instance()->have_access_to('articles')) : ?>
							<li class="dropdown <?=($module_name == 'articles') ? 'active' : ''?>">
								<a href="#"  class="dropdown-toggle" data-toggle="dropdown">Articles <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?=Url::site('khbackend/articles/index')?>">List</a></li>
									<li><a href="<?=Url::site('khbackend/articles/add')?>">Add</a></li>
								</ul>
							</li>
						<?php endif; ?>

						<?php if (Auth::instance()->have_access_to('phones')) : ?>
							<li class="dropdown <?=($module_name == 'phones') ? 'active' : ''?>">
								<a href="#"  class="dropdown-toggle" data-toggle="dropdown">Phones <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?=Url::site('khbackend/phones/index')?>">List</a></li>
								</ul>
							</li>
						<?php endif; ?>

						<?php if (Auth::instance()->have_access_to('object')) : ?>
						<li class="dropdown <?=($module_name == 'object') ? 'active' : ''?>">
							<a href="#"  class="dropdown-toggle" data-toggle="dropdown">Objects <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?=Url::site('khbackend/objects/index')?>">List</a></li>
							</ul>
						</li>
						<?php endif; ?>
						
						<?php if (Auth::instance()->have_access_to('reports')) : ?>
						<li class="dropdown <?=($module_name == 'reports') ? 'active' : ''?>">
							<a href="#"  class="dropdown-toggle" data-toggle="dropdown">Reports <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?=Url::site('khbackend/reports/operstat')?>">Operators stat</a></li>
							</ul>
						</li>
						<?php endif; ?>						

						<?php if (Auth::instance()->get_user()) : ?>
							<li><a href="<?=Url::site('khbackend/welcome/logout')?>">Log Out</a></li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div id="loading" style="position:absolute; z-index:99999; background-color:red; color: white; top: 10; left: 5; display:none">
			Loading...
		</div>
		<?=$_content?>
	</div>

	<?php 
	if (Kohana::$profiling === TRUE) 
	{
		echo View::factory('profiler/stats');
	}
	?>
</body>
</html>
