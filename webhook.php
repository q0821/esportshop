<?php
// Set Variables
$LOCAL_REPO_ROOT    = "/var/www/html";
$REMOTE_REPO        = "https://github.com/q0821/esportshop.git";
$BRANCH             = "master";

if ( $_POST['payload'] ) {
  // Only respond to POST requests from Github
  // If there is already a repo, just run a git pull to grab the latest changes
  shell_exec("cd {$LOCAL_REPO_ROOT} && git pull origin master");
  var_dump($_POST['payload']);
  die("done " . mktime());
}
//shell_exec("cd {$LOCAL_REPO_ROOT} && git pull");
?>
