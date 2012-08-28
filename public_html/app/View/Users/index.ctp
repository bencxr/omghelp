<?php echo $this->Html->css(array('dashboard'), 'stylesheet', array('inline' => false));
?>

<div id = "profile_edit">
    <span id="helpCount">You helped <?= $completedConversations ?> people!</span>
    <br/>
     <div id = "userinfo_edit">
<?php
    echo $this->Form->create('User', array('action' => 'edit'));
    echo $this->Form->hidden('id', array('value' => $user['id']));
    echo $this->Form->input('fullname', array('value' => $user['fullname']));
    echo $this->Form->input('HelpsCategories', array(
        'label' => __('My Categories',true),
        'type' => 'select',
        'multiple' => 'checkbox',
        'options' => $helpcategories,
    )); 
    echo $this->Form->end('Update');
?>
    </div>
</div>
<div id = "help">
    <h2>Help Dashboard</h2>
    <div id="unanswered"></div>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>
    setInterval(function() {
        yay();
}, 2000);
    window.onload = function() {
        var message = document.getElementById('flashMessage');
        if (message != null) {
            var profile = document.getElementById('profile_edit');
            profile.style.top = profile.offsetTop + message.clientHeight + 40 + 'px';
        }
    }
    function yay() {
    $.ajax({
        url: "http://www.omghelp.net/conversations/listUnanswered",
            context: document.body
    }).done(function(done) { 
        document.getElementById('unanswered').innerHTML = done; 
    });
    }
    yay();
</script>
</div>
