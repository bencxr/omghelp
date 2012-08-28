<div class="conversations index">
	<h2><?php echo __('Conversations'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('sessionID'); ?></th>
			<th><?php echo $this->Paginator->sort('helper_id'); ?></th>
			<th><?php echo $this->Paginator->sort('category_id'); ?></th>
			<th><?php echo $this->Paginator->sort('completed'); ?></th>
			<th><?php echo $this->Paginator->sort('imageEnabled'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($conversations as $conversation): ?>
	<tr>
		<td><?php echo h($conversation['Conversation']['id']); ?>&nbsp;</td>
		<td><?php echo h($conversation['Conversation']['sessionID']); ?>&nbsp;</td>
		<td><?php echo h($conversation['Conversation']['helper_id']); ?>&nbsp;</td>
		<td><?php echo h($conversation['Conversation']['category_id']); ?>&nbsp;</td>
		<td><?php echo h($conversation['Conversation']['completed']); ?>&nbsp;</td>
		<td><?php echo h($conversation['Conversation']['imageEnabled']); ?>&nbsp;</td>
		<td><?php echo h($conversation['Conversation']['created']); ?>&nbsp;</td>
		<td><?php echo h($conversation['Conversation']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $conversation['Conversation']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $conversation['Conversation']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $conversation['Conversation']['id']), null, __('Are you sure you want to delete # %s?', $conversation['Conversation']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Conversation'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Helper'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Categories'), array('controller' => 'categories', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Category'), array('controller' => 'categories', 'action' => 'add')); ?> </li>
	</ul>
</div>
