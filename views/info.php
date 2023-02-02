<?php

use Cryptographp\View;

/**
 * @var View $this
 * @var string $version
 * @var array<array{state:string,label:string,stateLabel:string}> $checks
 */

?>

<h1>Cryptographp <?=$this->esc($version)?></h1>
<h2><?=$this->text('syscheck_title')?></h2>
<ul class="cryptographp_syscheck">
<?php foreach ($checks as $check):?>
  <li class="xh_<?=$this->esc($check['state'])?>"><?=$this->text('syscheck_message', $check['label'], $check['stateLabel'])?></li>
<?php endforeach?>
</ul>
