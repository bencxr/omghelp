<div class="categories view">
<h2><?php  echo __('Category'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($category['Category']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Lft'); ?></dt>
		<dd>
			<?php echo h($category['Category']['lft']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Rght'); ?></dt>
		<dd>
			<?php echo h($category['Category']['rght']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Parentcategory'); ?></dt>
		<dd>
			<?php echo $this->Html->link($category['Parentcategory']['name'], array('controller' => 'categories', 'action' => 'view', $category['Parentcategory']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($category['Category']['name']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Category'), array('action' => 'edit', $category['Category']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Category'), array('action' => 'delete', $category['Category']['id']), null, __('Are you sure you want to delete # %s?', $category['Category']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Categories'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Category'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Categories'), array('controller' => 'categories', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parentcategory'), array('controller' => 'categories', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Conversations'), array('controller' => 'conversations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Conversation'), array('controller' => 'conversations', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Categories'); ?></h3>
	<?php if (!empty($category['Childcategory'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Lft'); ?></th>
		<th><?php echo __('Rght'); ?></th>
		<th><?php echo __('Parent Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($category['Childcategory'] as $childcategory): ?>
		<tr>
			<td><?php echo $childcategory['id']; ?></td>
			<td><?php echo $childcategory['lft']; ?></td>
			<td><?php echo $childcategory['rght']; ?></td>
			<td><?php echo $childcategory['parent_id']; ?></td>
			<td><?php echo $childcategory['name']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'categories', 'action' => 'view', $childcategory['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'categories', 'action' => 'edit', $childcategory['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'categories', 'action' => 'delete', $childcategory['id']), null, __('Are you sure you want to delete # %s?', $childcategory['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Childcategory'), array('controller' => 'categories', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Conversations'); ?></h3>
	<?php if (!empty($category['Conversation'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('SessionID'); ?></th>
		<th><?php echo __('Helper Id'); ?></th>
		<th><?php echo __('Category Id'); ?></th>
		<th><?php echo __('ImageEnabled'); ?></th>
		<th><?php echo __('ImagePhotoURL'); ?></th>
		<th><?php echo __('ImageOverlayData'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($category['Conversation'] as $conversation): ?>
		<tr>
			<td><?php echo $conversation['id']; ?></td>
			<td><?php echo $conversation['sessionID']; ?></td>
			<td><?php echo $conversation['helper_id']; ?></td>
			<td><?php echo $conversation['category_id']; ?></td>
			<td><?php echo $conversation['imageEnabled']; ?></td>
			<td><?php echo $conversation['imagePhotoURL']; ?></td>
			<td><?php echo $conversation['imageOverlayData']; ?></td>
			<td><?php echo $conversation['created']; ?></td>
			<td><?php echo $conversation['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'conversations', 'action' => 'view', $conversation['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'conversations', 'action' => 'edit', $conversation['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'conversations', 'action' => 'delete', $conversation['id']), null, __('Are you sure you want to delete # %s?', $conversation['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Conversation'), array('controller' => 'conversations', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
