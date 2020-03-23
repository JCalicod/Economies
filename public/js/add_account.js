$('.add-account').click(() => {
    $('.add-account').css('display', 'none');
    $('.add-account-opened').css('display', 'block');
});

$('.minus').click(() => {
    $('.add-account-opened').css('display', 'none');
    $('.add-account').css('display', 'block');
});