<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">

	<title>Admin</title>
	<?=HTML::style('bootstrap/css/bootstrap.min.css')?>
	<?=HTML::style('bootstrap/css/bootstrap-responsive.min.css')?>
	 <style>
      input[type="text"] {
        height: auto;
      }
    </style>
</head>
<body>
<?=View::factory('layouts/admin_head')?>
<div class="container">
	<div id="loading" style="position:absolute; z-index:99999; background-color:red; color: white; top: 10; left: 5; display:none">
	  Loading...
	</div>
	<?=$_content?>
</div>
</body>
</html>
