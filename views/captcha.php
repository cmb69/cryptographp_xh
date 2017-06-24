<div class="cryptographp">
    <img class="cryptographp_image" src="<?=$this->imageUrl()?>" alt="<?=$this->text('alt_image')?>">
    <a class="cryptographp_audio" href="<?=$this->audioUrl()?>">
        <img src="<?=$this->audioImage()?>" alt="<?=$this->text('alt_audio')?>" title="<?=$this->text('alt_audio')?>">
    </a>
    <span class="cryptographp_reload_container">
        <!--
            <a class="cryptographp_reload" data-image="<?=$this->imageUrl()?>" data-audio="<?=$this->audioUrl()?>">
                <img src="<?=$this->reloadImage()?>" alt="<?=$this->text('alt_reload')?>" title="<?=$this->text('alt_reload')?>">
            </a>
        -->
    </span>
    <div><?=$this->text('message_enter_code')?></div>
    <input type="text" name="cryptographp-captcha">
</div>
