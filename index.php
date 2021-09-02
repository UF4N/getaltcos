<?php
//phpinfo();
$rootdir = $_SERVER['DOCUMENT_ROOT'];
ini_set('include_path', "$rootdir/class");
require_once('repo.php');
?>

<html>
<head>
<title>Links</title>
<script>
function markAfter(input) {
  if (!input.checked) return;
  var inputs = document.getElementsByTagName('input');
  var n;
  for (n=0; n < inputs.length && inputs[n].value != input.value; n+=1);
  for (n=n+1; n < inputs.length; n+=1) {
    if (inputs[n].type='checkbox') {
      inputs[n].checked = true;
    }
  }
  
}
</script>
</head>
<body>

<h1>TEST</h1>

<?php
$archs = repos::listArchs();
// echo "<pre>ARCHS=" . print_r($archs, 1) . "</pre>\n";

foreach ($archs as $arch) {
  $streams = repos::listStreams($arch);
  //echo "<pre>STREAMS=" . print_r($streams, 1) . "</pre>\n";
  foreach ($streams as $stream) {
?>
<ul><h2>Поток: <?= $stream?></h2>
<?php
    foreach (repos::repoTypes() as $repoType) {
      $repo = new repo("acos/$arch/$stream", $repoType);
?>
  <li>
    <ul><h3>Тип репозитория: <?= $repoType?></h3>
<?php
      $refs = $repo->getRefs();
      foreach ($refs  as $ref) {
?>
    <li>
      <ul><h3>REF: <?= $ref?></h3>
        <li><a href='/v1/graph/?stream=<?= $stream?>&basearch=x86_64&repoType=<?= $repoType?>' target='graphREST'><?= $repoType?>-граф</a></li>
	<li>Коммиты:
	  <form action='/ostree/deleteCommits/' target='ostreeREST'>
	  <input type='hidden' name='ref' value='<?= $ref?>' />
	  <input type='hidden' name='repoType' value='<?= $repoType?>' />
       	  <ul>
<?php
        $commits = $repo->getCommits($ref);
        // echo "<pre>COMMITS=" . print_r($commits, 1) . "</pre>\n";
	$nCommits = count($commits);
	if ($nCommits  <= 0) continue;
	$commitIds = array_keys($commits);
	$lastCommitId = $commitIds[$nCommits-1];
	$lastVersion = $commits[$lastCommitId]['Version'];
        //echo "<pre>nCommits=$nCommits lastCommitId=$lastCommitId</pre>\n";
        foreach ($commits as $commitId=>$commit) {
          $version = $commit['Version'];
	  $date = $commit['Date'];
	  $parent = @$commit['Parent'];
?>
		<li>
                    <input type='checkbox' value='<?= $commitId?>' name='ids[]' onClick='markAfter(this)' />
		  <ul>ID: <?= $commitId?><br>Версия: <?= $version?><br>Дата создания: <?= $date?>
<?php
	  if ($parent) { ?><br>Parent: <?= $parent?> <?php } 
?>
		    <li><a href='/ostree/fsck/?ref=<?= $ref?>&repoType=<?= $repoType?>&commitId=<?= $commitId?>' target=ostreeREST>Проверка целостности</a>
                    <li><a href='/ostree/ls/?ref=<?= $ref?>&repoType=<?= $repoType?>&commitId=<?= $commitId?>' target=ostreeREST>Содержание</a>
		  </ul>
            	</li>
<?php 
        }
?>	  	<button type='submit'>Удалить отмеченные коммиты</button>
		</form>	
	      </ul>
	      <li><a href='/ostree/update/?ref=<?= $ref?>&commitId=<?= $commitId?>' target=ostreeREST>Обновить <?= $repoType?>-ветку <?= $ref?> версии <?= $lastVersion?></a></li>
          </ul>
<?php 	  
      }
?>
        </ul>
    <?php
    }
    ?>
  </ul>
<?php
  }
}



