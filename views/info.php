<?php

use Plib\View;

/**
 * @var View $this
 * @var string $version
 * @var array<array{class:string,key:string,arg:string,statekey:string}> $checks
 */
?>
<!-- cryptographp plugin info -->
<h1>Cryptographp <?=$this->esc($version)?></h1>
<h2><?=$this->text('syscheck_title')?></h2>
<ul class="cryptographp_syscheck">
<?foreach ($checks as $check):?>
  <li class="<?=$this->esc($check['class'])?>"><?=$this->text($check['key'], $check['arg'])?><?=$this->text($check['statekey'])?></li>
<?endforeach?>
</ul>
