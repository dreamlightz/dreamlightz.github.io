$(document).ready(function(){
    geosat.Swipe('body', 'left', function(){
        if( $('#mobile-menu-offcanvas').css('transform') !== 'matrix(1, 0, 0, 1, 0, 0)' ){ return false; }
        $('body').removeClass('offcanvas-open');
        $('#mobile-menu-offcanvas').removeClass('offcanvas-open');
    });
    $(document).on("contextmenu", "video", function(){ return false; });
    // $('#mobile-menu-offcanvas a, .main-menu a').on('click', function(e){
    //     e.preventDefault();
    //     let _url = $(this).attr('href');
    //     $.ajax({
    //         url: _url,
    //         method: "POST",
    //         data: { id : '123' },
    //         dataType: "html"
    //     }).done(function( msg ) {
    //         $('main').html( msg );
    //     }).fail(function( jqXHR, textStatus ) {
    //         alert( "Request failed: " + textStatus );
    //     });
    // });
    $('img').lazyload();
});