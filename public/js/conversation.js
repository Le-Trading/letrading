$(document).ready(function(){
    const tmpl = $('#admin_conversation_messages').data('prototype').replace(/__name__/g, 0);
    $('#admin_conversation_messages').append(tmpl);
});
$('#add-user').click(function () {
    console.log("ah");
    const index = +$('#widgets-counter').val();
    const tmpl = $('#admin_conversation_participants').data('prototype').replace(/__name__/g, index);
    $('#admin_conversation_participants').append(tmpl);
    $('#widgets-counter').val(index + 1);
    handleDeleteButtons();
});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        $(target).remove();
    })
}

function updateCounter() {
    const count = +$('#admin_conversation_participants div.form-group').length;
    $('#widgets-counter').val(count);
}

updateCounter();
handleDeleteButtons();
