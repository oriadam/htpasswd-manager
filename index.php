<?php 
/* CONFIG */
define('HTPASSWD_ABSOLUTE_PATH',realpath('path/to/.htpasswd'));
error_reporting(E_ALL);
?><html>
	<head>
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdn.jsdelivr.net/bootswatch/3.3.7/paper/bootstrap.min.css" rel="stylesheet">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<style>
			.btn { text-transform: none; }
			form { display: inline; }
			.u { margin-top: 20px;}
			.debugger { display:none; }
			h1 { font-size:2em;}
			h2 { font-size:1.5em;}
			h3 { font-size:1.1em;}
			.u_name { width: 150px; font-weight:bold; }
			span,input,input.form-control { display: inline-block; }
			.add_box { width: 250px; }
			input[name] { width:120px; margin:0 10px; }
			ul {
				margin:0;
				list-style-type: none;
				padding:0 10px;
			}
			li:before { 
				content: "-- "; 
				font-weight:bold;
			}
		</style>
	</head>
<body>
<?php
include_once 'htpasswd.php';
$filename = HTPASSWD_ABSOLUTE_PATH;
$htpasswd = new htpasswd($filename); // path to your .htpasswd file

$dbg = array();
$result = array();
if (!empty($_POST['a']) && !empty($_POST['u'])) {
	$dbg[] = 'POST=' . json_encode($_POST);
	$user = $_POST['u'];
	switch ($_POST['a']) {
	case 'add':
		if ($htpasswd->user_add($user, $_POST['p'])) {
			$result[] = '<div class="text-success">User added</div>';
		} else {
			$result[] = '<div class="text-danger">Failed to add user</div>';
		}

		break;

	case 'del':
		if ($htpasswd->user_delete($user)) {
			$result[] = '<div class="text-success">User removed</div>';
		} else {
			$result[] = '<div class="text-danger">Failed to remove user</div>';
		}

		break;

	case 'pw':
		if ($htpasswd->user_update($user, $_POST['p'])) {
			$result[] = '<div class="text-success">User password updated</div>';
		} else {
			$result[] = '<div class="text-danger">Failed to change password</div>';
		}

		break;
	}
}

// List of users
$string = file_get_contents($filename);
$dbg[] = "$filename
$string";
$rows = explode("\n", $string);
$users = array();
foreach ($rows as $row) {
	if (!empty($row)) {
		$parts = explode(':', $row);
		$dbg[] = json_encode($parts);

		if (!empty($parts[0])) {
			$users[] = $parts[0];
		}

	}
}

echo "<div class='well'><h1>User management</h1>";
echo "<h2>New User</h2>";
echo frm('', 'add', "Add user", "<span class='add_box'>User name: <input class='form-control' name='u'></span><span class='add_box'>Password: &nbsp; <input class='form-control' name='p'></span>");
echo "<h2>Existing Users</h2><ul>";

foreach ($users as $user) {
	echo "<li class='u'><span class='u_name'>$user</span>";
	echo frm($user, 'pw', 'Update password', "<input class='form-control' name='p' placeholder='Change pass'>");
	echo frm($user, 'del', "Remove");
	echo "</li>
	";
}

echo "</ul></div>";

if (count($result)) {
	echo "<br><br><div class='well'><h2>Actions</h2>" . implode("<br>", $result) . '</div>';
}

if (count($dbg)) {
	echo "<br><br><div class='well debugger'><small><h3>debug info</h3><pre>" . implode("\n", $dbg) . '</pre></small></div>';
}

function frm($user, $action, $submit_text, $content = '') {
	$html = '<form method="post">';
	if ($user) {
		$html .= "<input type='hidden' value='$user' name='u'>";
	}

	if ($action) {
		$html .= "<input type='hidden' value='$action' name='a'>";
	}

	if ($content) {
		$html .= $content;
	}

	if ($submit_text) {
		$html .= "<input class='btn btn-sm' type='submit' value='$submit_text'>";
	}

	$html .= '</form>';
	return $html;
}

?>

<script>
	$('[name="p"]').on('focus',function(ev){
		var elem = ev.target;
		if (!elem.value){
			var pw_chars = '23456789abcdefghjkpqrstuvwxyz';
			var s = '';
			var len = 6;
			while (len>0){
				var c = pw_chars[~~(Math.random()*pw_chars.length)];
				pw_chars.replace(c,'');
				s += c + c;
				len -= 2;
			}
			elem.value = s;
			$(elem).select();
		}
	});
</script>
