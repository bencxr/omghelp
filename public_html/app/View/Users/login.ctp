<?php 
    echo $this->Html->css(array('login'), 'stylesheet', array('inline' => false));
?>
<div id="login_content">
    <?php
        echo $this->Html->image('icon.png', array('alt' => 'omgHelp!!', 'width' => 300, 'height' => 300));    
    ?>
    <br>
    <span id="auth_window">
        <div class="users form">
        <?php echo $this->Html->image('shadeup.jpg', array("style" => "width:100%"));?>
        <?php echo $this->Session->flash('auth'); ?>
        <?php echo $this->Form->create('User', array('action' => 'login')); ?>
            <fieldset>
            <?php
        echo $this->Form->input('username');
                echo $this->Form->input('password');
            ?>
            </fieldset>
        <?php 
            echo $this->Form->end(__('Login'));
        ?>
        </div>
        Not yet a user? <?php echo $this->Html->link('Register', array('controller' => 'Users', 'action' => "add")); ?> now!
    <br style="clear:both">
</div>
