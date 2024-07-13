$(document).ready(function() {
    $('.category-select').select2({
        ajax: {
            url: function(params) {
                return '/fetch-categories?name=' + params.term;
            },
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data
                };
            },
            minimumInputLength: 1
        },
        maximumSelectionLength: 3,
        placeholder: "Select up to 3 items",
    });
});


$('.category-select').on('change', function(){
    var categories = $('.category-select').val();
    PostCreate.categories = categories;

    console.log(PostCreate);
});
