<?php

use Cryptographp\Infra\View;

/**
 * @var View $this
 * @var string $version
 * @var array<array{state:string,label:string,stateLabel:string}> $checks
 */
?>
<!-- cryptographp plugin info -->
<h1>Cryptographp <?=$version?></h1>
<h2><?=$this->text('syscheck_title')?></h2>
<ul class="cryptographp_syscheck">
<?foreach ($checks as $check):?>
  <li class="xh_<?=$check['state']?>"><?=$this->text('syscheck_message', $check['label'], $check['stateLabel'])?></li>
<?endforeach?>
</ul>
