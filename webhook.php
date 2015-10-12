<?php
// Set Variables
$LOCAL_REPO_ROOT    = "/var/www/html";
$REMOTE_REPO        = "https://github.com/q0821/esportshop.git";
$BRANCH             = "master";

if ( $_POST['payload'] ) {
  // Only respond to POST requests from Github
  // If there is already a repo, just run a git pull to grab the latest changes
  shell_exec("cd {$LOCAL_REPO_ROOT} && git pull");
  var_dump($_POST['payload']);
  die("done " . mktime());
}
// test
//shell_exec("cd {$LOCAL_REPO_ROOT} && git pull");
?>
