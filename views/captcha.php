<?php

use Cryptographp\Url;
use Cryptographp\View;

/**
 * @var View $this
 * @var Url $imageUrl
 * @var Url $audioUrl
 * @var string $audioImage
 * @var string $reloadImage
 */

?>

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
    <input type="text" name="cryptographp-captcha">
</div>
