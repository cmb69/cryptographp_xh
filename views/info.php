<h1>Cryptographp <?=$this->version()?></h1>
<h2><?=$this->text('syscheck_title')?></h2>
<ul class="cryptographp_syscheck">
<?php foreach ($this->checks as $check):?>
    <li class="xh_<?=$this->escape($check->state)?>"><?=$this->text('syscheck_message', $check->label, $check->stateLabel)?></li>
<?php endforeach?>
</ul>
