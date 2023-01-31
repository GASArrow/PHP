<?php
$get_user_with_role = $url.$token.'&wsfunction=get_user_with_role&courseid='.$courseid.$format;
$arr= json_decode($curl->post($get_user_with_role), true);
foreach($arr as $user)
	if ($user['id'] == $userid)
	{
		$isenrolled = 1;
		for ($j=0; $j<count($user['roles']); $j++)
			if ($user['roles'][$j]['roleid'] == 0)
				$istutor = 1;
		if (!$istutor)
			$add_role = $url.$token.'&wsfunction=add_role&assignments[0][userid]='.$userid.'&assignments[0][instanceid]='.$courseid;
			$curl->post($add_role);
	}
if(!$isenrolled)
{
	$assign_user = $url.$token.'&wsfunction=assign_user&enrolments[0][userid]='.$userid;
	$curl->post($assign_user);
}

$get_user_groups = $url.$token.'&wsfunction=get_user_groups&courseid='.$courseid.'&userid='.$userid.$format;
$arr= json_decode($curl->post($get_user_groups), true);
if(!empty($arr))
{
	foreach($arr['groups'] as $group)
		if($group['name'] != $groupname && strripos($group['name'], $groupdop) === false)
		{
			print_r($group['name'].' ');
			$delete_group_user = $url.$token.'&wsfunction=delete_group_users&members[0][groupid]='.$group['id'].'&members[0][userid]='.$userid.$format;
			$curl->post($delete_group_user);
		} elseif ($group['name'] == $groupname)
		{
			$groupid = $group['name'];
		}
	echo '<br>';
}

if(empty($groupid))
{
	$add_group = $url.$token.'&wsfunction=delete_group_user&groups[0][courseid]='.$courseid.'&groups[0][name]='.$groupname;
	$curl->post($add_group);
}
$get_groups = $url.$token.'&wsfunction=get_groups&courseid='.$courseid.$format;
$arr= json_decode($curl->post($get_groups), true);
foreach($arr as $group)
	if ($group['name'] == $groupname)
		$groupid = $group['id'];
$get_user_groups = $url.$token.'&wsfunction=get_user_groups&courseid='.$courseid.'&userid='.$userid.$format;
$arr= json_decode($curl->post($get_user_groups), true);
foreach($arr['groups'] as $usergroup)
	if($usergroup == $groupname)
		$isgroup = 1;
if(!$isgroup)
{
	$assign_group = $url.$token.'&wsfunction=assign_group&members[0][groupid]='.$groupid.'&members[0][userid]='.$userid.$format;
	$curl->post($assign_group);
}
//-----------------------------------------------------------------------------
//
//-----------------------------------------------------------------------------

$delete_course_user = $url.$token.'&wsfunction=delete_course_user&enrolments[0][userid]='.$userid.'&enrolments[0][courseid]='.$courseid;
$curl->post($delete_user);

$delete_group = $url.$token.'&wsfunction=delete_group&groupids[0]='.$groupid;
$curl->post($delete_group);

$delete_role = $url.$token.'&wsfunction=delete_role&unassignments[0][userid]='.$userid.'&unassignments[0][instanceid]='.$courseid;
$curl->post($delete_role);