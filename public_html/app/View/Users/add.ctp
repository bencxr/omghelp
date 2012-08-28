<!-- app/View/Users/add.ctp -->
<?php echo $this->Html->css(array('login'), 'stylesheet', array('inline' => false)); ?>
<div id="login_content">
    <?php echo $this->Html->image('icon.png', array('alt' => 'omgHelp!!', 'width' => 300, 'height' => 300));?>
<div class="users form">
         <?php echo $this->Html->image('shadeup.jpg', array("style" => "width:100%"));?>
   <?php echo $this->Form->create('User'); ?>
        <fieldset>
        <?php
            echo $this->Form->input('username');
            echo $this->Form->input('password');
            echo $this->Form->input('fullname');
            echo $this->Form->hidden('role', array('value' => 'helper'));
        ?>
        </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
    </div>
</div>
