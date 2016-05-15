<?php /**
        Author: SpringHack - springhack@live.cn
        Last modified: 2016-05-15 21:06:18
        Filename: status.php
        Description: Created by SpringHack using vim automatically.
**/ ?>
<?php
    require_once('api.php');
	$db = new MySQL();
	if (isset($_GET['cid']))
	{
		$res = $db->from('Contest')->where("`id`='".intval($_GET['cid'])."'")->select()->fetch_one();
		if (!$res)
			die('<center><h1><a href="index.php" style="color: #000000;">No such contest !</a></h1></center>');
		@session_start();
		if (!empty($res['password']))
		{
			if (!isset($_SESSION['contest_'.intval($_GET['cid'])]))
			{
				header('Location: password.php?cid='.intval($_GET['cid']));
				die();
			} else {
				if ($res['password'] != $_SESSION['contest_'.intval($_GET['cid'])])
				{
					header('Location: password.php?cid='.intval($_GET['cid']));
					die();
				}
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Status List</title>
    </head>
    <body>
    	<center>
        <?php require_once("header.php"); ?>
        <h1>Problem Status List</h1>
    	<table border="1">
        	<tr>
            	<td>
                	Run ID
                </td>
                <td>
                	User
                </td>
                <td>
                	Problem ID
                </td>
                <td>
                	Result
                </td>
                <td>
                	Memory
                </td>
                <td>
                	Time
                </td>
                <td>
                	Language
                </td>
                <td>
                	Submit Time
                </td>
            </tr>
            <?php
				$start = isset($_GET['page'])?(intval($_GET['page'])-1)*10:0;
				require_once('classes/Record.php');
				$is_contest = false;
				if (isset($_GET['cid']))
				{
					$arr = $db->from('Record')->where("`contest`='".intval($_GET['cid'])."'")->limit(10, $start)->order('DESC', 'time')->select('id')->fetch_all();
					$contest_fix = $db->from('Contest')->where("`id`='".intval($_GET['cid'])."'")->select('list')->fetch_one();
					if ($contest_fix)
					{
						$is_contest = true;
						$tmp_arr = explode(',', $contest_fix['list']);
						$hash = array();
						for ($i=0;$i<count($tmp_arr);++$i)
							$hash[$tmp_arr[$i]] = chr(65 + $i);
					}
				} else
					$arr = $db->from('Record')->where("`contest`='0'")->limit(10, $start)->order('DESC', 'time')->select('id')->fetch_all();
				for ($i=0;$i<count($arr);++$i)
				{
					$pro = new Record($arr[$i]['id']);
					$res = $pro->getInfo();
					?>
            <tr>
                <td>
                	<?php echo $res['id']; ?>
                </td>
                <td>
                	<?php echo $res['user']; ?>
                </td>
                <td>
                	<?php
						if($is_contest)
							echo $hash[$res['oid']];
						else
							echo $res['oid'];
					?>
                </td>
                <td>
                	<?php echo $res['result']; ?>
                </td>
                <td>
                	<?php echo $res['memory']; ?>
                </td>
                <td>
                	<?php echo $res['long']; ?>
                </td>
                <td>
                	<?php echo $res['lang']; ?>
                </td>
                <td>
                	<?php echo date("Y-M-D H:i:s", $res['time']); ?>
                </td>
            </tr>
                    <?php
				}
			?>
        </table>
        <br /><br />
		<script language="javascript" src="Widget/pageSwitcher/pageSwitcher.js"></script>
		<br /><br />
        </center>
    </body>
</html>
