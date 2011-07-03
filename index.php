<pre>
<?php

require_once('curl.php');
require_once('RedmineAPI.php');

$redmine = new RedmineAPI();
$redmine->site = 'https://www.hostedredmine.com/';
$redmine->key = '156c78abc53683b327b464799fd078712d089673';
$redmine->request_format='xml';

echo '<h3>The Projects</h3>';

$result = $redmine->getProjects();
foreach($result as $k => $v){
	$count = $count + 1;
	echo $count.'. '.$v['name'].'<br />';
}

echo '<hr />';
echo '<h3>Project</h3>';

$project = $redmine->getProject('james');
echo 'Project Name:'.$project['name'].'<br />';
echo 'Project Description: '.$project['description'].'<br />';

echo '<hr />';
echo '<h3>Project Issues</h3>';
$projectissues = $redmine->getIssues(array(
	'project_id' => 'james'
));

$count = 0;
foreach($projectissues as $k => $v){
	$count = $count + 1;
	echo $count.'. Subject: '.$v['subject'].'<br />';
	echo '&nbsp;&nbsp;Description: '.$v['description'].'<br /><br />';
}

echo '<hr />';
echo '<h3>Project Issues Assigned To Me</h3>';
$myissues = $redmine->getMyIssues();

$count = 0;
foreach($myissues as $k => $v){
	$count = $count + 1;
	echo $count.'. Subject: '.$v['subject'].'<br />';
	echo '&nbsp;&nbsp;Description: '.$v['description'].'<br /><br />';
}

echo '<hr />';
echo '<h3>Get Issue</h3>';
$getissues = $redmine->getIssue(22114);

$count = 0;
foreach($getissues as $k => $v){
	$count = $count + 1;
	echo $count.'. Subject: '.$v['subject'].'<br />';
	echo '&nbsp;&nbsp;Description: '.$v['description'].'<br /><br />';
}




?>
</pre>
