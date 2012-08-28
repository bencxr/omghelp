<div class="conversations form">
<?php echo $this->Form->create('Conversation'); ?>
	<fieldset>
		<legend><?php echo __('Edit Conversation'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('sessionID');
		echo $this->Form->input('clientID');
		echo $this->Form->input('helper_id');
		echo $this->Form->input('category_id');
		echo $this->Form->input('completed');
		echo $this->Form->input('imageEnabled');
		echo $this->Form->input('imagePhotoURL');
		echo $this->Form->input('imageOverlayData');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Conversation.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Conversation.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Conversations'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Helper'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Categories'), array('controller' => 'categories', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Category'), array('controller' => 'categories', 'action' => 'add')); ?> </li>
	</ul>
</div>
