<?php

use Plib\View;

/**
 * @var View $this
 * @var string $js
 * @var string $imageUrl
 * @var string $audioUrl
 * @var string $audioImage
 * @var string $reloadImage
 * @var string $nonce
 */
?>
<!-- cryptographp captcha -->
<script type="module" src="<?=$this->esc($js)?>"></script>
<div class="cryptographp">
  <img class="cryptographp_image" src="<?=$this->esc($imageUrl)?>" alt="<?=$this->text('alt_image')?>">
  <a class="cryptographp_audio" href="<?=$this->esc($audioUrl)?>">
    <img src="<?=$this->esc($audioImage)?>" alt="<?=$this->text('alt_audio')?>" title="<?=$this->text('alt_audio')?>">
  </a>
  <span class="cryptographp_reload_container">
    <!--
      <a class="cryptographp_reload" data-image="<?=$this->esc($imageUrl)?>" data-audio="<?=$this->esc($audioUrl)?>">
        <img src="<?=$this->esc($reloadImage)?>" alt="<?=$this->text('alt_reload')?>" title="<?=$this->text('alt_reload')?>">
      </a>
    -->
  </span>
  <div><?=$this->text('message_enter_code')?></div>
  <input type="hidden" name="cryptographp_nonce" value="<?=$this->esc($nonce)?>">
  <input type="text" name="cryptographp-captcha">
</div>
