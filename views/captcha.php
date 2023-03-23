<?php

use Cryptographp\Infra\View;

/**
 * @var View $this
 * @var string $imageUrl
 * @var string $audioUrl
 * @var string $audioImage
 * @var string $reloadImage
 * @var string $nonce
 */
?>
<!-- cryptographp captcha -->
<div class="cryptographp">
  <img class="cryptographp_image" src="<?=$imageUrl?>" alt="<?=$this->text('alt_image')?>">
  <a class="cryptographp_audio" href="<?=$audioUrl?>">
    <img src="<?=$audioImage?>" alt="<?=$this->text('alt_audio')?>" title="<?=$this->text('alt_audio')?>">
  </a>
  <span class="cryptographp_reload_container">
    <!--
      <a class="cryptographp_reload" data-image="<?=$imageUrl?>" data-audio="<?=$audioUrl?>">
        <img src="<?=$reloadImage?>" alt="<?=$this->text('alt_reload')?>" title="<?=$this->text('alt_reload')?>">
      </a>
    -->
  </span>
  <div><?=$this->text('message_enter_code')?></div>
  <input type="hidden" name="cryptographp_nonce" value="<?=$nonce?>">
  <input type="text" name="cryptographp-captcha">
</div>
