<h1>Cryptographp</h1>
<img src="<?=$this->logo()?>" class="cryptographp_logo" alt="<?=$this->text('alt_logo')?>">
<p>Version: <?=$this->version()?></p>
<p>
    Copyright &copy; 2006-2007 Sylvain Brison<br>
    Copyright &copy; 2011-2021 <a href="http://3-magi.net/">Christoph M. Becker</a>
</p>
<p class="cryptographp_license">
    Cryptographp_XH is free software: you can redistribute it and/or modify it
    under the terms of the GNU General Public License as published by the Free
    Software Foundation, either version 3 of the License, or (at your option)
    any later version.
</p>
<p class="cryptographp_license">
    Cryptographp_XH is distributed in the hope that it will be useful, but
    <em>without any warranty</em>; without even the implied warranty of
    <em>merchantability</em> or <em>fitness for a particular purpose</em>. See
    the GNU General Public License for more details.
</p>
<p class="cryptographp_license">
    You should have received a copy of the GNU General Public License along with
    Cryptographp_XH. If not, see <a href="http://www.gnu.org/licenses/"
    target="_blank">http://www.gnu.org/licenses/</a>.
</p>
<h2><?=$this->text('syscheck_title')?></h2>
<ul class="cryptographp_syscheck">
<?php foreach ($this->checks as $check):?>
    <li class="xh_<?=$this->escape($check->state)?>"><?=$this->text('syscheck_message', $check->label, $check->stateLabel)?></li>
<?php endforeach?>
</ul>
